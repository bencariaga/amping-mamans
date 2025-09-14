<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;
use App\Scout\Engines\CustomDatabaseEngine;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->app->make(EngineManager::class)
            ->extend('database', fn() => new CustomDatabaseEngine);
    }
}
