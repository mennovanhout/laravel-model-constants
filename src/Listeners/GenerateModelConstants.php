<?php

namespace MennoVanHout\LaravelModelConstants\Listeners;

use Artisan;
use MennoVanHout\LaravelModelConstants\Console\Commands\ModelConstantGenerateCommand;

class GenerateModelConstants
{
    public function handle(): void
    {
        Artisan::call(ModelConstantGenerateCommand::class);
    }
}
