<?php

namespace App\Api\Middlewares;

use Closure;
use Illuminate\Http\Request;

class Bootstrap
{
    public function handle(Request $request, Closure $next)
    {
        $helpers = config('api.helpers');
        if(file_exists($helpers)) {
            require $helpers;
        }

        return $next($request);
    }
}
