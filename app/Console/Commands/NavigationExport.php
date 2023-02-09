<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NavigationExport extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'navigation:export-seed {classname=NavigationSeeder} {--except-fields=id,created_at,updated_at : except fields}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export seed navigation';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $name = $this->argument('classname');
        $exceptFields = explode(',', $this->option('except-fields'));
        $seedFile = $this->laravel->databasePath().'/seeders/'.$name.'.php';
        $contents = $this->getStub('NavigationSeeder');

        $replaces = [
            'DummyClass' => $name,

            'Table'       => "navigation",

            'ArrayNavigation'       => $this->getTableDataArrayAsString('navigation', $exceptFields),
        ];

        $contents = str_replace(array_keys($replaces), array_values($replaces), $contents);

        $this->laravel['files']->put($seedFile, $contents);

        $this->line('<info>Navigation tables seed file was created:</info> '.str_replace(base_path(), '', $seedFile));
        $this->line("Use: <info>php artisan db:seed --class={$name}</info>");
    }

    /**
     * Get data array from table as string result var_export.
     *
     * @param string $table
     * @param array  $exceptFields
     *
     * @return string
     */
    protected function getTableDataArrayAsString($table, $exceptFields = [])
    {
        $fields = \DB::getSchemaBuilder()->getColumnListing($table);
        $fields = array_diff($fields, $exceptFields);

        $array = \DB::table($table)->get($fields)->map(function ($item) {
            return (array) $item;
        })->all();

        return $this->varExport($array, str_repeat(' ', 12));
    }

    /**
     * Get stub contents.
     *
     * @param $name
     *
     * @return string
     */
    protected function getStub($name)
    {
        return $this->laravel['files']->get(__DIR__."/stubs/$name.stub");
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
                return '"'.addcslashes($var, "\\\$\"\r\n\t\v\f").'"';

            case 'array':
                $indexed = array_keys($var) === range(0, count($var) - 1);

                $r = [];

                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                        .($indexed ? '' : $this->varExport($key).' => ')
                        .$this->varExport($value, "{$indent}    ");
                }

                return "[\n".implode(",\n", $r)."\n".$indent.']';

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
