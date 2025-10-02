<?php

namespace App\Livewire\TariffList;

use Livewire\Component;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Support\Number;
use App\Models\Operation\TariffList;
use App\Models\Operation\ExpenseRange;
use App\Models\Operation\Service;
use Exception;

class TariffListEdit extends Component
{
    public $show = false;
    public $tariffListId;
    public $tariffModel;
    public $services;
    public $selectedServices = [];
    public $effectivity_date;
    public $ranges = [];
    public $isEffective = false;

    protected $listeners = [
        'openEditModal' => 'openEditModal',
        'addRangeAt' => 'addRangeAt',
        'removeRange' => 'removeRange',
        'syncSelectedServices' => 'syncSelectedServices',
    ];

    protected $rules = [
        'effectivity_date' => 'required|date',
        'selectedServices' => 'required|array|min:1',
        'ranges.*.exp_range_min' => 'required|numeric|min:0|',
        'ranges.*.exp_range_max' => 'required|numeric|min:0|gte:ranges.*.exp_range_min',
        'ranges.*.coverage_percent' => 'required|numeric|min:0|max:100',
    ];

    protected $messages = [
        'ranges.*.exp_range_max.gte' => 'The maximum value must be greater than or equal to the minimum value.',
        'ranges.*.exp_range_min.numeric' => 'The value must be a number.',
        'ranges.*.exp_range_max.numeric' => 'The value must be a number.',
        'ranges.*.coverage_percent.numeric' => 'The coverage percent must be a number between 0 and 100.',
    ];

    public function mount()
    {
        $this->services = Service::all();
    }

    public function openEditModal($tariffListId = null)
    {
        if (empty($tariffListId)) {
            session()->flash('warning', 'No tariff list id provided.');
            return;
        }
        $this->resetValidation();
        $this->tariffListId = $tariffListId;
        $this->tariffModel = TariffList::where('tariff_list_id', $tariffListId)->firstOrFail();
        $this->effectivity_date = Carbon::parse($this->tariffModel->effectivity_date)->toDateString();
        $this->isEffective = $this->tariffModel->effectivity_status === 'Active';
        $existingRanges = ExpenseRange::where('tariff_list_id', $tariffListId)->orderBy('exp_range_id')->get();
        $this->ranges = [];
        $this->selectedServices = [];
        foreach ($this->services as $service) {
            $serviceRanges = $existingRanges->where('service_id', $service->service_id);
            if ($serviceRanges->count() > 0) {
                $this->selectedServices[] = $service->service_id;
                foreach ($serviceRanges as $serviceRange) {
                    $this->ranges[] = [
                        'exp_range_id' => $serviceRange->exp_range_id,
                        'service_id' => $serviceRange->service_id,
                        'exp_range_min' => $this->formatNumber((string) $serviceRange->exp_range_min),
                        'exp_range_max' => $this->formatNumber((string) $serviceRange->exp_range_max),
                        'coverage_percent' => (string) $serviceRange->coverage_percent,
                    ];
                }
            }
        }
        $this->show = true;
        $this->dispatch('tariff-edit-opened');
    }

    public function formatNumber($value)
    {
        $clean = Str::of((string) $value)->trim();
        $clean = Str::replace(',', '', $clean);
        $float = (float) $clean;
        return Number::format($float, 0);
    }

    private function sanitizeNumericString(string $value): string
    {
        $value = Str::of((string) $value)->trim();
        $value = Str::replace(',', '', $value);
        $allowed = '0123456789.-';
        $len = strlen($value);
        $out = '';
        $dotSeen = false;
        for ($i = 0; $i < $len; $i++) {
            $ch = $value[$i];
            if ($ch === '.') {
                if ($dotSeen) {
                    continue;
                }
                $dotSeen = true;
                $out .= '.';
                continue;
            }
            if ($ch === '-' && $out === '') {
                $out .= '-';
                continue;
            }
            if (Str::contains($allowed, $ch) !== false && $ch !== '.' && $ch !== '-') {
                $out .= $ch;
            }
        }
        return $out === '' ? '0' : $out;
    }

