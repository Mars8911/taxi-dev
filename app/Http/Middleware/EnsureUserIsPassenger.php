<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 確保已登入且為乘客
 */
class EnsureUserIsPassenger
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || auth()->user()->role !== 'passenger') {
            return redirect()->route('passenger.login')->with('error', '請先以乘客身分登入。');
        }

        return $next($request);
    }
}
