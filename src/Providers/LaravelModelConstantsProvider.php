<?php

namespace MennoVanHout\LaravelModelConstants\Providers;

use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use MennoVanHout\LaravelModelConstants\Console\Commands\ModelConstantCleanCommand;
use MennoVanHout\LaravelModelConstants\Console\Commands\ModelConstantGenerateCommand;
use MennoVanHout\LaravelModelConstants\Listeners\GenerateModelConstants;

class LaravelModelConstantsProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../../config/model-constants.php' => config_path('model-constants.php'),
        ]);

        $this->commands([
            ModelConstantGenerateCommand::class,
            ModelConstantCleanCommand::class
        ]);

        if (App::environment() != 'testing' && App::environment() != 'production') {
            Event::listen(
                MigrationsEnded::class,
                GenerateModelConstants::class
            );
        }
    }
}
