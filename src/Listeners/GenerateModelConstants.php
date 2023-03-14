<?php

namespace MennoVanHout\LaravelModelConstants\Listeners;

use Artisan;
use MennoVanHout\LaravelModelConstants\Console\Commands\ModelConstantCommand;

class GenerateModelConstants
{
    public function handle(): void
    {
        Artisan::call(ModelConstantCommand::class);
    }
}
