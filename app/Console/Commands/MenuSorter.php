<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MenuSorter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'menu:sort';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Admin Menu Sort';

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
    public function handle()
    {
        $data = [];
        $parent = DB::table("admin_menu")
            ->where("parent_id", "=", "0")
            ->orderBy("order")
            ->get()
            ->toArray();
        $index = 1;
        foreach ($parent as $row) {
            $children = DB::table("admin_menu")
                ->where("parent_id", "=", $row->id)
                ->orderBy("order")
                ->get()
                ->toArray();
            $row->id = $parent_id = $index++;
            $row = json_decode(json_encode($row), true);
            array_push($data, $row);
            foreach ($children as $sub) {
                $sub->id = $index++;
                $sub->parent_id = $parent_id;
                $sub = json_decode(json_encode($sub), true);
                array_push($data, $sub);
            }
        }
        DB::statement("TRUNCATE `admin_menu`");
        DB::table("admin_menu")->insert($data);
        return 0;
    }
}
