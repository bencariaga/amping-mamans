<?php

namespace App\Actions\TariffList;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DestroyTariffList
{
    protected $deleteTariffList;
    protected $updateAllTariffStatuses;

    public function __construct(
        DeleteTariffList $deleteTariffList,
        UpdateAllTariffStatuses $updateAllTariffStatuses
    ) {
        $this->deleteTariffList = $deleteTariffList;
        $this->updateAllTariffStatuses = $updateAllTariffStatuses;
    }

    public function execute(string $tariffListId): JsonResponse
    {
        try {
            $this->deleteTariffList->execute($tariffListId);
            $this->updateAllTariffStatuses->execute();

            return response()->json(['message' => 'Tariff list version has been deleted.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Tariff list not found. It may have already been deleted.'], 404);
        } catch (Exception $e) {
            Log::error('Failed to delete tariff list', ['tariff_list_id' => $tariffListId, 'error' => $e->getMessage()]);

            return response()->json(['message' => 'Failed to delete tariff list. Please try again.'], 500);
        }
    }
}
