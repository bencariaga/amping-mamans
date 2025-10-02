<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Support\Number;
use App\Models\Storage\Data;
use App\Models\Operation\Service;
use App\Models\Operation\TariffList;
use App\Models\Operation\ExpenseRange;
use Exception;

class TariffListController extends Controller
{
    public function showTariffLists()
    {
        return redirect()->route('tariff-lists.rows.show');
    }

    public function showTariffListVersions()
    {
        $tariffListsQuery = TariffList::with('data')->select('data_id', DB::raw('MAX(effectivity_date) as latest_date'))->groupBy('data_id')->orderBy('latest_date', 'desc')->get();
        $groupedTariffs = [];
        $tariffModels = [];
        foreach ($tariffListsQuery as $list) {
            $tariffModel = TariffList::where('data_id', $list->data_id)->orderBy('effectivity_date', 'desc')->orderBy('tariff_list_id', 'desc')->first();
            $tariffModels[$list->data_id] = $tariffModel;
            $servicesList = ExpenseRange::where('tariff_list_id', $tariffModel->tariff_list_id)->join('services', 'expense_ranges.service_id', '=', 'services.service_id')->pluck('services.service_type')->unique();
            $groupedTariffs[$list->data_id] = $servicesList;
        }
        $groupedTariffs = collect($groupedTariffs)->reverse()->all();
        $services = Service::all();
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $baseTariff = "TL-{$year}-{$month}";
        $latestTariff = TariffList::where('tariff_list_id', 'like', "{$baseTariff}%")->latest('tariff_list_id')->first();
        $lastNumTariff = $latestTariff ? (int) Str::afterLast($latestTariff->tariff_list_id, '-') : 0;
        $nextNumberPreview = $lastNumTariff + 1;
        return view('pages.dashboard.landing.tariff-lists', [
            'groupedTariffs' => $groupedTariffs,
            'tariffModels' => $tariffModels,
            'services' => $services,
            'previewBase' => $baseTariff,
            'previewNextNumber' => $nextNumberPreview,
        ]);
    }

    public function getLatestTariffListVersion(): array
    {
        $services = Service::all();
        $serviceTariffs = [];
        foreach ($services as $service) {
            $tariffIds = ExpenseRange::where('service_id', $service->service_id)->pluck('tariff_list_id')->unique()->toArray();
            if (empty($tariffIds)) {
                $serviceTariffs[$service->service_type] = 'N/A';
                continue;
            }
            $latestTariff = TariffList::whereIn('tariff_list_id', $tariffIds)->orderBy('effectivity_date', 'desc')->orderBy('tariff_list_id', 'desc')->first();
            $serviceTariffs[$service->service_type] = $latestTariff ? $latestTariff->tariff_list_id : 'N/A';
        }
        return $serviceTariffs;
    }

    private function generateNextSequentialId(string $modelClass, string $column, string $basePrefix, int $padLength = 9): string
    {
        $latest = $modelClass::where($column, 'like', "{$basePrefix}%")->orderBy($column, 'desc')->first();
        if (!$latest) {
            $next = 1;
        } else {
            $lastNum = (int) Str::afterLast($latest->{$column}, '-');
            $next = $lastNum + 1;
        }
        $nextPadded = $padLength > 0 ? Str::padLeft($next, $padLength, '0') : (string) $next;
        return "{$basePrefix}-{$nextPadded}";
    }

    private function normalizeNumberString(string $raw): string
    {
        $s = Str::of((string) $raw)->trim();
        $s = Str::replace(',', '', $s);
        if ($s === '' || $s === '-') {
            return '0';
        }
        $s = Str::of($s)->replaceMatches('/[^\d\.\-]/', '')->toString();
        $s = (int) Number::floor((float) $s);
        return (string) $s;
    }

