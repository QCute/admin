# Admin
A Game Manage System Power By [Laravel-Admin](https://github.com/z-song/laravel-admin)

# Requirements
* PHP >= 7.0.0
* Laravel >= 5.5.0

# Installation

Install dependency
```
composer install -vvv
```

Make env file
```
cp .env.example .env
```

Add prefix admin to .env
```
ADMIN_ROUTE_PREFIX=admin
DB_DATABASE=laravel(or your database name)
```

Import SQL dump file
``` 
create database `laravel`; -- (or your database name)
use `laravel`; -- (or your database name)
source storage/mysql_dump/laravel.sql;
```

Run
```
php -S 0.0.0.0:80 -t public/
```

Open http://localhost/admin/ in browser, use username admin and password admin to login.
