<?php

namespace App\Http\Controllers\Financial;

use App\Actions\Financial\CreateTariffList;
use App\Actions\Financial\DeleteTariffList;
use App\Actions\Financial\UpdateAllTariffStatuses;
use App\Actions\Financial\UpdateTariffList;
use App\Http\Controllers\Controller;
use App\Models\Operation\ExpenseRange;
use App\Models\Operation\Service;
use App\Models\Operation\TariffList;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TariffListController extends Controller
{
    protected $expenseRangeController;

    public function __construct(ExpenseRangeController $expenseRangeController)
    {
        $this->expenseRangeController = $expenseRangeController;
    }

    public function showTariffLists()
    {
        return redirect()->route('tariff-lists');
    }

    public function getGroupedTariffVersions(): array
    {
        $tariffListsQuery = TariffList::with('data')->select('data_id', DB::raw('MAX(effectivity_date) as latest_date'))->groupBy('data_id')->orderBy('latest_date', 'desc')->get();
        $groupedTariffs = [];
        $tariffModels = [];

        foreach ($tariffListsQuery as $list) {
            $tariffModel = TariffList::where('data_id', $list->data_id)->orderBy('effectivity_date', 'desc')->orderBy('tariff_list_id', 'desc')->first();
            $tariffModels[$list->data_id] = $tariffModel;
            $servicesList = ExpenseRange::where('tariff_list_id', $tariffModel->tariff_list_id)->join('services', 'expense_ranges.service_id', '=', 'services.service_id')->pluck('services.service_type')->unique()->toArray();
            $groupedTariffs[$list->data_id] = [
                'tariff_list_id' => $tariffModel->tariff_list_id,
                'services' => $servicesList,
            ];
        }

        return [
            'tariffModels' => $tariffModels,
            'groupedTariffs' => $groupedTariffs,
        ];
    }

    public function getServiceTariffMapping(): array
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

    public function create()
    {
        return view('pages.tariff-list.tariff-list-create');
    }

    public function getTakenDates()
    {
        $takenDates = TariffList::pluck('effectivity_date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        return response()->json(['taken_dates' => $takenDates], 200);
    }

    public function checkEffectivityDate(Request $request)
    {
        $request->validate([
            'effectivity_date' => 'required|date_format:Y-m-d',
        ]);

        $date = $request->input('effectivity_date');

        $exists = TariffList::where('effectivity_date', $date)->exists();

        return response()->json(['is_taken' => $exists], 200);
    }

    public function store(Request $request, CreateTariffList $createTariffList, UpdateAllTariffStatuses $updateAllTariffStatuses)
    {
        try {
            $request->validate([
                'effectivity_date' => [
                    'required',
                    'date_format:Y-m-d',
                    'after:today',
                    Rule::unique('tariff_lists', 'effectivity_date'),
                ],
                'selectedServices' => 'required|array|min:1',
                'selectedServices.*' => 'exists:services,service_id',
            ], [
                'selectedServices.min' => 'Please check at least one service type to include in this draft.',
                'effectivity_date.unique' => 'The selected effectivity date is already taken by another tariff list version.',
            ]);

            $createTariffList->execute($request->effectivity_date, $request->selectedServices);
            $updateAllTariffStatuses->execute();

            return response()->json(['message' => 'Tariff list version has been added.'], 200);
        } catch (Exception $e) {
            if ($e->getMessage() === 'You cannot create more than 9 tariff list versions in the same month') {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            Log::error('Failed to create tariff list', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Failed to create tariff list. Please try again.'], 500);
        }
    }

    public function show(string $tariffListId)
    {
        try {
            $tariffList = TariffList::with(['data'])->where('tariff_list_id', $tariffListId)->firstOrFail();

            $expenseRangesCollection = ExpenseRange::with('service')
                ->where('tariff_list_id', $tariffListId)
                ->get();

            $rangesByService = $expenseRangesCollection
                ->groupBy('service_id')
                ->mapWithKeys(function ($ranges, $serviceId) {
                    $service = $ranges->first()->service;
                    if (! $service) {
                        Log::warning('Orphaned ExpenseRange found', [
                            'tariff_list_id' => $ranges->first()->tariff_list_id,
                            'service_id' => $serviceId,
                        ]);

                        return [];
                    }

                    $transformedRanges = $ranges->map(function ($range) use ($service) {
                        return (object) [
                            'service_id' => $service->service_id,
                            'exp_range_id' => $range->exp_range_id,
                            'exp_range_min' => (int) $range->exp_range_min,
                            'exp_range_max' => (int) $range->exp_range_max,
                            'coverage_percent' => (int) $range->coverage_percent,
                        ];
                    })->all();

                    return [$service->service_type => $transformedRanges];
                });

            $serviceLists = $rangesByService;
            $serviceTypes = $rangesByService->keys()->all();
            $allServices = Service::all();
            $allServiceTypes = $allServices->pluck('service_type', 'service_id')->toArray();
            $usedServiceIds = $expenseRangesCollection->pluck('service_id')->unique()->toArray();

            return view('pages.tariff-list.tariff-list-view', [
                'tariffListModel' => $tariffList,
                'serviceLists' => $serviceLists,
                'serviceTypes' => $serviceTypes,
                'allServiceTypes' => $allServiceTypes,
                'usedServiceIds' => $usedServiceIds,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Tariff list version not found.'], 404);
        } catch (Exception $e) {
            Log::error('Failed to retrieve tariff list', [
                'tariff_list_id' => $tariffListId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while retrieving the tariff list.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, string $tariffListId, UpdateTariffList $updateTariffList)
    {
        Log::info('Update request data:', $request->all());

        try {
            $ranges = $this->collectAllRanges($request);

            if ($this->expenseRangeController->checkOverlap($ranges)) {
                throw ValidationException::withMessages([
                    'general' => ['One or more expense ranges overlap. Please correct them before saving.'],
                ]);
            }

            DB::beginTransaction();

            try {
                $validatedAndFormattedRanges = $this->expenseRangeController->validateAndFormatRanges($tariffListId, $ranges);

                $updateTariffList->execute($tariffListId, $validatedAndFormattedRanges);

                DB::commit();

                Log::info('Successfully updated tariff list with ranges', [
                    'tariff_list_id' => $tariffListId,
                ]);

                session()->flash('success', 'Tariff list has been updated successfully.');

                return redirect()->route('tariff-lists');
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Tariff list not found.'], 404);
        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {

            Log::error('Failed to update tariff list', ['tariff_list_id' => $tariffListId, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'message' => 'Failed to update tariff list: An unexpected error occurred. (Error: '.$e->getMessage().')',
                'errors' => null,
            ], 500);
        }
    }

    protected function collectAllRanges(Request $request): array
    {
        $ranges = [];

        $rangeMins = $request->input('range_min', []);
        $rangeMaxes = $request->input('range_max', []);
        $coveragePercents = $request->input('tariff_amount', []);

        foreach ($rangeMins as $serviceId => $expRangeMins) {
            foreach ($expRangeMins as $expRangeId => $min) {
                $ranges[] = [
                    'service_id' => $serviceId,
                    'exp_range_id' => $expRangeId,
                    'exp_range_min' => $min,
                    'exp_range_max' => $rangeMaxes[$serviceId][$expRangeId] ?? null,
                    'coverage_percent' => $coveragePercents[$serviceId][$expRangeId] ?? null,
                ];
            }
        }

        return $ranges;
    }

    public function destroy(string $tariffListId, DeleteTariffList $deleteTariffList, UpdateAllTariffStatuses $updateAllTariffStatuses)
    {
        try {
            $deleteTariffList->execute($tariffListId);
            $updateAllTariffStatuses->execute();

            return response()->json(['message' => 'Tariff list version has been deleted.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Tariff list not found. It may have already been deleted.'], 404);
        } catch (Exception $e) {
            Log::error('Failed to delete tariff list', ['tariff_list_id' => $tariffListId, 'error' => $e->getMessage()]);

            return response()->json(['message' => 'Failed to delete tariff list. Please try again.'], 500);
        }
    }
}
