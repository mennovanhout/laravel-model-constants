<?php

namespace MennoVanHout\LaravelModelConstants\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use SplFileInfo;

abstract class ModelConstantCommand extends Command
{
    protected function findAllFilesWithClass(string $class): Collection
    {
        /**
         * Get all files
         * There are more optimized ways to do this, however this will support all types of laravel usage.
         * For example: People who refactor Laravel in a Domain driven approach where models are not within the app directory
         */
        return collect(File::allFiles(base_path()))
            ->map(function (SplFileInfo $file) {
                if ($file->getExtension() != 'php' || str_contains($file->getPathname(), 'vendor')) {
                    return '';
                }

                $content = file_get_contents($file->getPathname());

                // Find namespace
                $matches = [];
                preg_match("/namespace\s+([\w\\\\]+);/", $content, $matches);
                $namespace = $matches[1] ?? '';

                // Find classname
                $matches = [];
                preg_match("/class\s+([\w\\\\]+)\s/", $content, $matches);
                $className = $matches[1] ?? '';

                return "{$namespace}\\{$className}";
            })
            ->filter(function (string $fileClass) use ($class) {
                if ($fileClass === '' || $fileClass === '\\' || !class_exists($fileClass, false)) {
                    return false;
                }

                $reflection = new ReflectionClass($fileClass);

                return $reflection->isSubclassOf($class) && !$reflection->isAbstract();
            });
    }
}
