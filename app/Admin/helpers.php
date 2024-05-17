<?php

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory as ViewFactory;

if (! function_exists('admin_config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array<string, mixed>|string|null  $key
     * @param  mixed  $default
     * @return ($key is null ? \Illuminate\Config\Repository : ($key is string ? mixed : null))
     */
    function admin_config($key = null, $default = null)
    {
        if(is_null($key)) {
            return config('admin');    
        }

        return config("admin.$key", $default);
    }
}

if (! function_exists('admin_trans')) {
    /**
     * Translate the given message.
     *
     * @param  string|null  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    function admin_trans($key = null, $replace = [], $locale = null)
    {
        /** @var Illuminate\Contracts\Translation\Translator **/
        $translator = app('translator');

        if (is_null($key)) {
            return $translator;
        }

        return $translator->get("admin.$key", $replace, $locale);
    }
}

if (! function_exists('admin_path')) {
    /**
     * Get the path for the given path.
     *
     * @param  string  $path
     * @return string
     */
    function admin_path($path = '')
    {
        // admin prefix
        $prefix = config('admin.route.prefix');
        
        // "" or "/prefix"
        $prefix = $prefix === '' ? $prefix : '/' . trim($prefix, '/');

        $path = trim($path, '/');

        return $prefix  . '/' . $path;
    }
}

if (! function_exists('admin_view')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string|null  $view
     * @param  Arrayable|array  $data
     * @param  array  $mergeData
     * @return View|ViewFactory
     */
    function admin_view($view = null, $data = [], $mergeData = [])
    {
        /** @var ViewFactory */
        $factory = app(ViewFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        $prefix = config('admin.resource.view');

        return $factory->make("$prefix/$view", $data, $mergeData);
    }
}

if (! function_exists('admin_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    function admin_asset($path, $secure = null)
    {
        $url = config('app.url');
        
        $prefix = trim(parse_url($url, PHP_URL_PATH), '/');

        $vendor = trim(config('admin.vendor.path'), '/');

        $framework = trim(config('admin.vendor.framework'), '/');

        $path = trim($path, '/');

        return asset("/$prefix/$vendor/$framework/$path", $secure);
    }
}

if (! function_exists('admin_plugin_asset')) {
    /**
     * Generate an plugin asset path for the application.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    function admin_plugin_asset($path, $secure = null)
    {
        $url = config('app.url');
        
        $prefix = trim(parse_url($url, PHP_URL_PATH), '/');

        $vendor = trim(config('admin.vendor.path'), '/');

        $framework = trim(config('admin.vendor.framework'), '/');

        $path = trim($path, '/');

        return asset("/$prefix/$vendor/$framework/$path", $secure);
    }
}

if (! function_exists('vendor_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    function vendor_asset($path, $secure = null)
    {
        $url = config('app.url');
        
        $prefix = trim(parse_url($url, PHP_URL_PATH), '/');

        $vendor = trim(config('admin.vendor.path'), '/');

        $path = trim($path, '/');

        return asset("/$prefix/$vendor/$path", $secure);
    }
}
