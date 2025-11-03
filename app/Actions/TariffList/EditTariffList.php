<?php

namespace App\Actions\TariffList;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class EditTariffList
{
    protected $getTariffListForEdit;

    public function __construct(GetTariffListForEdit $getTariffListForEdit)
    {
        $this->getTariffListForEdit = $getTariffListForEdit;
    }

    public function execute(string $tariffListId): View|RedirectResponse
    {
        try {
            $data = $this->getTariffListForEdit->execute($tariffListId);
            return view('pages.tariff-list.tariff-list-edit', $data);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('tariff-lists')->with('error', 'Tariff list version not found.');
        } catch (Exception $e) {
            Log::error('Failed to load edit page', [
                'tariff_list_id' => $tariffListId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('tariff-lists')->with('error', 'An error occurred while loading the edit page.');
        }
    }
}
