<?php

namespace App\Livewire\TariffList;

use Livewire\Component;
use App\Models\Operation\TariffList;

class TariffListApply extends Component
{
    public $show = false;
    public $tariffListId;

    protected $listeners = [
        'openApplyModal' => 'openApplyModal'
    ];

    public function openApplyModal($tariffListId)
    {
        $this->tariffListId = $tariffListId;
        $this->show = true;
    }

    public function applyVersion()
    {
        $tariffList = TariffList::where('tariff_list_id', $this->tariffListId)->firstOrFail();
        $tariffList->update(['effectivity_status' => 'Effective']);
        $this->dispatch('refreshTariffTable');
        $this->show = false;
        $this->tariffListId = null;
        session()->flash('success', 'Tariff list version applied successfully.');
    }

    public function render()
    {
        return view('livewire.tariff-list.tariff-list-apply');
    }
}
