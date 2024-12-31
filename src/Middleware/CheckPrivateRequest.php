<?php

namespace Oh86\GW\Middleware;

use Illuminate\Http\Request;
use Oh86\Http\Exceptions\ErrorCodeException;

class CheckPrivateRequest
{
    /**
     * Summary of handle
     * @param Request $request
     * @param \Closure $next
     * @return void
     */
    public function handle($request, \Closure $next, string $privateRequest)
    {
        /**
         * @var array{app: string, ticket: string}
         */
        $config = config("gw-auth.private-requests.$privateRequest");

        if (!$config || $config['app'] != $request->header('Gw-Private-App')) {
            throw new ErrorCodeException(403, "app error", null, 403);
        }

        $time = $request->header('Gw-Private-Time');
        if (abs(time() - $time) > 300) {
            throw new ErrorCodeException(403, "time error", null, 403);
        }

        $expectedSignature = sm3(sprintf(
            "%s%s%s",
            $config['app'],
            $time,
            $config['ticket'],
        ));

        if ($expectedSignature != $request->header('Gw-Private-Sign')) {
            throw new ErrorCodeException(403, "signature error", null, 403);
        }

        return $next($request);
    }
}