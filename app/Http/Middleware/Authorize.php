<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $ability
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function handle(Request $request, Closure $next, $ability, ...$models): Response
    {
        if (! $request->user() || ! $request->user()->can($ability, $models)) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        return $next($request);
    }
}
