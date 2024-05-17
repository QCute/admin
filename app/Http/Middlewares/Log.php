<?php

namespace App\Http\Middlewares;

use App\Http\Models\LogModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Log
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
        if ($this->shouldLogOperation($request)) {
            $log = [
                'path'    => substr($request->path(), 0, 255),
                'method'  => $request->method(),
                'ip'      => $request->getClientIp(),
                'input'   => json_encode($request->input()),
            ];

            try {
                LogModel::insert($log);
            } catch (\Exception $exception) {
                // pass
            }
        }

        return $next($request);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function shouldLogOperation(Request $request)
    {
        return config('web.log.enable')
            && !$this->inExceptArray($request)
            && $this->inAllowedMethods($request->method());
    }

    /**
     * Whether requests using this method are allowed to be logged.
     *
     * @param string $method
     *
     * @return bool
     */
    protected function inAllowedMethods(string $method)
    {
        $allowedMethods = config('web.operation_log.allowed_methods');
        $allowedMethods = collect($allowedMethods)->filter()->map(function ($method) {
            return strtoupper($method);
        });

        if ($allowedMethods->isEmpty()) {
            return true;
        }

        return $allowedMethods->contains($method);
    }

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function inExceptArray(Request $request)
    {
        $prefix = config('api.route.prefix', '');

        foreach (config('web.log.except') as $except) {

            if ($except === '/') {
                $except = $prefix . $except;
            } else {
                $except = $prefix . '/' . trim($except, '/');
            }

            $methods = [];

            if (Str::contains($except, ':')) {
                [$methods, $except] = explode(':', $except);
                $methods = explode(',', $methods);
                $methods = array_map(function($method) { return strtoupper($method); }, $methods);
            }

            if ($request->is($except) && (empty($methods) || in_array($request->method(), $methods))) {
                return true;
            }
        }

        return false;
    }
}
