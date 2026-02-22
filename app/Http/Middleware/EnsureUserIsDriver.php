<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 確保已登入且為司機
 */
class EnsureUserIsDriver
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || auth()->user()->role !== 'driver') {
            return redirect()->route('driver.login')->with('error', '請先以司機身分登入。');
        }

        return $next($request);
    }
}
