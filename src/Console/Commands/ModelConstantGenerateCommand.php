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
                return config('model-constants.indentation', '    ') . "const " . strtoupper($name) . " = '{$name}';";
            })->toArray();
            $enumClassName = $reflection->getShortName() . 'Attributes';
            $fileLocation = substr($reflection->getFileName(), 0, strrpos($reflection->getFileName(), '/')) . config('model-constants.path', '/');
            if (!is_dir($fileLocation)) {
                mkdir($fileLocation);
            }
            $enumFileName = $fileLocation . $reflection->getShortName() . 'Attributes.php';

            $nl = config('model-constants.newline', "\n");
            $documentEnd = config('model-constants.eof_blank_line', false) ? $nl : '';
            file_put_contents($enumFileName, "<?php{$nl}{$nl}namespace {$reflection->getNamespaceName()};{$nl}{$nl}use MennoVanHout\\LaravelModelConstants\\Types\\ModelAttributes;{$nl}{$nl}class {$enumClassName} extends ModelAttributes{$nl}{" . $nl . implode($nl, $columns) . $nl . "}" . $documentEnd);
        }
    }
}
