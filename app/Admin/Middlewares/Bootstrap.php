<?php

namespace App\Admin\Middlewares;

use Closure;
use Illuminate\Http\Request;

class Bootstrap
{
    public function handle(Request $request, Closure $next)
    {
        $helpers = config('admin.helpers');
        if(file_exists($helpers)) {
            require $helpers;
        }

        return $next($request);
    }
}
