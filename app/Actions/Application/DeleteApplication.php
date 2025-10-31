<?php

namespace App\Actions\Application;

use App\Models\Operation\Application;
use App\Models\Operation\GuaranteeLetter;
use Illuminate\Support\Facades\DB;

class DeleteApplication
{
    public function execute(string $applicationId): void
    {
        DB::transaction(function () use ($applicationId) {
            $application = Application::where('application_id', $applicationId)->firstOrFail();

            GuaranteeLetter::where('application_id', $applicationId)->delete();
            $application->delete();
        });
    }
}
