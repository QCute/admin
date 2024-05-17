<?php

namespace App\Admin\Middlewares;

use App\Admin\Services\Auth\AuthService;
use Closure;
use \Illuminate\Http\Request;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // "/" or "path/to/name"
        $path = $request->path();
        $path = $path === '/' ? $path : '/' . $path;

        // "" or "/prefix"
        $prefix = config('admin.route.prefix');
        $prefix = $prefix === '' ? $prefix : '/' . $prefix;

        if($path !== '/') {

            $excepts = config('admin.auth.excepts');
    
            foreach($excepts as $except) {

                // except path
                if(strcasecmp($path, $prefix . $except) == 0) {
                    return $next($request);
                }

            }

        }

        if(AuthService::guest()) {

            $redirectTo = config('admin.auth.redirect_to');

            return redirect()->to($prefix . $redirectTo);
        }

        return $next($request);
    }
}
