<?php

namespace App\Providers;

use App\Gates\ApplicationGate;
use App\Gates\BudgetGate;
use App\Gates\TariffGate;
use App\Gates\UserManagementGate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class GateServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerPolicies();

        ApplicationGate::define();
        BudgetGate::define();
        TariffGate::define();
        UserManagementGate::define();
    }
}
