<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Storage\Data;
use App\Models\Operation\Service;
use Exception;

class ServiceController extends Controller
{
    private function generateNextId(string $prefix, string $table, string $primaryKey): string
    {
        $year = Carbon::now()->year;
        $base = "{$prefix}-{$year}";
        $max  = DB::table($table)->where($primaryKey, 'like', "{$base}-%")->max($primaryKey);
        $lastNum = $max ? (int) Str::afterLast($max, '-') : 0;
        $next = $lastNum + 1;
        $padded = Str::padLeft($next, 9, '0');
        return "{$base}-{$padded}";
    }

    private function assistScopeOptions(): array
    {
        return [
            "Inpatient Care",
            "Outpatient Care",
            "Generic Drug",
            "Branded Drug",
            "Biopsy",
            "CT Scan",
            "MRI",
            "Pap Test",
            "PET Scan",
            "Ultrasound",
            "X-Ray Scan",
            "Endoscopy",
            "Electrolyte Imbalance",
            "End-Stage Renal Disease",
            "Drug Overdose",
            "Liver Dialysis",
            "Hypervolemia",
            "Peritoneal Dialysis",
            "Poisoning",
            "Uremia",
            "Anemia",
            "Blood Transfusion",
            "Childbirth",
            "Hemorrhage"
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
                'assist_scope_list' => $this->matchOptionsFromString($assist, $options)
            ];
        });

        return response()->json($services);
    }

    public function confirmChanges(Request $request)
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

                $dataId = $this->generateNextId('DATA', 'data', 'data_id');

                Data::create([
                    'data_id' => $dataId,
                    'data_status' => 'Unarchived',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                Service::create([
                    'service_id' => $this->generateNextId('SERVICE', 'services', 'service_id'),
                    'data_id' => $dataId,
                    'service_type' => $svcName,
                    'assist_scope' => isset($svcData['assist_scope']) ? $svcData['assist_scope'] : null
                ]);
            }

            foreach ($updates as $svcData) {
                if (!is_array($svcData) || empty($svcData['service_id']) || empty($svcData['service_type'])) {
                    continue;
                }

                $svcId = $svcData['service_id'];
                $svcName = (string) Str::of($svcData['service_type'])->trim();

                if ($svcName === '') {
                    continue;
                }

                Service::where('service_id', $svcId)->update([
                    'service_type' => $svcName,
                    'assist_scope' => isset($svcData['assist_scope']) ? $svcData['assist_scope'] : null
                ]);
            }

            foreach ($deletes as $svcId) {
                if (!is_string($svcId) || Str::of($svcId)->trim() === '') {
                    continue;
                }

                $svc = Service::where('service_id', $svcId)->first();

                if ($svc) {
                    $expenseCount = DB::table('expense_ranges')->where('service_id', $svc->service_id)->count();

                    if ($expenseCount > 0) {
                        throw new Exception("Cannot delete service '{$svc->service_type}' because {$expenseCount} expense range(s) reference it.");
                    }

                    $dataId = $svc->data_id;
                    $svc->delete();
                    $referencing = false;
                    $tablesToCheck = ['services', 'tariff_lists'];

                    foreach ($tablesToCheck as $table) {
                        if (DB::table($table)->where('data_id', $dataId)->exists()) {
                            $referencing = true;
                            break;
                        }
                    }

                    if (!$referencing) {
                        Data::where('data_id', $dataId)->delete();
                    }
                }
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
                    'assist_scope_list' => $this->matchOptionsFromString($assist, $options)
                ];
            });

            return response()->json(['success' => true, 'services' => $updatedServices]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $svc = Service::where('service_id', $id)->first();
            if ($svc) {
                $expenseCount = DB::table('expense_ranges')->where('service_id', $svc->service_id)->count();

                if ($expenseCount > 0) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'error' => "Cannot delete service '{$svc->service_type}' because {$expenseCount} expense range(s) reference it."]);
                }

                $dataId = $svc->data_id;
                $svc->delete();
                $referencing = false;
                $tablesToCheck = ['services', 'tariff_lists'];

                foreach ($tablesToCheck as $table) {
                    if (DB::table($table)->where('data_id', $dataId)->exists()) {
                        $referencing = true;
                        break;
                    }
                }

                if (!$referencing) {
                    Data::where('data_id', $dataId)->delete();
                }

                DB::commit();
                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'error' => 'Service not found.']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
