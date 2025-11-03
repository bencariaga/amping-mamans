<?php

namespace App\Actions\TariffList;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckEffectivityDateResponse
{
    protected $checkEffectivityDate;

    public function __construct(CheckEffectivityDate $checkEffectivityDate)
    {
        $this->checkEffectivityDate = $checkEffectivityDate;
    }

    public function execute(Request $request): JsonResponse
    {
        $request->validate([
            'effectivity_date' => 'required|date_format:Y-m-d',
        ]);

        $date = $request->input('effectivity_date');
        $exists = $this->checkEffectivityDate->execute($date);

        return response()->json(['is_taken' => $exists], 200);
    }
}
