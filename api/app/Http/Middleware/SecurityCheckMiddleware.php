<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class SecurityCheckMiddleware
 * @package App\Http\Middleware
 */
class SecurityCheckMiddleware
{
    /**
     *
     */
    const API_AUTH_BYPASS_TOKEN_HEADER = "Api-Auth-Bypass-Token";

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Closure                   $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->bypassSecurity($request)) {
            return $next($request);
        }

        return response('Invalid Auth Token Provided', 401);

    }

    /**
     * @ match token is present in the header and has valid value.
     *
     * @param $request
     *
     * @return bool
     */
    private function bypassSecurity($request): bool
    {
        if ($request->header(self::API_AUTH_BYPASS_TOKEN_HEADER) == null) {
            return false;
        }

        if ($this->getApiSecurityKey() == null || $this->getApiSecurityKey() == "") {
            return false;
        }

        if ($request->header(self::API_AUTH_BYPASS_TOKEN_HEADER) != $this->getApiSecurityKey()) {
            return false;
        }

        return true;
    }


    /**
     * Get Token from config ..
     * @return null|string
     */
    public function getApiSecurityKey(): ?string
    {
        $key = config("mail.apiSecurityKey");

        return isset($key) ? $key : null;
    }
}
