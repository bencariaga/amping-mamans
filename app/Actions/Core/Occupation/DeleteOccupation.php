<?php

namespace App\Actions\Core\Occupation;

use App\Models\Authentication\Occupation;
use App\Models\Operation\Data;
use App\Models\User\Client;
use Exception;
use Illuminate\Support\Facades\DB;

class DeleteOccupation
{
    public function execute(string $occupationId): void
    {
        DB::transaction(function () use ($occupationId) {
            $occupation = Occupation::where('occupation_id', $occupationId)->firstOrFail();

            $clientCount = Client::where('occupation_id', $occupation->occupation_id)->count();

            if ($clientCount > 0) {
                throw new Exception("Cannot delete occupation '{$occupation->occupation}' because {$clientCount} client(s) are assigned to it.");
            }

            $dataId = $occupation->data_id;
            $occupation->delete();

            $referencing = DB::table('occupations')->where('data_id', $dataId)->exists();

            if (!$referencing) {
                Data::where('data_id', $dataId)->delete();
            }
        });
    }
}
