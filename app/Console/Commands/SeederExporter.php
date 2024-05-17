<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SeederExporter extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'seeder:export {config} {--except-fields=created_time,updated_time,deleted_time : except fields}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export seeder';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $name = $this->argument('config');
        $studlyName = Str::studly($name);
        $className = "{$studlyName}Seeder";

        $exceptFields = explode(',', $this->option('except-fields'));
        $seederFile = $this->laravel->databasePath() . "/seeders/$className.php";
        $contents =$this->laravel['files']->get(__DIR__ . "/stubs/SeederExporter.stub");

        $contents = $this->replace($name, $className, $exceptFields, $contents);

        $this->laravel['files']->put($seederFile, $contents);

        $this->line("<info>$name tables seed file was created:</info> " . str_replace(base_path(), '', $seederFile));
        $this->line("Use: <info>php artisan db:seed --class={$name}</info>");
    }

    /**
     * Execute the console command.
     *
     * @param string $name
     * @param array  $exceptFields
     * @return string
     */
    protected function replace(string $name, string $className, array $exceptFields = [], string $contents = '')
    {
        $import = [];
        $code = [];

        $list = config("$name.database") ?? [];

        $connection = $list['connection'];

        $excepts = $list['excepts'];

        foreach($list as $name => $table) {

            $tokens = explode('_', $name);
            $lastKey = array_key_last($tokens);
            // not a table
            if($tokens[$lastKey] != 'table') continue;

            $tokens[$lastKey] = 'model';
            $modelKey = implode('_', $tokens);
            // table no model
            if(!isset($list[$modelKey])) continue;
            $model = $list[$modelKey];

            // excepts
            if(in_array($table, $excepts)) continue;

            $import[] = $this->getTableDataAsImport($connection, $model, $table);
            $code[] = $this->getTableDataAsCode($connection, $model, $table, $exceptFields);
        }

        $replaces = [
            'DummyImport' => implode("\n", $import),

            'DummyClass' => $className,

            'DummyCode' => implode("\n", $code),
        ];

        $contents = str_replace(array_keys($replaces), array_values($replaces), $contents);

        return $contents;
    }

    /**
     * Get model import.
     *
     * @param string $connection
     * @param string $model
     * @param string $table
     *
     * @return string
     */
    protected function getTableDataAsImport(string $connection, string $model, string $table)
    {
        return "use $model;";
    }

    /**
     * Get data array from table as string result var_export.
     *
     * @param string $connection
     * @param string $model
     * @param string $table
     * @param array  $exceptFields
     *
     * @return string
     */
    protected function getTableDataAsCode(string $connection, string $model, string $table, array $exceptFields = [])
    {
        $fields = DB::connection($connection)->getSchemaBuilder()->getColumnListing($table);
        $fields = array_diff($fields, $exceptFields);

        $array = (new $model)
            ->newQuery()
            ->withOnly([])
            ->select($fields)
            ->get()
            ->toArray();

        $data = $this->varExport($array, str_repeat(' ', 12));

        $model = basename(str_replace("\\", '/', $model));
        return "
        $model::truncate();
        $model::insert(
            $data
        );";
    }

    /**
     * Custom var_export for correct work with \r\n.
     *
     * @param $var
     * @param string $indent
     *
     * @return string
     */
    protected function varExport($var, $indent = '')
    {
        switch (gettype($var)) {

            case 'string':
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';

            case 'array':
                $indexed = array_keys($var) === range(0, count($var) - 1);

                $row = [];

                foreach ($var as $key => $value) {
                    $row[] = "$indent    " . ($indexed ? '' : $this->varExport($key) . ' => ') . $this->varExport($value, "{$indent}    ");
                }

                return "[\n" . implode(",\n", $row) . "\n" . $indent . ']';

            case 'boolean':
                return $var ? 'true' : 'false';

            case 'integer':
            case 'double':
                return $var;

            default:
                return var_export($var, true);
        }
    }
}
