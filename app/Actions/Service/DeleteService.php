<?php

namespace App\Actions\Service;

use App\Models\Operation\Data;
use App\Models\Operation\ExpenseRange;
use App\Models\Operation\Service;
use Exception;
use Illuminate\Support\Facades\DB;

class DeleteService
{
    public function execute(string $serviceId): void
    {
        DB::transaction(function () use ($serviceId) {
            $service = Service::where('service_id', $serviceId)->firstOrFail();

            $expenseRangeCount = ExpenseRange::where('service_id', $service->service_id)->count();

            if ($expenseRangeCount > 0) {
                throw new Exception("Cannot delete service '{$service->service_type}' because {$expenseRangeCount} expense range(s) are assigned to it.");
            }

            $dataId = $service->data_id;
            $service->delete();

            $referencing = DB::table('services')->where('data_id', $dataId)->exists();

            if (! $referencing) {
                Data::where('data_id', $dataId)->delete();
            }
        });
    }
}
