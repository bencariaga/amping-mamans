<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Storage\Data;
use App\Models\Operation\Service;
use App\Models\Operation\TariffList;
use App\Models\Operation\ExpenseRange;

class TariffListController extends Controller
{
    public function showTariffListVersions()
    {
        $tariffLists = TariffList::with('data')->select('data_id', DB::raw('MAX(effectivity_date) as latest_date'))->groupBy('data_id')->orderBy('latest_date', 'desc')->get();
        $groupedTariffs = [];
        $tariffModels = [];

        foreach ($tariffLists as $list) {
            $tariffModel = TariffList::where('data_id', $list->data_id)->orderBy('effectivity_date', 'desc')->first();
            $tariffModels[$list->data_id] = $tariffModel;
            $services = ExpenseRange::where('tariff_list_id', $tariffModel->tariff_list_id)->join('services', 'expense_ranges.service_id', '=', 'services.service_id')->pluck('services.service_type')->unique();
            $groupedTariffs[$list->data_id] = $services;
        }

        $groupedTariffs = collect($groupedTariffs)->reverse()->all();
        return view('pages.dashboard.landing.tariff-list-versions', ['groupedTariffs' => $groupedTariffs, 'tariffModels' => $tariffModels]);
    }

    public function showTariffListTable(string $tariff_list_id)
    {
        $tariffModel = TariffList::where('tariff_list_id', $tariff_list_id)->firstOrFail();
        $dataId = $tariffModel->data_id;
        $tariffDisplayName = $tariff_list_id;
        $services = Service::all();
        $expenseRanges = ExpenseRange::where('tariff_list_id', $tariff_list_id)->join('services', 'expense_ranges.service_id', '=', 'services.service_id')->select('expense_ranges.*', 'services.service_type')->get();
        $tariffLists = $expenseRanges->groupBy('service_type');
        $allTariffLists = TariffList::orderBy('effectivity_date', 'desc')->orderBy('tariff_list_id', 'desc')->get();
        $latestEffectiveTariffList = $allTariffLists->first();

        return view('pages.dashboard.budget-updates.tariff-list.tariff-list-tables', [
            'data_id' => $dataId,
            'tariffDisplayName' => $tariffDisplayName,
            'services' => $services,
            'tariffLists' => $tariffLists,
            'allTariffLists' => $allTariffLists,
            'latestEffectiveTariffListId' => $latestEffectiveTariffList ? $latestEffectiveTariffList->tariff_list_id : null,
            'tariffModel' => $tariffModel
        ]);
    }

