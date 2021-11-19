<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HelpersFixer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'helper:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Laravel Admin Helpers Fix';

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
        $helpers = base_path("vendor/encore/laravel-admin/src/helpers.php");
        $data = file_get_contents($helpers);
        $data = str_replace("->flash", "->now", $data);
        file_put_contents($helpers, $data);
        return 0;
    }
}
