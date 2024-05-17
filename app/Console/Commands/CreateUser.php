<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /** @var \Illuminate\Database\Eloquent\Model $userModel */
        $userModel = config('admin.database.user_model');

        /** @var \Illuminate\Database\Eloquent\Model $roleModel */
        $roleModel = config('admin.database.role_model');

        $username = $this->ask('Please enter a username to login');

        $password = Hash::make($this->secret('Please enter a password to login'));

        $name = $this->ask('Please enter a name to display');

        $roles = $roleModel::all();

        $selectedOption = $roles->pluck('name')->toArray();

        if (empty($selectedOption)) {
            $selected = $this->choice('Please choose a role for the user', $selectedOption, null, null, true);

            $roles = $roles->filter(function ($role) use ($selected) {
                return in_array($role->name, $selected);
            });
        }

        $user = new $userModel(compact('username', 'password', 'name'));

        $user->save();

        if (isset($roles)) {
            $user->roles()->attach($roles);
        }

        $this->info("User [$name] created successfully.");
    }
}
