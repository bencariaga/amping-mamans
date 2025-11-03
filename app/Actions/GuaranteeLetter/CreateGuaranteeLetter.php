<?php

namespace App\Actions\GuaranteeLetter;

use App\Actions\IdGeneration\GenerateGuaranteeLetterId;
use App\Models\Operation\GuaranteeLetter;

class CreateGuaranteeLetter
{
    public function execute($application, $budgetUpdate): GuaranteeLetter
    {
        return GuaranteeLetter::create([
            'gl_id' => GenerateGuaranteeLetterId::execute(),
            'application_id' => $application->application_id,
            'budget_update_id' => $budgetUpdate->budget_update_id,
        ]);
    }
}
