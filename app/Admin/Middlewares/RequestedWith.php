<?php

namespace App\Admin\Middlewares;

use App\Admin\Services\Auth\AuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RequestedWith
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        /** @var Response */
        $response = $next($request);

        if(AuthService::guest()) {
            return $response;
        }

        if($request->pjax() || $request->ajax() || str_contains($response->headers->get('content-disposition'), 'attachment')) {
            return $response;
        }

        $class = config('admin.index');
        $controller = new $class();

        $response = $controller->index($request);

        $response = $response instanceof Response ? $response : new Response($response);
        
        return $response;
    }
}
