<?php

namespace App\Http\Controllers\Core;

use App\Actions\Core\CreateService;
use App\Actions\Core\DeleteService;
use App\Actions\Core\UpdateService;
use App\Http\Controllers\Controller;
use App\Models\Operation\Service;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceController extends Controller
{

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

    public function index()
    {
        $options = $this->assistScopeOptions();
        $services = Service::join('data', 'services.data_id', '=', 'data.data_id')->orderBy('data.updated_at', 'desc')->get()->map(function ($svc) use ($options) {
            $assist = $svc->assist_scope ?? '';

            return [
                'service_id' => $svc->service_id,
                'data_id' => $svc->data_id,
                'service_type' => $svc->service_type ?? '',
                'assist_scope' => $assist,
                'assist_scope_list' => $this->matchOptionsFromString($assist, $options),
            ];
        });

        return response()->json($services);
    }

    public function confirmChanges(Request $request, CreateService $createService, UpdateService $updateService, DeleteService $deleteService)
    {
        $payload = $request->all();
        $creates = isset($payload['create']) && is_array($payload['create']) ? $payload['create'] : [];
        $updates = isset($payload['update']) && is_array($payload['update']) ? $payload['update'] : [];
        $deletes = isset($payload['delete']) && is_array($payload['delete']) ? $payload['delete'] : [];

        DB::beginTransaction();

        try {
            foreach ($creates as $svcData) {
                if (!is_array($svcData) || empty($svcData['service_type'])) {
                    continue;
                }

                $svcName = (string) Str::of($svcData['service_type'])->trim();

                if ($svcName === '') {
                    continue;
                }

                $createService->execute($svcName, $svcData['assist_scope'] ?? null);
            }

            foreach ($updates as $svcData) {
                if (!is_array($svcData) || empty($svcData['service_id']) || empty($svcData['service_type'])) {
                    continue;
                }

                $svcName = (string) Str::of($svcData['service_type'])->trim();

                if ($svcName === '') {
                    continue;
                }

                $updateService->execute($svcData['service_id'], $svcName, $svcData['assist_scope'] ?? null);
            }

            foreach ($deletes as $svcId) {
                if (!is_string($svcId) || Str::of($svcId)->trim() === '') {
                    continue;
                }

                $deleteService->execute($svcId);
            }

            DB::commit();

            $options = $this->assistScopeOptions();

            $updatedServices = Service::join('data', 'services.data_id', '=', 'data.data_id')->orderBy('data.updated_at', 'desc')->get()->map(function ($svc) use ($options) {
                $assist = $svc->assist_scope ?? '';

                return [
                    'service_id' => $svc->service_id,
                    'data_id' => $svc->data_id,
                    'service_type' => $svc->service_type ?? '',
                    'assist_scope' => $assist,
                    'assist_scope_list' => $this->matchOptionsFromString($assist, $options),
                ];
            });

            return response()->json(['success' => true, 'services' => $updatedServices]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id, DeleteService $deleteService)
    {
        DB::beginTransaction();

        try {
            $deleteService->execute($id);
            DB::commit();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