    private function parseToNumeric($value, $decimals = 0)
    {
        $clean = Str::of((string) $value)->trim();
        $clean = Str::replace(',', '', $clean);
        $clean = Str::of($clean)->replaceMatches('/[^\d\.\-]/', '')->toString();
        if ($clean === '' || $clean === '-' || $clean === '.') {
            return 0;
        }
        $num = (float) $clean;
        return $decimals >= 0 ? Number::round($num, $decimals) : $num;
    }

    public function updatedRanges($value, $key)
    {
        $parts = Str::of($key)->explode('.');
        if (collect($parts)->count() === 2) {
            $index = $parts[0];
            $field = $parts[1];
            $raw = $value;
            if ($field === 'coverage_percent') {
                $san = Str::of((string) $raw)->replaceMatches('/\D/', '')->toString();
                $san = $san === '' ? '0' : $san;
                $this->ranges[$index][$field] = (string) (int) $san;
            } else {
                $san = Str::replace(',', '', (string) $raw);
                $san = Str::of((string) $san)->replaceMatches('/[^\d\.\-]/', '')->toString();
                if ($san === '' || $san === '-' || $san === '.') {
                    $san = '0';
                }
                $numeric = (int) Number::floor((float) $san);
                $this->ranges[$index][$field] = $this->formatNumber((string) $numeric);
            }
            $this->validateOnly("ranges.{$index}.{$field}");
        }
    }

    public function addRangeAt($index, $serviceId)
    {
        $newRange = [
            'exp_range_id' => '',
            'service_id' => $serviceId,
            'exp_range_min' => '0',
            'exp_range_max' => '0',
            'coverage_percent' => '0',
        ];
        $rangesCollection = collect($this->ranges);
        $rangesCollection->splice($index + 1, 0, [$newRange]);
        $this->ranges = $rangesCollection->values()->all();
        $this->dispatch('tariff-ranges-updated');
    }

    public function removeRange($index)
    {
        if (! isset($this->ranges[$index])) {
            return;
        }
        $rangesCollection = collect($this->ranges);
        $rangesCollection->splice($index, 1);
        $this->ranges = $rangesCollection->values()->all();
        $this->dispatch('tariff-ranges-updated');
    }

    public function syncSelectedServices($selectedServices)
    {
        $this->selectedServices = $selectedServices;
    }

    private function sanitizeRangesForValidation()
    {
        foreach ($this->ranges as $i => $range) {
            $minRaw = $range['exp_range_min'] ?? '0';
            $maxRaw = $range['exp_range_max'] ?? '0';
            $coverageRaw = $range['coverage_percent'] ?? '0';
            $minNum = (int) Str::of((string) $minRaw)->replace(',', '')->toString();
            $maxNum = (int) Str::of((string) $maxRaw)->replace(',', '')->toString();
            $coverageNum = (int) Str::of((string) $coverageRaw)->replaceMatches('/\D/', '')->toString();
            $this->ranges[$i]['exp_range_min'] = (string) $minNum;
            $this->ranges[$i]['exp_range_max'] = (string) $maxNum;
            $this->ranges[$i]['coverage_percent'] = (string) $coverageNum;
        }
    }

