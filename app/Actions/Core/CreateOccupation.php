<?php

namespace App\Actions\Core;

use App\Actions\DatabaseTableIdGeneration\GenerateDataId;
use App\Actions\DatabaseTableIdGeneration\GenerateOccupationId;
use App\Models\Authentication\Occupation;
use App\Models\Operation\Data;
use Illuminate\Support\Facades\DB;

class CreateOccupation
{
    public function execute(string $occupationName): Occupation
    {
        return DB::transaction(function () use ($occupationName) {
            $dataId = GenerateDataId::execute();

            Data::create([
                'data_id' => $dataId,
                'archive_status' => 'Unarchived',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return Occupation::create([
                'occupation_id' => GenerateOccupationId::execute(),
                'data_id' => $dataId,
                'occupation' => $occupationName,
            ]);
        });
    }
}
