<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        // health: '/up',
        then: function(Application $application) {

            // admin
            Route::middleware(config('admin.route.middleware'))
                ->prefix(config('admin.route.prefix'))
                ->namespace(config('admin.route.namespace'))
                ->domain(config('admin.route.domain'))
                ->group(__DIR__ . '/../routes/admin.php');

            // api
            Route::middleware(config('api.route.middleware'))
                ->prefix(config('api.route.prefix'))
                ->namespace(config('api.route.namespace'))
                ->domain(config('api.route.domain'))
                ->group(__DIR__ . '/../routes/api.php');

            // web
            Route::middleware(config('web.route.middleware'))
                ->prefix(config('web.route.prefix'))
                ->namespace(config('web.route.namespace'))
                ->domain(config('web.route.domain'))
                ->group(__DIR__ . '/../routes/web.php');

        }
    )
    ->withMiddleware(function (Middleware $middleware) {

        // web
        $middleware->appendToGroup('web', [
            App\Http\Middlewares\Bootstrap::class,
            App\Http\Middlewares\TrustProxies::class,
            App\Http\Middlewares\Log::class,
        ]);

        // api
        $middleware->appendToGroup('api', [
            App\Api\Middlewares\Bootstrap::class,
            App\Api\Middlewares\TrustProxies::class,
            App\Api\Middlewares\Log::class,
        ]);

        // admin
        $middleware->group('admin', [
            App\Admin\Middlewares\Bootstrap::class,
            App\Admin\Middlewares\TrustProxies::class,
            App\Admin\Middlewares\Auth::class,
            App\Admin\Middlewares\Permission::class,
            App\Admin\Middlewares\RequestedWith::class,
            App\Admin\Middlewares\LogOperation::class,
        ]);
    })
    ->withProviders([
        App\Providers\EventServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