    public function save()
    {
        $this->sanitizeRangesForValidation();
        $this->validate();

        // Check for overlaps and duplicates
        $rangesByService = [];
        foreach ($this->ranges as $i => $range) {
            if (!collect($this->selectedServices)->contains($range['service_id'])) {
                continue;
            }
            $min = (int) Str::of((string) ($range['exp_range_min'] ?? '0'))->replace(',', '')->toString();
            $max = (int) Str::of((string) ($range['exp_range_max'] ?? '0'))->replace(',', '')->toString();

            // Group ranges by service
            $rangesByService[$range['service_id']][] = [
                'index' => $i,
                'min' => $min,
                'max' => $max,
            ];
        }

        foreach ($rangesByService as $serviceId => $ranges) {
            // Sort ranges by min value
            usort($ranges, fn($a, $b) => $a['min'] <=> $b['min']);

            $seen = [];
            foreach ($ranges as $idx => $current) {
                // Check for duplicate min/max
                $key = "{$current['min']}-{$current['max']}";
                if (in_array($key, $seen)) {
                    session()->flash('error', "Duplicate range values found for service ID {$serviceId} (min: {$current['min']}, max: {$current['max']}).");
                    Log::warning("Duplicate range values found", [
                        'service_id' => $serviceId,
                        'min' => $current['min'],
                        'max' => $current['max'],
                    ]);
                    return;
                }
                $seen[] = $key;

                // Check for overlap with previous range
                if ($idx > 0) {
                    $prev = $ranges[$idx - 1];
                    if ($current['min'] <= $prev['max']) {
                        session()->flash('error', "Overlapping ranges detected for service ID {$serviceId} between min: {$prev['min']}-max: {$prev['max']} and min: {$current['min']}-max: {$current['max']}.");
                        return;
                    }
                }
            }
        }

        if (empty($this->tariffListId)) {
            session()->flash('warning', 'Tariff list not selected.');
            return;
        }
        try {
            DB::transaction(function () {
                $tariff_list_id = $this->tariffListId;
                $tariffList = TariffList::where('tariff_list_id', $tariff_list_id)->firstOrFail();
                $existingRangeIds = ExpenseRange::where('tariff_list_id', $tariff_list_id)->pluck('exp_range_id')->toArray();
                $newRangeIds = [];
                foreach ($this->ranges as $range) {
                    if (! collect($this->selectedServices)->contains($range['service_id'])) {
                        continue;
                    }
                    $min = (int) Str::of((string) ($range['exp_range_min'] ?? '0'))->replace(',', '')->toString();
                    $max = (int) Str::of((string) ($range['exp_range_max'] ?? '0'))->replace(',', '')->toString();
                    $coverage = (int) Str::of((string) ($range['coverage_percent'] ?? '0'))->replaceMatches('/\D/', '')->toString();
                    $expRangeId = $range['exp_range_id'] ?? null;
                    if ($expRangeId) {
                        $newRangeIds[] = $expRangeId;
                        ExpenseRange::where('exp_range_id', $expRangeId)->update([
                            'exp_range_min' => $min,
                            'exp_range_max' => $max,
                            'coverage_percent' => $coverage,
                        ]);
                    } else {
                        $now = Carbon::now();
                        $baseExp = "EXP-RANGE-{$now->year}";
                        $latestExp = ExpenseRange::where('exp_range_id', 'like', "{$baseExp}%")->latest('exp_range_id')->first();
                        $lastExp = $latestExp ? (int) Str::afterLast($latestExp->exp_range_id, '-') : 0;
                        $nextNumExp = Str::padLeft($lastExp + 1, 9, '0');
                        $newExpRangeId = "{$baseExp}-{$nextNumExp}";
                        $created = ExpenseRange::create([
                            'exp_range_id' => $newExpRangeId,
                            'tariff_list_id' => $tariff_list_id,
                            'service_id' => $range['service_id'],
                            'exp_range_min' => $min,
                            'exp_range_max' => $max,
                            'coverage_percent' => $coverage,
                        ]);
                        $newRangeIds[] = $created->exp_range_id;
                    }
                }
                $rangesToDelete = collect($existingRangeIds)->diff($newRangeIds)->all();
                if (! empty($rangesToDelete)) {
                    ExpenseRange::whereIn('exp_range_id', $rangesToDelete)->delete();
                }
                $servicesList = Service::whereIn('service_id', $this->selectedServices)->pluck('service_type')->implode(', ');
                $tariffList->update([
                    'effectivity_date' => $this->effectivity_date,
                    'service_types_involved' => $servicesList,
                    'effectivity_status' => 'Active'
                ]);
            });
            $this->dispatch('refreshTariffTable');
            $this->show = false;
            $this->dispatch('tariff-edit-saved');
            session()->flash('success', 'Tariff list has been updated successfully.');
        } catch (ModelNotFoundException $e) {
            session()->flash('warning', 'Tariff list not found.');
        } catch (Exception $e) {
            Log::error('Error saving tariff edit', [
                'tariff_list_id' => $this->tariffListId,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'An error occurred while updating the tariff list.');
        }
    }

    public function render()
    {
        return view('livewire.tariff-list.tariff-list-edit', [
            'services' => $this->services,
            'tariffModel' => $this->tariffModel,
            'tariffLists' => collect(),
        ]);
    }

    public function closeModal()
    {
        $this->show = false;
        $this->dispatch('tariff-edit-closed');
    }
}
