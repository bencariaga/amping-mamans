<?php

namespace App\Actions\TariffList;

use Illuminate\Http\JsonResponse;

class GetTakenDatesResponse
{
    protected $getTakenDates;

    public function __construct(GetTakenDates $getTakenDates)
    {
        $this->getTakenDates = $getTakenDates;
    }

    public function execute(): JsonResponse
    {
        $takenDates = $this->getTakenDates->execute();
        return response()->json(['taken_dates' => $takenDates], 200);
    }
}
