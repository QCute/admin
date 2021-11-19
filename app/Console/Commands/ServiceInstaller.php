<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ServiceInstaller extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Laravel Admin Service Install';

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
        $path = base_path();
        $data =
"[Unit]
Description=Laravel Admin Service
# start after mariadb
After=mariadb.service

[Install]
WantedBy=multi-user.target

[Service]
# lararvel admin directory
WorkingDirectory=$path
# start
ExecStart=/usr/bin/php artisan octane:start
# reload
ExecReload=/usr/bin/php artisan octane:reload
# stop
ExecStop=/usr/bin/php artisan octane:stop
# restart
Restart=on-failure
# restart time
RestartSec=5s
";
        $file = "/usr/lib/systemd/system/laravel-admin.service";
        file_put_contents($file, $data);
        // mode
        echo shell_exec(implode(" ", ["chmod", "0644", $file]));
        // selinux context
        echo shell_exec(implode(" ", ["chcon", "-h", "system_u:object_r:systemd_unit_file_t:s0", $file]));
        // reload service
        echo shell_exec(implode(" ", ["systemctl", "daemon-reload"]));
        // env file
        file_put_contents(base_path(".env"), file_get_contents(base_path(".env.example")));
        // generate key
        Artisan::call("key:generate");
        // ssh pass
        Artisan::call("sshpass:install");
        return 0;
    }
}
