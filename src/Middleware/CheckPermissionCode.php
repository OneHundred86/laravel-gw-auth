<?php

namespace Oh86\GW\Auth\Middleware;

use Oh86\Http\Exceptions\ErrorCodeException;

class CheckPermissionCode
{
    /**
     * @param \Illuminate\Http\Request  $request
     * @param string $permissionCode
     * @param \Closure $next
     */
    public function handle($request, \Closure $next, $permissionCode)
    {
        $permissionCodes = json_decode($request->header(config('gw-auth.permission-codes-header'))) ?? [];

        if (!in_array($permissionCode, $permissionCodes)) {
            throw new ErrorCodeException(403, "permission denied", null, 403);
        }

        return $next($request);
    }
}