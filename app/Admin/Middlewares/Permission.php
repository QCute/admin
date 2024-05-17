<?php

namespace App\Admin\Middlewares;

use App\Admin\Services\Auth\AuthService;
use App\Admin\Services\Auth\PermissionService;
use Illuminate\Http\Request;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        if(AuthService::guest()) {
            return $next($request);    
        }

        if(!PermissionService::check($request)) {
            return PermissionService::error();
        }

        return $next($request);
    }
}