    private function validateRangesStructure(array $rangesByService)
    {
        foreach ($rangesByService as $serviceId => $ranges) {
            $normalized = [];
            foreach ($ranges as $r) {
                $minRaw = isset($r['exp_range_min']) ? (string) $r['exp_range_min'] : '';
                $maxRaw = isset($r['exp_range_max']) ? (string) $r['exp_range_max'] : '';
                $coverageRaw = isset($r['coverage_percent']) ? (string) $r['coverage_percent'] : '';
                $minSan = $this->normalizeNumberString($minRaw);
                $maxSan = $this->normalizeNumberString($maxRaw);
                $coverageSan = Str::of($coverageRaw)->replaceMatches('/\D/', '')->toString();
                $min = (float) $minSan;
                $max = (float) $maxSan;
                $coverage = $coverageSan === '' ? 0 : (int) $coverageSan;
                if ($min > $max) {
                    throw new Exception("For service {$serviceId} a range has min greater than max.");
                }
                if ($coverage < 0 || $coverage > 100) {
                    throw new Exception("For service {$serviceId} a range has coverage percent out of bounds (0-100).");
                }
                $normalized[] = ['min' => $min, 'max' => $max];
            }
            $normalized = Collection::make($normalized)->sortBy('min')->values()->all();
            $prevMax = null;
            foreach ($normalized as $seg) {
                if ($prevMax !== null && $seg['min'] <= $prevMax) {
                    throw new Exception("For service {$serviceId} ranges overlap or are contiguous/unsorted. Adjust the ranges so they are strictly increasing and non-overlapping.");
                }
                $prevMax = $seg['max'];
            }
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'effectivity_date' => ['required', 'date', Rule::unique('tariff_lists', 'effectivity_date')],
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,service_id',
            'ranges' => 'nullable|array',
        ]);
        try {
            DB::transaction(function () use ($request) {
                $now = Carbon::now();
                $year = $now->year;
                $baseData = "DATA-{$year}";
                $latestData = Data::where('data_id', 'like', "{$baseData}%")->latest('data_id')->first();
                $lastNumData = $latestData ? (int) Str::afterLast($latestData->data_id, '-') : 0;
                $nextNumData = Str::padLeft($lastNumData + 1, 9, '0');
                $newDataId = "{$baseData}-{$nextNumData}";
                Data::create([
                    'data_id' => $newDataId,
                    'data_status' => 'Unarchived',
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                $month = Str::upper($now->format('M'));
                $baseTariff = "TL-{$year}-{$month}";
                $latestTariff = TariffList::where('tariff_list_id', 'like', "{$baseTariff}%")->latest('tariff_list_id')->first();
                $lastNumTariff = $latestTariff ? (int) Str::afterLast($latestTariff->tariff_list_id, '-') : 0;
                $nextNumTariff = $lastNumTariff + 1;
                $newTariffId = "{$baseTariff}-{$nextNumTariff}";
                $servicesList = collect($request->services)->map(function ($serviceId) {
                    $service = Service::find($serviceId);
                    return $service ? $service->service_type : null;
                })->filter()->implode(', ');
                TariffList::create([
                    'tariff_list_id' => $newTariffId,
                    'data_id' => $newDataId,
                    'effectivity_date' => $request->effectivity_date,
                    'service_types_involved' => $servicesList,
                    'effectivity_status' => ($request->effectivity_date === Carbon::now()->toDateString()) ? 'Active' : 'Inactive'
                ]);
                $rangesInput = $request->input('ranges', []);
                $rangesByService = [];
                if (!empty($rangesInput) && is_array($rangesInput)) {
                    foreach ($rangesInput as $serviceId => $ranges) {
                        $rangesByService[$serviceId] = $ranges;
                    }
                    $this->validateRangesStructure($rangesByService);
                    foreach ($rangesByService as $serviceId => $ranges) {
                        foreach ($ranges as $range) {
                            $baseExp = "EXP-RANGE-{$now->year}";
                            $newExpRangeId = $this->generateNextSequentialId(ExpenseRange::class, 'exp_range_id', $baseExp, 9);
                            $minRaw = isset($range['exp_range_min']) ? (string) $range['exp_range_min'] : '';
                            $maxRaw = isset($range['exp_range_max']) ? (string) $range['exp_range_max'] : '';
                            $coverageRaw = isset($range['coverage_percent']) ? (string) $range['coverage_percent'] : '';
                            $minSan = $this->normalizeNumberString($minRaw);
                            $maxSan = $this->normalizeNumberString($maxRaw);
                            $coverageSan = Str::of($coverageRaw)->replaceMatches('/\D/', '')->toString();
                            ExpenseRange::create([
                                'exp_range_id' => $newExpRangeId,
                                'tariff_list_id' => $newTariffId,
                                'service_id' => $serviceId,
                                'exp_range_min' => Number::round((float) $minSan, 0),
                                'exp_range_max' => Number::round((float) $maxSan, 0),
                                'coverage_percent' => $coverageSan === '' ? 0 : (int) $coverageSan
                            ]);
                        }
                    }
                } else {
                    foreach ($request->services as $serviceId) {
                        $baseExp = "EXP-RANGE-{$now->year}";
                        $newExpRangeId = $this->generateNextSequentialId(ExpenseRange::class, 'exp_range_id', $baseExp, 9);
                        ExpenseRange::create([
                            'exp_range_id' => $newExpRangeId,
                            'tariff_list_id' => $newTariffId,
                            'service_id' => $serviceId,
                            'exp_range_min' => 0,
                            'exp_range_max' => 0,
                            'coverage_percent' => 0
                        ]);
                    }
                }
            });
            return redirect()->route('tariff-lists.rows.show')->with('success', 'New tariff list version has been created successfully!');
        } catch (Exception $e) {
            Log::error('Error creating new tariff list version', [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('tariff-lists.rows.show')->with('error', 'Failed to create new tariff list version. Please try again.');
        }
    }

    public function updateTariffLists(Request $request)
    {
        $request->validate([
            'tariff_list_id' => ['required', 'string', 'exists:tariff_lists,tariff_list_id'],
            'effectivity_date' => ['required', 'date', Rule::unique('tariff_lists', 'effectivity_date')->ignore($request->input('tariff_list_id'), 'tariff_list_id')],
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,service_id',
            'ranges' => 'nullable|array',
        ]);
        $tariff_list_id = $request->input('tariff_list_id');
        try {
            DB::transaction(function () use ($request, $tariff_list_id) {
                $tariffList = TariffList::where('tariff_list_id', $tariff_list_id)->firstOrFail();
                $rangesInput = $request->input('ranges', []);
                $rangesByService = [];
                if (!empty($rangesInput)) {
                    foreach ($rangesInput as $range) {
                        $serviceId = $range['service_id'] ?? null;
                        if (!$serviceId) continue;
                        $rangesByService[$serviceId][] = $range;
                    }
                }
                if (!empty($rangesByService)) {
                    $this->validateRangesStructure($rangesByService);
                }
                $existingRangeIds = ExpenseRange::where('tariff_list_id', $tariff_list_id)->pluck('exp_range_id')->toArray();
                $newRangeIds = [];
                foreach ($request->input('services') as $serviceId) {
                    $ranges = $rangesByService[$serviceId] ?? [];
                    foreach ($ranges as $range) {
                        $minRaw = isset($range['exp_range_min']) ? (string) $range['exp_range_min'] : '';
                        $maxRaw = isset($range['exp_range_max']) ? (string) $range['exp_range_max'] : '';
                        $coverageRaw = isset($range['coverage_percent']) ? (string) $range['coverage_percent'] : '';
                        $minSan = $this->normalizeNumberString($minRaw);
                        $maxSan = $this->normalizeNumberString($maxRaw);
                        $coverageSan = Str::of($coverageRaw)->replaceMatches('/\D/', '')->toString();
                        $min = Number::round((float) $minSan, 0);
                        $max = Number::round((float) $maxSan, 0);
                        $coverage = $coverageSan === '' ? 0 : (int) $coverageSan;
                        $expRangeId = isset($range['exp_range_id']) ? $range['exp_range_id'] : null;
                        if ($expRangeId) {
                            $newRangeIds[] = $expRangeId;
                            ExpenseRange::where('exp_range_id', $expRangeId)->update([
                                'exp_range_min' => $min,
                                'exp_range_max' => $max,
                                'coverage_percent' => $coverage
                            ]);
                        } else {
                            $now = Carbon::now();
                            $baseExp = "EXP-RANGE-{$now->year}";
                            $newExpRangeId = $this->generateNextSequentialId(ExpenseRange::class, 'exp_range_id', $baseExp, 9);
                            $created = ExpenseRange::create([
                                'exp_range_id' => $newExpRangeId,
                                'tariff_list_id' => $tariff_list_id,
                                'service_id' => $serviceId,
                                'exp_range_min' => $min,
                                'exp_range_max' => $max,
                                'coverage_percent' => $coverage
                            ]);
                            $newRangeIds[] = $created->exp_range_id;
                        }
                    }
                }
                $rangesToDelete = collect($existingRangeIds)->diff($newRangeIds)->all();
                if (!empty($rangesToDelete)) {
                    ExpenseRange::whereIn('exp_range_id', $rangesToDelete)->delete();
                }
                $servicesList = Service::whereIn('service_id', $request->input('services'))->pluck('service_type')->implode(', ');
                $tariffList->update([
                    'effectivity_date' => $request->input('effectivity_date'),
                    'service_types_involved' => $servicesList
                ]);
            });
            return redirect()->route('tariff-lists.rows.show')->with('success', 'Tariff list has been updated successfully.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('tariff-lists.rows.show')->with('warning', 'Tariff list not found.');
        } catch (Exception $e) {
            Log::error('Error updating tariff list', [
                'tariff_list_id' => $tariff_list_id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('tariff-lists.rows.show')->with('error', 'An error occurred while updating the tariff list.');
        }
    }
}
