<?php

namespace MennoVanHout\LaravelModelConstants\Console\Commands;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

class ModelConstantGenerateCommand extends ModelConstantCommand
{
    protected $signature = 'model:constants';
    protected $description = 'Generate constants for model column names';

    public function handle(): void
    {
        $models = $this->findAllFilesWithClass(Model::class);

        foreach ($models as $model) {
            $this->info("Generating constant file for Model: {$model}");

            /** @var Model $instance */
            $reflection = new ReflectionClass($model);
            $instance = new $model;

            $columns = collect($instance->getConnection()->getSchemaBuilder()->getColumnListing($instance->getTable()))->map(function (string $name) {
                return "\t const " . strtoupper($name) . " = '{$name}';";
            })->toArray();
            $enumClassName = $reflection->getShortName() . 'Attributes';
            $enumFileName = substr($reflection->getFileName(), 0, strrpos($reflection->getFileName(), '.')) . 'Attributes.php';

            file_put_contents($enumFileName, "<?php\n\rnamespace {$reflection->getNamespaceName()};\n\ruse MennoVanHout\\LaravelModelConstants\\Types\\ModelAttributes;\n\rclass {$enumClassName} extends ModelAttributes\n{\n" . implode("\n", $columns) . "\n}");
        }
    }
}
