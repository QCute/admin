<?php

namespace App\Admin\Services\Auth;

use App\Admin\Models\Admin\UserModel;
// use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public static function check()
    {
        return self::guard()->check();
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public static function guest()
    {
        return self::guard()->guest();
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array  $credentials
     * @param  bool  $remember
     * @return bool
     */
    public static function attempt(array $credentials = [], bool $remember = false)
    {
        return self::guard()->attempt($credentials, $remember);
    }

    /**
     * Get the currently authenticated user.
     *
     * @return UserModel|null
     */
    public static function user()
    {
        return self::guard()->user();
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public static function logout()
    {
        return self::guard()->logout();
    }

    /**
     * Attempt to get the guard from the local cache.
     *
     * @return Guard|StatefulGuard
     */
    public static function guard()
    {
        $guard = config('admin.auth.guard');

        return Auth::guard($guard);
    }
}
