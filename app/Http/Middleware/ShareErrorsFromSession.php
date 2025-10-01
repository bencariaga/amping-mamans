<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Request;
use Illuminate\View\Factory as ViewFactory;

class ShareErrorsFromSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        ViewFactory::share('errors', $request->session()->get('errors', new MessageBag));

        return $next($request);
    }
}
