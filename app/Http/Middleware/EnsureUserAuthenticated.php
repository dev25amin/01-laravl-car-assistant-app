<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // تحقق من أن المستخدم مسجل دخول عبر API Token
        if (! $request->user()) {
            return response()->json([
                'message' => 'غير مسجل الدخول ❌'
            ], 401);
        }

        return $next($request);
    }
}
