<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureNotSuspended
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && strtolower(trim(auth()->user()->status ?? 'active')) === 'suspended') {
            $suspendedUser = auth()->user();
            $reason = $suspendedUser->suspension_reason ?: 'No reason was provided.';

            auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')
                ->with('error', 'Your account has been suspended. Reason: ' . $reason)
                ->with('suspension_user_id', $suspendedUser->id);
        }

        return $next($request);
    }
}
