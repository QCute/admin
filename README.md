# Admin
A Game Manage System Power By [Laravel-Admin](https://github.com/z-song/laravel-admin)

# Requirements
* PHP >= 7.0.0
* Laravel >= 5.5.0

# Installation

Install dependency
```sh
composer install -vvv
```

Make env file
```sh
cp .env.example .env
```

Change Domain and/or Prefix
```
# http://admin.localhost
ADMIN_ROUTE_DOMAIN=admin
# http://localhost/admin
ADMIN_ROUTE_PREFIX=admin
```

Create Database  
```sql
create database `admin`; -- database name
```

Install Admin  
```sh
php artisan admin:install
```

Import User/Role/Permission/Menu Data  
```sh
php artisan db:seed --class=AdminTablesSeeder
```

Run  
```sh
php artisan serve --host=0.0.0.0 --port=80
```

# Usage 
Open http://admin.localhost/ or http://localhost/admin/ in browser, use username admin and password admin to login.