    public function create()
    {
        $services = Service::all();
        $tariffLists = Collection::make();

        foreach ($services as $service) {
            $tariffLists->put($service->service_type, Collection::make());
        }

        return view('pages.dashboard.budget-updates.tariff-list.tariff-list-create', ['services' => $services, 'tariffLists' => $tariffLists]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'effectivity_date' => 'required|date',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,service_id',
            'apply_version' => 'boolean',
            'range_min.*' => 'nullable|numeric|min:0',
            'range_max.*' => 'nullable|numeric|min:0',
            'tariff_amount.*' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $year = Carbon::now()->year;
            $base = "DATA-{$year}";
            $latestData = Data::where('data_id', 'like', "{$base}%")->latest('data_id')->first();
            $last = $latestData ? (int) Str::afterLast($latestData->data_id, '-') : 0;
            $nextNumData = Str::padLeft((string) ($last + 1), 9, '0');
            $newDataId = "{$base}-{$nextNumData}";

            Data::create(['data_id' => $newDataId, 'data_status' => 'Unarchived', 'created_at' => now(), 'updated_at' => now()]);

            $baseTariff = "TARIFF-LIST-{$year}";
            $latestTariffList = TariffList::where('tariff_list_id', 'like', "{$baseTariff}%")->latest('tariff_list_id')->first();
            $lastTariff = $latestTariffList ? (int) Str::afterLast($latestTariffList->tariff_list_id, '-') : 0;
            $nextNumTariff = Str::padLeft((string) ($lastTariff + 1), 3, '0');
            $newTariffListId = "{$baseTariff}-{$nextNumTariff}";

            $tariffList = TariffList::create([
                'tariff_list_id' => $newTariffListId,
                'data_id' => $newDataId,
                'effectivity_date' => $request->effectivity_date,
                'effectivity_status' => $request->has('apply_version') && $request->apply_version ? 'Effective' : 'Not Currently Used'
            ]);

            foreach ($request->services as $serviceId) {
                $rangeMins = $request->input("range_min_{$serviceId}");
                $rangeMaxs = $request->input("range_max_{$serviceId}");
                $tariffAmounts = $request->input("tariff_amount_{$serviceId}");

                if (!empty($rangeMins) && is_array($rangeMins)) {
                    foreach ($rangeMins as $index => $min) {
                        $max = $rangeMaxs[$index] ?? 0.00;
                        $amount = $tariffAmounts[$index] ?? 0.00;
                        $baseExp = "EXP-RANGE-{$year}";
                        $latestExpRange = ExpenseRange::where('exp_range_id', 'like', "{$baseExp}%")->latest('exp_range_id')->first();
                        $lastExp = $latestExpRange ? (int) Str::afterLast($latestExpRange->exp_range_id, '-') : 0;
                        $nextNumExp = Str::padLeft((string) ($lastExp + 1), 9, '0');
                        $newExpRangeId = "{$baseExp}-{$nextNumExp}";

                        ExpenseRange::create([
                            'exp_range_id' => $newExpRangeId,
                            'tariff_list_id' => $tariffList->tariff_list_id,
                            'service_id' => $serviceId,
                            'exp_range_min' => (float) Str::of($min)->replace(',', '')->toString(),
                            'exp_range_max' => (float) Str::of($max)->replace(',', '')->toString(),
                            'assist_amount' => (float) Str::of($amount)->replace(',', '')->toString()
                        ]);
                    }
                } else {
                    $baseExp = "EXP-RANGE-{$year}";
                    $latestExpRange = ExpenseRange::where('exp_range_id', 'like', "{$baseExp}%")->latest('exp_range_id')->first();
                    $lastExp = $latestExpRange ? (int) Str::afterLast($latestExpRange->exp_range_id, '-') : 0;
                    $nextNumExp = Str::padLeft((string) ($lastExp + 1), 9, '0');
                    $newExpRangeId = "{$baseExp}-{$nextNumExp}";

                    ExpenseRange::create([
                        'exp_range_id' => $newExpRangeId,
                        'tariff_list_id' => $tariffList->tariff_list_id,
                        'service_id' => $serviceId,
                        'exp_range_min' => 0.00,
                        'exp_range_max' => 0.00,
                        'assist_amount' => 0.00
                    ]);
                }
            }
        });

