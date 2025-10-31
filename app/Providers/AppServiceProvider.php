<?php

namespace App\Providers;

use App\Scout\Engines\CustomDatabaseEngine;
use App\Services\FakeSmsService;
use App\Services\TextBeeService;
use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TextBeeService::class, fn () => new TextBeeService);
    }

    public function boot(): void
    {
        $this->app->make(EngineManager::class)->extend('database', fn () => new CustomDatabaseEngine);
        $this->app->bind('FakeSmsService', fn () => new FakeSmsService);
    }
}
