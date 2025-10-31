<?php

namespace App\Livewire\Modal;

use App\Models\Operation\Data;
use App\Models\Operation\Service;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class ModalServices extends Component
{
    public $services = [];

    public $newServiceType = '';

    public $newAssistScope = [];

    public $editingServiceId = null;

    public $editingServiceType = '';

    public $editingAssistScope = [];

    public $isOpen = false;

    protected $listeners = [
        'loadServices' => 'loadServices',
        'openServicesModal' => 'openModal',
        'closeServicesModal' => 'closeModal',
    ];

    private function assistScopeOptions(): array
    {
        return [
            'Inpatient Care',
            'Outpatient Care',
            'Generic Drug',
            'Branded Drug',
            'Biopsy',
            'CT Scan',
            'MRI',
            'Pap Test',
            'PET Scan',
            'Ultrasound',
            'X-Ray Scan',
            'Endoscopy',
            'Electrolyte Imbalance',
            'End-Stage Renal Disease',
            'Drug Overdose',
            'Liver Dialysis',
            'Hypervolemia',
            'Peritoneal Dialysis',
            'Poisoning',
            'Uremia',
            'Anemia',
            'Blood Transfusion',
            'Childbirth',
            'Hemorrhage',
        ];
    }

    private function matchOptionsFromString(?string $stored, array $options): array
    {
        $result = [];
        if ($stored === null || $stored === '') {
            return $result;
        }
        $hay = mb_strtolower($stored);
        foreach ($options as $opt) {
            if ($opt === null) {
                continue;
            }
            $needle = mb_strtolower($opt);
            if (mb_strpos($hay, $needle) !== false) {
                $result[] = $opt;
            }
        }

        return $result;
    }

    public function mount()
    {
        $this->loadServices();
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->loadServices();
    }

    public function loadServices()
    {
        $options = $this->assistScopeOptions();
        $this->services = Service::join('data', 'services.data_id', '=', 'data.data_id')
            ->orderBy('data.updated_at', 'desc')
            ->get()
            ->map(function ($svc) use ($options) {
                $assist = $svc->assist_scope ?? '';

                return [
                    'service_id' => $svc->service_id,
                    'data_id' => $svc->data_id,
                    'service_type' => $svc->service_type ?? '',
                    'assist_scope' => $assist,
                    'assist_scope_list' => $this->matchOptionsFromString($assist, $options),
                ];
            })
            ->toArray();
    }

    public function addService()
    {
        $this->validate(['newServiceType' => 'required|min:3']);
        DB::beginTransaction();
        try {
            $dataId = $this->generateNextId('DATA', 'data', 'data_id');
            Data::create([
                'data_id' => $dataId,
                'data_status' => 'Unarchived',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            Service::create([
                'service_id' => $this->generateNextId('SERVICE', 'services', 'service_id'),
                'data_id' => $dataId,
                'service_type' => $this->newServiceType,
                'assist_scope' => Arr::join($this->newAssistScope, ', ') ?: null,
            ]);
            DB::commit();
            $this->newServiceType = '';
            $this->newAssistScope = [];
            $this->loadServices();
            $this->dispatch('showToast', ['message' => 'Service added', 'type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('showToast', ['message' => 'Error adding service: '.$e->getMessage(), 'type' => 'error']);
        }
    }

    public function startEdit($serviceId)
    {
        $svc = Service::where('service_id', $serviceId)->first();
        if (! $svc) {
            return;
        }
        $this->editingServiceId = $serviceId;
        $this->editingServiceType = $svc->service_type;
        $this->editingAssistScope = $this->matchOptionsFromString($svc->assist_scope ?? '', $this->assistScopeOptions());
    }

    public function cancelEdit()
    {
        $this->editingServiceId = null;
        $this->editingServiceType = '';
        $this->editingAssistScope = [];
    }

    public function updateService()
    {
        $this->validate(['editingServiceType' => 'required|min:3']);
        DB::beginTransaction();
        try {
            $service = Service::where('service_id', $this->editingServiceId)->first();
            if ($service) {
                $service->update([
                    'service_type' => $this->editingServiceType,
                    'assist_scope' => Arr::join($this->editingAssistScope, ', ') ?: null,
                ]);
                DB::commit();
                $this->cancelEdit();
                $this->loadServices();
                $this->dispatch('showToast', ['message' => 'Service updated', 'type' => 'success']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('showToast', ['message' => 'Error updating service: '.$e->getMessage(), 'type' => 'error']);
        }
    }

    public function deleteService($serviceId)
    {
        DB::beginTransaction();
        try {
            $svc = Service::where('service_id', $serviceId)->first();
            if (! $svc) {
                $this->dispatch('showToast', ['message' => 'Service not found', 'type' => 'error']);

                return;
            }
            $expenseCount = DB::table('expense_ranges')->where('service_id', $svc->service_id)->count();
            if ($expenseCount > 0) {
                $this->dispatch('showToast', ['message' => "Cannot delete service '{$svc->service_type}' because {$expenseCount} expense range(s) reference it.", 'type' => 'error']);

                return;
            }
            $dataId = $svc->data_id;
            $svc->delete();
            $referencing = DB::table('services')->where('data_id', $dataId)->exists() || DB::table('tariff_lists')->where('data_id', $dataId)->exists();
            if (! $referencing) {
                Data::where('data_id', $dataId)->delete();
            }
            DB::commit();
            $this->loadServices();
            $this->dispatch('showToast', ['message' => 'Service deleted', 'type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('showToast', ['message' => 'Error deleting service: '.$e->getMessage(), 'type' => 'error']);
        }
    }

    private function generateNextId(string $prefix, string $table, string $primaryKey): string
    {
        $year = Carbon::now()->year;
        $base = "{$prefix}-{$year}";
        $max = DB::table($table)->where($primaryKey, 'like', "{$base}-%")->max($primaryKey);
        $lastNum = $max ? (int) Str::afterLast($max, '-') : 0;
        $next = $lastNum + 1;
        $padded = Str::padLeft($next, 9, '0');

        return "{$base}-{$padded}";
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.modal.modal-services', [
            'assistScopeOptions' => $this->assistScopeOptions(),
        ]);
    }
}
