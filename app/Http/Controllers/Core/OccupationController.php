<?php

namespace App\Http\Controllers\Core;

use App\Actions\Core\Occupation\CreateOccupation;
use App\Actions\Core\Occupation\DeleteOccupation;
use App\Actions\Core\Occupation\UpdateOccupation;
use App\Http\Controllers\Controller;
use App\Models\Authentication\Occupation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OccupationController extends Controller
{

    public function index()
    {
        return response()->json(Occupation::join('data', 'occupations.data_id', '=', 'data.data_id')->orderBy('data.updated_at', 'desc')->get());
    }

    public function store(Request $request, CreateOccupation $createOccupation)
    {
        $request->validate(['occupation' => 'required|string|max:30']);

        $occupation = $createOccupation->execute($request->occupation);

        return response()->json($occupation);
    }

    public function confirmChanges(Request $request, CreateOccupation $createOccupation, UpdateOccupation $updateOccupation, DeleteOccupation $deleteOccupation)
    {
        $changes = $request->all();
        DB::beginTransaction();

        try {
            foreach ($changes['create'] as $occupationData) {
                $occupationName = Str::of($occupationData['occupation'])->trim();

                if ($occupationName === '') {
                    continue;
                }

                $createOccupation->execute((string) $occupationName);
            }

            foreach ($changes['update'] as $occupationData) {
                $occupationName = Str::of($occupationData['occupation'])->trim();

                if ($occupationName === '') {
                    continue;
                }

                $updateOccupation->execute($occupationData['occupation_id'], (string) $occupationName);
            }

            foreach ($changes['delete'] as $occupationId) {
                $deleteOccupation->execute($occupationId);
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

    public function destroy(Request $request, $id, DeleteOccupation $deleteOccupation)
    {
        DB::beginTransaction();

        try {
            $deleteOccupation->execute($id);
            DB::commit();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
