# Admin
A Game Manage System Power By [Laravel-Admin](https://github.com/z-song/laravel-admin)  

# Requirements
* [PHP](https://github.com/php) >= 8.0.0  
* [Laravel](https://github.com/laravel) >= 8.0.0  
* PHP [Swoole](https://github.com/swoole) Extension or [RoadRunner](https://github.com/spiral/roadrunner)  
* [Composer](https://github.com/composer)

# Installation

Install dependency  
```sh
composer install -vvv
```

Make env file  
```sh
cp .env.example .env
```

Generate Key  
```sh
php artisan key:generate
```

Change Domain and/or Prefix  
```
# http://admin.localhost
ADMIN_ROUTE_DOMAIN=admin
# http://localhost/admin
ADMIN_ROUTE_PREFIX=admin
```

Setup Database Connection  
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=admin
DB_USERNAME=root
DB_PASSWORD=root
```

Create Database  
```sql
create database `admin`; -- database name
```

Migrate Table  
```sh
php artisan migrate
```

Seed Role/Permission/Menu Data  
```sh
php artisan db:seed --class=AdminTablesSeeder
```

Create User  
```sh
php artisan admin:create-user
```

Run  
```sh
# laravel
php artisan serve --host=0.0.0.0 --port=80
# run with octane
php artisan octane:start --host=0.0.0.0 --port=80
```

# Usage 
Open http://admin.localhost/ or http://localhost/admin/ in browser.
