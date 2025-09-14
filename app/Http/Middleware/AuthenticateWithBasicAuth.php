<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth as Middleware;
use Closure;

class AuthenticateWithBasicAuth extends Middleware
{
    /**
     * Handle an incoming request with HTTP Basic Auth.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @param  string|null  $field
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null, $field = null)
    {
        return parent::handle($request, $next, $guard, $field);
    }
}
