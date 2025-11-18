<?php

namespace Oh86\GW\Auth\Middleware;

use Illuminate\Auth\Middleware\Authenticate;

/**
 * 可选的身份认证
 * 即使所有 guards 认证失败，也不会抛出认证失败异常，而是已游客身份访问
 */
class AuthenticateOptional extends Authenticate
{
    /**
     * Handle an unauthenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     */
    protected function unauthenticated($request, array $guards)
    {
        // do nothing
    }
}
