## Admin
A Game Manage System Power By [Laravel](https://github.com/laravel) and [PeadAdmin](http://www.PearAdmin.com)  

## Requirements
* [PHP](https://github.com/php) >= 8.4.0  
* PHP [Swoole](https://github.com/swoole) Extension  
* [Laravel](https://github.com/laravel) >= 11.0.0  
* [LayUI](https://github.com/layui/layui) >= 2.9.10  
* [PearAdmin](https://github.com/pearadmin/pear-admin-layui) >= 4.0.0  
* [Composer](https://github.com/composer)  

## Installation

#### Clone repo
```sh
git clone https://github.com/QCute/admin && cd admin
```

#### Clone UI framework
```sh
cd public
mkdir -p build
cd build

# clone ui
git clone https://github.com/pearadmin/pear-admin-layui
# or
git clone https://gitee.com/pear-admin/pear-admin-layui.git

# clone site
git clone https://github.com/pearadmin/pear-admin-site
# or
git clone https://gitee.com/pear-admin/pear-admin-site.git

# back to root
cd ../../
```

#### Install dependency  
```sh
composer install -vvv
```

#### Make env file  
```sh
cp .env.example .env
```

#### Generate Key  
```sh
php artisan key:generate
```

#### Change Domain and/or Prefix  
```
# http://admin.localhost
ADMIN_ROUTE_DOMAIN=admin
# http://localhost/admin
ADMIN_ROUTE_PREFIX=admin
```

#### Setup Database Connection  
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root

# table name prefix
DB_PREFIX=laravel_
DB_PREFIX_ADMIN=admin_
DB_PREFIX_API=api_
DB_PREFIX_WEB=web_
```

#### Create Database  
```sql
create database `laravel`;
```

#### Migrate Table  
```sh
php artisan migrate
```

#### Seed Role/Permission/Menu Data  
```sh
php artisan db:seed --class=AdminSeeder
```

#### Create User  
```sh
php artisan admin:create-user
```

#### Run  
```sh
# laravel
php artisan serve --host=0.0.0.0 --port=80
# run with octane
php artisan octane:start --host=0.0.0.0 --port=80
```

#### Usage 
* Open http://admin.localhost/ in browser if you setup route domain.  
* Open http://localhost/admin/ in browser if you setup route prefix.
* Open http://admin.localhost/admin/ in browser if you setup route domain and prefix.

## Advance

#### Table Order Sort
```sh
php artisan sorter:order "table name"
```

#### Data Migration
```sh
# export seeder
php artisan seeder:export "config name"
```

#### Install SSH Pass Script
```sh
# the sshpass script is used to manage the remote machine
php artisan sshpass:install
```

#### Run Release Mode Using SystemDaemon
```sh
# install unit
sudo php artisan service:install
# start
sudo systemctl start laravel-admin.service
```

## Tables

* Laravel
    * cache
    * cache_locks
    * failed_jobs
    * job_batches
    * jobs
    * migrations
    * password_reset_tokens
    * sessions
    * users

* Admin
    * user
    * user_role
    * role
    * role_permission
    * permission
    * menu
    * operation_log
    * channel
    * role_channel
    * channel_server
    * server
    * role_server
    * import_log
    * config_file
    * config_table
    * user_manage
    * user_chat_manage
    * mail
    * notice
    * ssh_key

* API
    * server
    * maintain_notice
    * impeach
    * client_error_log
    * sensitive_word_data
    * log

* Web
    * naivgation
    * log
