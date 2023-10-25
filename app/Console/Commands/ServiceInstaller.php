<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class ServiceInstaller extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service:install {--name=}';

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
# specific user
User=root
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
# The limits of max open file
LimitNOFILE=262144
# The limits of max core dump size
LimitCORE=infinity
";
        $name = $this->option('name');
        $file = "/usr/lib/systemd/system/laravel-admin" . ($name ? "-" . $name : $name) . ".service";
        // system unit
        file_put_contents($file, $data);
        // mode
        $process = new Process(["chmod", "0644", $file]);
        $process->run();
        if(!$process->isSuccessful()) {
            $this->error($process->getErrorOutput());
            return $process->getExitCode();
        }
        // context
        $process = new Process(["chcon", "-h", "system_u:object_r:systemd_unit_file_t:s0", $file]);
        $process->run();
        if(!$process->isSuccessful()) {
            $this->error($process->getErrorOutput());
            return $process->getExitCode();
        }
        // system reload
        $process = new Process(["systemctl", "daemon-reload"]);
        $process->run();
        if(!$process->isSuccessful()) {
            $this->error($process->getErrorOutput());
            return $process->getExitCode();
        }
        // env file
        file_put_contents(base_path(".env"), file_get_contents(base_path(".env.example")));
        // generate key
        Artisan::call("key:generate");
        // ssh pass
        Artisan::call("sshpass:install");
        // migrate table
        $this->info("Install service successfully");
        $this->info("Edit .env file, setup database config");
        $this->info("Run artisan migrate to migrate tables");
        $this->info("Run artisan db:seed to seed table data");
        return 0;
    }
}
