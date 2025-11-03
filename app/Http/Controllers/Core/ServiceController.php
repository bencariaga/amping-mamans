<?php

namespace App\Http\Controllers\Core;

use App\Actions\Service\CreateService;
use App\Actions\Service\DeleteService;
use App\Actions\Service\UpdateService;
use App\Http\Controllers\Controller;
use App\Models\Operation\Service;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::join('data', 'services.data_id', '=', 'data.data_id')->orderBy('data.updated_at', 'desc')->get()->map(function ($svc) {

            return [
                'service_id' => $svc->service_id,
                'data_id' => $svc->data_id,
                'service' => $svc->service ?? '',
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
                if (! is_array($svcData) || empty($svcData['service_type'])) {
                    continue;
                }

                $svcName = (string) Str::of($svcData['service_type'])->trim();

                if ($svcName === '') {
                    continue;
                }

                $createService->execute($svcName);
            }

            foreach ($updates as $svcData) {
                if (! is_array($svcData) || empty($svcData['service_id']) || empty($svcData['service_type'])) {
                    continue;
                }

                $svcName = (string) Str::of($svcData['service_type'])->trim();

                if ($svcName === '') {
                    continue;
                }

                $updateService->execute($svcData['service_id'], $svcName);
            }

            foreach ($deletes as $svcId) {
                if (! is_string($svcId) || Str::of($svcId)->trim() === '') {
                    continue;
                }

                $deleteService->execute($svcId);
            }

            DB::commit();

            $updatedServices = Service::join('data', 'services.data_id', '=', 'data.data_id')->orderBy('data.updated_at', 'desc')->get()->map(function ($svc) {
                return [
                    'service_id' => $svc->service_id,
                    'data_id' => $svc->data_id,
                    'service' => $svc->service ?? '',
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
