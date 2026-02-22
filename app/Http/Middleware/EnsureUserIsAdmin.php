<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 確保已登入且為後台管理員
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->is_admin) {
            return redirect()->route('admin.login')->with('error', '請先以管理員身分登入。');
        }

        return $next($request);
    }
}
