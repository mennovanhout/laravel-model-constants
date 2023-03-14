<?php

namespace MennoVanHout\LaravelModelConstants\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use SplFileInfo;

class ModelConstantCommand extends Command
{
    protected $signature = 'model:constants';
    protected $description = 'Generate constants for model column names';

    public function handle(): void
    {
        /**
         * Get all models
         * There are more optimized ways to do this, however this support all type of laravel approaches
         * Regular use of laravel, Domain driven use of laravel etc this can be used for all patterns
         */
        $models = collect(File::allFiles(base_path()))
            ->map(function (SplFileInfo $file) {
                if (str_contains($file->getPathname(), 'vendor')) {
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
            ->filter(function (string $class) {
                if ($class === '' || $class === '\\' || !class_exists($class)) {
                    return false;
                }

                $reflection = new ReflectionClass($class);

                return $reflection->isSubclassOf(Model::class) && !$reflection->isAbstract();
            });

        foreach ($models as $model) {
            $this->info("Generating constant file for Model: {$model}");

            /** @var Model $instance */
            $reflection = new ReflectionClass($model);
            $instance = new $model;

            $columns = collect($instance->getConnection()->getSchemaBuilder()->getColumnListing($instance->getTable()))->map(function (string $name) {
                return "\t const " . strtoupper($name) . " = '{$name}';";
            })->toArray();
            $enumClassName = $reflection->getShortName() . 'Columns';
            $enumFileName = substr($reflection->getFileName(), 0, strrpos($reflection->getFileName(), '.')) . 'Columns.php';

            file_put_contents($enumFileName, "<?php\n\rnamespace {$reflection->getNamespaceName()};\n\rclass {$enumClassName}\n{\n" . implode("\n", $columns) . "\n}");
        }
    }
}
