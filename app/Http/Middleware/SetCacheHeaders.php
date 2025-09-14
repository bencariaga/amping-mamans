<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $cacheControl
     * @param  string|null  $etag
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $cacheControl = 'no-cache, no-store, max-age=0, must-revalidate', $etag = null): Response
    {
        $response = $next($request);

        if ($cacheControl) {
            $response->headers->set('Cache-Control', $cacheControl);
        }

        if ($etag !== null) {
            $response->setEtag($etag);
        }

        return $response;
    }
}
