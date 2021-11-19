<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SSHPassInstaller extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sshpass:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SSH Passphrase Script Install';

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
        $data =
'#!/usr/bin/env bash
# sshpass - use command line password with ssh
# inspire by https://github.com/huan/sshpass.sh
# ref to https://www.exratione.com/2014/08/bash-script-ssh-automation-without-a-password-prompt/
# the origin sshpass pass passphrase by pipe, but Symfony Process pipe need --enable-sigchild
# examples:
# sshpass <ssh or key passphrase> ssh <ssh options>
# sshpass <ssh or key passphrase> scp <scp options>
# sshpass <ssh or key passphrase> rsync <rsync options>

if [[ -n "${SSH_ASKPASS_PASSWORD}" ]];then
    cat <<< "${SSH_ASKPASS_PASSWORD}"
else
    export SSH_ASKPASS="$0"
    export SSH_ASKPASS_PASSWORD="$1"
    export DISPLAY=dummydisplay:0
    shift
    setsid "$@"
fi
';
        file_put_contents(base_path("vendor/bin/sshpass"), $data);
        return 0;
    }
}
