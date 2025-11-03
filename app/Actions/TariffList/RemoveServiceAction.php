<?php

namespace App\Actions\TariffList;

use App\Exceptions\FinanceException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RemoveServiceAction
{
    protected $removeServiceFromTariffList;
    protected $updateAllTariffStatuses;

    public function __construct(
        RemoveServiceFromTariffList $removeServiceFromTariffList,
        UpdateAllTariffStatuses $updateAllTariffStatuses
    ) {
        $this->removeServiceFromTariffList = $removeServiceFromTariffList;
        $this->updateAllTariffStatuses = $updateAllTariffStatuses;
    }

    public function execute(Request $request, string $tariffListId): JsonResponse
    {
        try {
            $request->validate([
                'service_id' => 'required|exists:services,service_id',
            ]);

            $this->removeServiceFromTariffList->execute($tariffListId, $request->service_id);
            $this->updateAllTariffStatuses->execute();

            return response()->json(['message' => 'Service has been removed successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Tariff list not found.'], 404);
        } catch (FinanceException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (Exception $e) {
            Log::error('Failed to remove service', [
                'tariff_list_id' => $tariffListId,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Failed to remove service. Please try again.'], 500);
        }
    }
}
