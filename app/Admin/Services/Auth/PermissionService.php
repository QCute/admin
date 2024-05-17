<?php

namespace App\Admin\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PermissionService
{
    /**
     * Check permission.
     *
     * @param Request $request
     * 
     * @return bool
     */
    public static function check(Request $request): bool
    {
        // the path match mode
        // permission                  request
        // 1. /path                    /path
        // 2. /path/*                  /path/...
        // 3. /path/*                  /path/.../.../...
        // 4. /path/*/to               /path/.../to
        // 5. /path/*/to/*/name        /path/.../to/.../name

        $user = AuthService::user();

        $prefix = config('admin.route.prefix');

        $requestPath = explode('/', trim($request->path(), '/'));

        // remove prefix
        if($prefix !== '') {
            array_shift($requestPath);
        }

        // role permission
        foreach($user->roles as $role) {

            foreach($role->permissions as $permission) {

                $isMatch = self::checkPath($requestPath, $permission->http_path);

                if ($isMatch) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function checkPath(array $requestPath, string $permissionHttpPath): bool 
    {
        $pathList = explode("\r\n", $permissionHttpPath);

        foreach($pathList as $path) {

            $permissionPath = explode('/', trim($path, '/'));

            $i = 0;
            $lastPermissionPath = '';
            while(true) {
    
                $thisRequestPath = $requestPath[$i] ?? '';
                $thisPermissionPath = $permissionPath[$i] ?? $lastPermissionPath;
                $i++;
    
                // any
                if($thisPermissionPath == '*') {
                    $lastPermissionPath = $thisPermissionPath;
    
                    // end
                    if($thisRequestPath == '') {
                        return true;
                    }
    
                    continue;
                }
    
                // equal
                if($thisRequestPath == $thisPermissionPath) {
    
                    $lastPermissionPath = '';
    
                    // end
                    if($thisPermissionPath == '') {
                        return true;
                    }
    
                    continue;
                }
    
                // not equal
                break;
            }
        }

        return false;
    }

    /**
     * Send error response page.
     * 
     * @return mixed
     */
    public static function error()
    {
        return new Response(admin_view(config('admin.vendor.view.forbidden')));
    }

    /**
     * If current user is administrator.
     *
     * @return bool
     */
    public static function isAdministrator(): bool
    {
        $user = AuthService::user();

        return $user->roles->pluck('tag')->contains('Administrator');
    }
}
