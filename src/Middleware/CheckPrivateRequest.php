<?php

namespace Oh86\GW\Auth\Middleware;

use Illuminate\Http\Request;
use Oh86\Http\Exceptions\ErrorCodeException;

class CheckPrivateRequest
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @param null|string $gw
     */
    public function handle($request, \Closure $next, $gw = null)
    {
        $gw = $gw ?: config("gw-auth.default");
        /**
         * @var array{app: string, ticket: string, ignore-check: null|bool}
         */
        $config = config("gw-auth.gateways.$gw.private-request");

        if (!$config || $config['app'] != $request->header('GW-Private-App')) {
            throw new ErrorCodeException(403, "app error", null, 403);
        }

        if (!($config['ignore-check'] ?? false)) {
            $time = $request->header('GW-Private-Time');
            if (abs(time() - $time) > 300) {
                throw new ErrorCodeException(403, "time error", null, 403);
            }

            $expectedSignature = sm3(sprintf(
                "%s%s%s",
                $config['app'],
                $time,
                $config['ticket'],
            ));

            if ($expectedSignature != $request->header('GW-Private-Sign')) {
                throw new ErrorCodeException(403, "signature error", null, 403);
            }
        }

        return $next($request);
    }
}