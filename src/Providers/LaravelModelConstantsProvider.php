<?php

namespace MennoVanHout\LaravelModelConstants\Providers;

use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use MennoVanHout\LaravelModelConstants\Console\Commands\ModelConstantCommand;
use MennoVanHout\LaravelModelConstants\Listeners\GenerateModelConstants;

class LaravelModelConstantsProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            ModelConstantCommand::class
        ]);

        Event::listen(
            MigrationsEnded::class,
            GenerateModelConstants::class
        );
    }
}
