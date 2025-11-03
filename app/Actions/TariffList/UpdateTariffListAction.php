<?php

namespace App\Actions\TariffList;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UpdateTariffListAction
{
    protected $updateTariffListRanges;

    public function __construct(UpdateTariffListRanges $updateTariffListRanges)
    {
        $this->updateTariffListRanges = $updateTariffListRanges;
    }

    public function execute(Request $request, string $tariffListId): RedirectResponse|JsonResponse
    {
        try {
            $this->updateTariffListRanges->execute($request, $tariffListId);
            session()->flash('success', 'Tariff list has been updated successfully.');
            return redirect()->route('tariff-lists');
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Tariff list not found.'], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Failed to update tariff list', [
                'tariff_list_id' => $tariffListId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to update tariff list: An unexpected error occurred. (Error: '.$e->getMessage().')',
                'errors' => null,
            ], 500);
        }
    }
}
