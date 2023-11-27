<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddTrailingSlash
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        if (!str_ends_with($uri, '/')) {
            $redirect = $request->url() . '/';
            if ($request->getQueryString()) {
                $redirect .= '?' . $request->getQueryString();
            }

            return redirect()->to($redirect, 301);
        }

        return $next($request);
    }
}
