<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OrderSorter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sorter:order {table} {--k|key=id : id} {--p|parent=parent_id : parent_id} {--o|order=order : order}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Table order sorter';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $data = [];
        $table = $this->argument('table');
        $key = $this->option('key') ?? 'id';
        $parentKey = $this->option('parent') ?? 'parent_id';
        $orderKey = $this->option('order') ?? 'order';

        $root = DB::connection()
            ->setTablePrefix('')
            ->table($table)
            ->where($parentKey, "=", "0")
            ->orderBy($orderKey)
            ->get();

        $index = 1;

        foreach ($root as $row) {

            $parent = DB::connection()
                ->setTablePrefix('')
                ->table($table)
                ->where($parentKey, "=", $row->{$key})
                ->orderBy($orderKey)
                ->get();

            $row->{$key} = $parent_id = $index++;
            $data[] = (array)$row;

            foreach ($parent as $children) {
                $children->{$key} = $index++;
                $children->{$parentKey} = $parent_id;
                $data[] = (array)$children;
            }
        }

        DB::statement("TRUNCATE `$table`");
        DB::table("$table")->insert($data);

        return 0;
    }
}
