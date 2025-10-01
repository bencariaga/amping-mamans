<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Storage\Data;
use App\Models\Authentication\Occupation;
use App\Models\User\Client;
use Exception;

class OccupationController extends Controller
{
    private function generateNextId(string $prefix, string $table, string $primaryKey): string
    {
        $year = Carbon::now()->year;
        $base = "{$prefix}-{$year}";
        $max  = DB::table($table)->where($primaryKey, 'like', "{$base}-%")->max($primaryKey);
        $lastNum = $max ? (int) Str::afterLast($max, '-') : 0;
        return $base . '-' . Str::padLeft($lastNum + 1, 9, '0');
    }

    public function index()
    {
        return response()->json(Occupation::join('data', 'occupations.data_id', '=', 'data.data_id')->orderBy('data.updated_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $request->validate(['occupation' => 'required|string|max:30']);
        $dataId = 'DATA-' . now()->year . '-' . Str::padLeft(Data::count() + 1, 9, '0');

        Data::create([
            'data_id'     => $dataId,
            'data_status' => 'Unarchived',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $occId = 'OCCUP-' . now()->year . '-' . Str::padLeft(Occupation::count() + 1, 9, '0');

        $occupation = Occupation::create([
            'occupation_id' => $occId,
            'data_id'       => $dataId,
            'occupation'    => $request->occupation,
        ]);

        return response()->json($occupation);
    }

    public function confirmChanges(Request $request)
    {
        $changes = $request->all();
        DB::beginTransaction();

        try {
            foreach ($changes['create'] as $occupationData) {
                $occupationName = Str::of($occupationData['occupation'])->trim();

                if ($occupationName === '') {
                    continue;
                }

                $dataId = $this->generateNextId('DATA', 'data', 'data_id');
                Data::create(['data_id' => $dataId, 'data_status' => 'Unarchived']);

                Occupation::create([
                    'occupation_id' => $this->generateNextId('OCCUP', 'occupations', 'occupation_id'),
                    'data_id' => $dataId,
                    'occupation' => (string) $occupationName
                ]);
            }

            foreach ($changes['update'] as $occupationData) {
                $occupationName = Str::of($occupationData['occupation'])->trim();

                if ($occupationName === '') {
                    continue;
                }

                Occupation::where('occupation_id', $occupationData['occupation_id'])->update(['occupation' => (string) $occupationName]);
            }

            foreach ($changes['delete'] as $occupationId) {
                $occupation = Occupation::find($occupationId);

                if ($occupation) {
                    $clientCount = Client::where('occupation_id', $occupation->occupation_id)->count();

                    if ($clientCount > 0) {
                        throw new Exception("Cannot delete occupation '{$occupation->occupation}' because {$clientCount} client(s) are assigned to it.");
                    }

                    $dataId = $occupation->data_id;
                    $occupation->delete();
                    $referencing = false;
                    $tablesToCheck = ['occupations'];

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

            $updatedOccupations = Occupation::join('data', 'occupations.data_id', '=', 'data.data_id')->orderBy('data.updated_at', 'desc')->get()->map(function ($occupation) {
                return ['id' => $occupation->occupation_id, 'name' => $occupation->occupation, 'status' => 'existing'];
            });

            return response()->json(['success' => true, 'occupations' => $updatedOccupations]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $occupation = Occupation::where('occupation_id', $id)->first();

            if ($occupation) {
                $clientCount = Client::where('occupation_id', $occupation->occupation_id)->count();

                if ($clientCount > 0) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'error' => "Cannot delete occupation '{$occupation->occupation}' because {$clientCount} client(s) are assigned to it."]);
                }

                $dataId = $occupation->data_id;
                $occupation->delete();
                $referencing = false;
                $tablesToCheck = ['occupations'];

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

            return response()->json(['success' => false, 'error' => 'Occupation not found.']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
