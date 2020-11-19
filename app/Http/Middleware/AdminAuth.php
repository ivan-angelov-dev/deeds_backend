<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $admin = session()->get('admin');
        if (isset($admin)) {
            return $next($request);
        }
        session()->flash('error_msg', 'Please login first');

        return redirect()->route('auth.login');
    }
}
