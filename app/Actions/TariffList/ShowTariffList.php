<?php

namespace App\Actions\TariffList;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ShowTariffList
{
    protected $getTariffListForView;

    public function __construct(GetTariffListForView $getTariffListForView)
    {
        $this->getTariffListForView = $getTariffListForView;
    }

    public function execute(string $tariffListId): View|JsonResponse
    {
        try {
            $data = $this->getTariffListForView->execute($tariffListId);
            return view('pages.tariff-list.tariff-list-view', $data);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Tariff list version not found.'], 404);
        } catch (Exception $e) {
            Log::error('Failed to retrieve tariff list', [
                'tariff_list_id' => $tariffListId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while retrieving the tariff list.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
