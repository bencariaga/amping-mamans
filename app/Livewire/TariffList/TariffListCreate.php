<?php

namespace App\Livewire\TariffList;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Storage\Data;
use App\Models\Operation\TariffList;
use App\Models\Operation\Service;
use App\Models\Operation\ExpenseRange;

class TariffListCreate extends Component
{
    public $show = false;
    public $services;
    public $selectedServices = [];
    public $effectivity_date;
    public $previewBase;
    public $previewNextNumber;

    protected $listeners = [
        'openCreateModal' => 'openCreateModal'
    ];

    public function mount()
    {
        $this->services = Service::all();
        $now = Carbon::now();
        $this->effectivity_date = now()->toDateString();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $this->previewBase = "TL-{$year}-{$month}";
        $latestTariff = TariffList::where('tariff_list_id', 'like', "{$this->previewBase}%")->latest('tariff_list_id')->first();
        $lastNumTariff = $latestTariff ? (int) Str::afterLast($latestTariff->tariff_list_id, '-') : 0;
        $this->previewNextNumber = $lastNumTariff + 1;
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->selectedServices = [];
        $this->effectivity_date = now()->toDateString();
        $this->show = true;
    }

    public function closeModal()
    {
        $this->show = false;
    }

    public function create()
    {
        $this->validate([
            'selectedServices' => 'required|array|min:1',
            'effectivity_date' => 'required|date'
        ]);

        $now = Carbon::now();
        $baseTariff = "TL-{$now->year}-" . Str::upper($now->format('M'));
        $latestTariff = TariffList::where('tariff_list_id', 'like', "{$baseTariff}%")->latest('tariff_list_id')->first();
        $lastNumTariff = $latestTariff ? (int) Str::afterLast($latestTariff->tariff_list_id, '-') : 0;

        if ($lastNumTariff >= 99) {
            session()->flash('warning', 'You cannot create more than 99 tariff list versions in the same month');
            return;
        }

        $baseData = "DATA-{$now->year}";
        $latestData = Data::where('data_id', 'like', "{$baseData}%")->latest('data_id')->first();
        $lastNumData = $latestData ? (int) Str::afterLast($latestData->data_id, '-') : 0;
        $nextNumData = Str::padLeft((string) ($lastNumData + 1), 9, '0');
        $newDataId = "{$baseData}-{$nextNumData}";

        Data::create(['data_id' => $newDataId, 'data_status' => 'Unarchived', 'created_at' => $now, 'updated_at' => $now]);

        $newTariffListId = "{$baseTariff}-" . ($lastNumTariff + 1);
        $serviceNames = Service::whereIn('service_id', $this->selectedServices)->pluck('service_type')->implode(', ');

        TariffList::create([
            'tariff_list_id' => $newTariffListId,
            'data_id' => $newDataId,
            'service_types_involved' => $serviceNames,
            'effectivity_status' => 'Inactive',
            'effectivity_date' => $this->effectivity_date
        ]);

        foreach ($this->selectedServices as $serviceId) {
            ExpenseRange::create([
                'exp_range_id' => 'EXP-PLACEHOLDER-' . Str::random(8),
                'tariff_list_id' => $newTariffListId,
                'service_id' => $serviceId,
                'exp_range_min' => 0,
                'exp_range_max' => 0,
                'coverage_percent' => 0
            ]);
        }

        $this->dispatch('refreshTariffTable');
        $this->closeModal();

        session()->flash('success', 'New tariff list version created successfully.');
    }

    public function render()
    {
        return view('livewire.tariff-list.tariff-list-create');
    }
}
