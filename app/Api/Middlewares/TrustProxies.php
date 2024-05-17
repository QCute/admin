<?php

namespace App\Api\Middlewares;

use Closure;
use \Illuminate\Http\Request;

class TrustProxies
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
        if(empty(Request::getTrustedProxies())) {
            $addresses = config('api.proxies.addresses');
            $headers = config('api.proxies.headers');
            Request::setTrustedProxies($addresses, $headers);
        }

        return $next($request);
    }
}
