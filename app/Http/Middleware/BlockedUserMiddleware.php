<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockedUserMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->user()?->is_blocked) {
            abort(403, 'This account is blocked.');
        }

        return $next($request);
    }
}
