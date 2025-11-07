<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Operation\TariffList;
use App\Models\Operation\ExpenseRange;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Financial\TariffListController;
use App\Http\Controllers\Financial\ExpenseRangeController;

class UpdateTariffStatuses extends Command
{
    protected $signature = 'tariffs:update-statuses';

    protected $description = 'Recompute and persist tl_status for all tariff list versions';

    public function handle(): int
    {
        try {
            // Use the controller's method to update all statuses and handle service migration
            $expenseRangeController = new ExpenseRangeController();
            $tariffListController = new TariffListController($expenseRangeController);
            
            // This will update all statuses and automatically migrate services
            $reflection = new \ReflectionClass($tariffListController);
            $method = $reflection->getMethod('updateAllTariffStatuses');
            $method->setAccessible(true);
            $method->invoke($tariffListController);

            $this->info('Tariff list statuses updated and services migrated where applicable.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error updating tariff statuses: ' . $e->getMessage());
            Log::error('Tariff status update command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }
}
