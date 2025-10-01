<?php

namespace App\Livewire\TariffList;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Operation\TariffList;
use App\Models\Operation\Service;
use App\Models\Operation\ExpenseRange;
use Illuminate\Support\Facades\Log;

class TariffListVersionTable extends Component
{
    public $tariffModels = [];
    public $groupedTariffs = [];
    public $services;

    protected $listeners = [
        'refreshTariffTable' => 'loadData'
    ];

    public function mount()
    {
        $this->services = Service::all();
        $this->loadData();
    }

    public function loadData()
    {
        $tariffListsQuery = TariffList::with('data')
            ->select('data_id', DB::raw('MAX(effectivity_date) as latest_date'))
            ->groupBy('data_id')
            ->orderBy('latest_date', 'desc')
            ->get();
        Log::info('Loaded tariff lists', ['count' => $tariffListsQuery->count()]);
        $grouped = [];
        $models = [];

        foreach ($tariffListsQuery as $list) {
            $tariffModel = TariffList::where('data_id', $list->data_id)
                ->orderBy('effectivity_date', 'desc')
                ->orderBy('tariff_list_id', 'desc')
                ->first();

            // Skip if no TariffList found for this data_id (e.g., after deletion)
            if (!$tariffModel) {
                continue;
            }

            $models[$list->data_id] = $tariffModel;
            $servicesList = ExpenseRange::where('tariff_list_id', $tariffModel->tariff_list_id)
                ->join('services', 'expense_ranges.service_id', '=', 'services.service_id')
                ->pluck('services.service_type')
                ->unique();

            $grouped[$list->data_id] = $servicesList;
        }

        $this->groupedTariffs = collect($grouped)->reverse()->all();
        $this->tariffModels = $models;
    }

    public function openCreateModal()
    {
        $this->dispatch('openCreateModal');
    }

    public function openEditModal($tariffListId)
    {
        $this->dispatch('openEditModal', $tariffListId);
    }

    public function openDeleteModal($tariffListId)
    {
        $this->dispatch('openDeleteModal', $tariffListId);
    }

    public function openApplyModal($tariffListId)
    {
        $this->dispatch('openApplyModal', $tariffListId);
    }

    public function render()
    {
        return view('livewire.tariff-list.tariff-list-version-table', [
            'tariffModels' => $this->tariffModels,
            'groupedTariffs' => $this->groupedTariffs,
            'services' => $this->services,
        ]);
    }
}
