<?php

namespace App\Livewire\TariffList;

use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Storage\Data;
use App\Models\Operation\TariffList;
use App\Models\Operation\ExpenseRange;
use Exception;

class TariffListDelete extends Component
{
    public $show = false;
    public $tariffListId;
    public $tariffListExists = true;

    protected $listeners = [
        'openDeleteModal' => 'openDeleteModal'
    ];

    public function openDeleteModal($tariffListId)
    {
        $exists = TariffList::where('tariff_list_id', $tariffListId)->exists();
        if (!$exists) {
            $this->tariffListExists = false;
            $this->dispatch('refreshTariffTable');
            session()->flash('error', 'Tariff list not found. It may have already been deleted.');
            return;
        }
        $this->tariffListId = $tariffListId;
        $this->tariffListExists = true;
        $this->show = true;
    }

    public function confirmDelete()
    {
        if (!$this->tariffListId) {
            $this->closeModal();
            session()->flash('error', 'Invalid tariff list ID.');
            return;
        }
        $tariffList = TariffList::where('tariff_list_id', $this->tariffListId)->first();
        if (!$tariffList) {
            $this->closeModal();
            $this->dispatch('refreshTariffTable');
            session()->flash('warning', 'Tariff list was already deleted.');
            return;
        }
        try {
            DB::transaction(function () use ($tariffList) {
                $dataId = $tariffList->data_id;
                $versionsCount = TariffList::where('data_id', $dataId)->count();
                ExpenseRange::where('tariff_list_id', $this->tariffListId)->delete();
                $tariffList->delete();
                if ($versionsCount === 1) {
                    Data::where('data_id', $dataId)->delete();
                }
                Log::info("Tariff list deleted successfully", [
                    'tariff_list_id' => $this->tariffListId,
                    'data_id' => $dataId,
                    'versions_count' => $versionsCount
                ]);
            });
            $this->closeModal();
            session()->flash('success', 'Tariff list version has been deleted successfully.');
            $this->dispatch('refresh-page');
        } catch (Exception $e) {
            Log::error("Failed to delete tariff list", [
                'tariff_list_id' => $this->tariffListId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->closeModal();
            $this->dispatch('refreshTariffTable');
            if (Str::contains($e->getMessage(), ['not found', 'already been deleted'])) {
                session()->flash('warning', 'Tariff list was already deleted by another user.');
            } else {
                session()->flash('error', 'Failed to delete tariff list. Please try again.');
            }
        }
    }

    public function closeModal()
    {
        $this->show = false;
        $this->tariffListId = null;
        $this->tariffListExists = false;
    }

    public function render()
    {
        return view('livewire.tariff-list.tariff-list-delete');
    }
}