        return redirect()->route('tariff-lists.versions.show')->with('success', 'New tariff list version created successfully.');
    }

    public function updateTariffLists(Request $r)
    {
        $validated = $r->validate([
            'tariff_list_id' => 'required|exists:tariff_lists,tariff_list_id',
            'effectivity_date' => 'required|date',
            'apply_version' => 'sometimes|accepted',
            'tariff_amount.*' => 'nullable|numeric|min:0',
            'range_min.*' => 'nullable|numeric|min:0',
            'range_max.*' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($r, $validated) {
            $tariffListId = $validated['tariff_list_id'];
            $selectedServices = $r->input('services', []);
            $existingExpIds = ExpenseRange::where('tariff_list_id', $tariffListId)->pluck('exp_range_id')->toArray();
            $submittedExpIds = collect($r->input('tariff_amount', []))->keys()->all();
            $toDelete = collect($existingExpIds)->diff($submittedExpIds)->values()->all();

            if (!empty($toDelete)) {
                ExpenseRange::whereIn('exp_range_id', $toDelete)->delete();
            }

            foreach ($r->input('tariff_amount', []) as $expRangeId => $amt) {
                $clean_amt = (float) Str::replace(',', '', $amt);
                $clean_min = (float) Str::replace(',', '', $r->input('range_min')[$expRangeId] ?? 0);
                $clean_max = (float) Str::replace(',', '', $r->input('range_max')[$expRangeId] ?? 0);

                if (ExpenseRange::where('exp_range_id', $expRangeId)->exists()) {
                    ExpenseRange::where('exp_range_id', $expRangeId)->update([
                        'assist_amount' => $clean_amt,
                        'exp_range_min' => $clean_min,
                        'exp_range_max' => $clean_max
                    ]);
                }
            }

            $year = Carbon::now()->year;
            $baseExp = "EXP-RANGE-{$year}";

            foreach ($selectedServices as $serviceId) {
                $newMins = $r->input("range_min_new.{$serviceId}", $r->input("range_min_new[{$serviceId}]", []));
                $newMaxs = $r->input("range_max_new.{$serviceId}", $r->input("range_max_new[{$serviceId}]", []));
                $newAmounts = $r->input("tariff_amount_new.{$serviceId}", $r->input("tariff_amount_new[{$serviceId}]", []));

                if (!empty($newAmounts) && is_array($newAmounts)) {
                    foreach ($newAmounts as $index => $amount) {
                        $min = $newMins[$index] ?? 0;
                        $max = $newMaxs[$index] ?? 0;
                        $amt = $amount ?? 0;
                        $latestExpRange = ExpenseRange::where('exp_range_id', 'like', "{$baseExp}%")->latest('exp_range_id')->first();
                        $lastExp = $latestExpRange ? (int) Str::afterLast($latestExpRange->exp_range_id, '-') : 0;
                        $nextNumExp = Str::padLeft((string) ($lastExp + 1), 9, '0');
                        $newExpRangeId = "{$baseExp}-{$nextNumExp}";

                        ExpenseRange::create([
                            'exp_range_id' => $newExpRangeId,
                            'tariff_list_id' => $tariffListId,
                            'service_id' => $serviceId,
                            'exp_range_min' => (float) Str::replace(',', '', $min),
                            'exp_range_max' => (float) Str::replace(',', '', $max),
                            'assist_amount' => (float) Str::replace(',', '', $amt)
                        ]);
                    }
                }
            }

            $allExistingServices = ExpenseRange::where('tariff_list_id', $tariffListId)->pluck('service_id')->unique()->toArray();
            $toRemoveServices = collect($allExistingServices)->diff($selectedServices)->values()->all();

            if (!empty($toRemoveServices)) {
                ExpenseRange::where('tariff_list_id', $tariffListId)->whereIn('service_id', $toRemoveServices)->delete();
            }

            $tariffList = TariffList::where('tariff_list_id', $tariffListId)->firstOrFail();

            $tariffList->update([
                'effectivity_date' => $validated['effectivity_date'],
                'effectivity_status' => $r->has('apply_version') ? 'Effective' : 'Not Currently Used'
            ]);
        });

        return redirect()->back()->with('success', 'Tariff lists have been updated successfully.');
    }

    public function showTariffLists()
    {
        return redirect()->route('tariff-lists.versions.show');
    }

    public function destroy(string $tariff_list_id)
    {
        $tariffList = TariffList::where('tariff_list_id', $tariff_list_id)->firstOrFail();
        $dataId = $tariffList->data_id;
        $versionsCount = TariffList::where('data_id', $dataId)->count();

        DB::beginTransaction();

        try {
            ExpenseRange::where('tariff_list_id', $tariff_list_id)->delete();
            $tariffList->delete();

            if ($versionsCount === 1) {
                Data::where('data_id', $dataId)->delete();
            }

            DB::commit();
            return redirect()->route('tariff-lists.versions.show')->with('success', 'Tariff list version has been deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete tariff list version: ' . $e->getMessage());
        }
    }

    public function getLatestTariffListVersion(): string
    {
        $latestTariffList = TariffList::where('effectivity_status', 'Effective')->orderBy('effectivity_date', 'desc')->orderBy('tariff_list_id', 'desc')->first();
        return $latestTariffList ? $latestTariffList->tariff_list_id : 'N/A';
    }

    public function getTariffListPreview(string $tariff_list_id)
    {
        $tariffModel = TariffList::where('tariff_list_id', $tariff_list_id)->firstOrFail();
        $dataId = $tariffModel->data_id;
        $tariffDisplayName = $tariff_list_id;
        $services = Service::all();
        $expenseRanges = ExpenseRange::where('tariff_list_id', $tariff_list_id)->join('services', 'expense_ranges.service_id', '=', 'services.service_id')->select('expense_ranges.*', 'services.service_type')->get();
        $tariffLists = $expenseRanges->groupBy('service_type');
        $allTariffLists = TariffList::orderBy('effectivity_date', 'desc')->orderBy('tariff_list_id', 'desc')->get();
        $latestEffectiveTariffList = $allTariffLists->first();

        return view('pages.dashboard.budget-updates.tariff-list.tariff-list-tables', [
            'data_id' => $dataId,
            'tariffDisplayName' => $tariffDisplayName,
            'services' => $services,
            'tariffLists' => $tariffLists,
            'allTariffLists' => $allTariffLists,
            'latestEffectiveTariffListId' => $latestEffectiveTariffList ? $latestEffectiveTariffList->tariff_list_id : null,
            'tariffModel' => $tariffModel
        ]);
    }
}
