<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AutoLoginDemoUser
{
    /**
     * For demo: if not authenticated, log in as siena@admin.com so the dashboard shows without a login form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            return $next($request);
        }

        $user = User::where('email', 'siena@admin.com')->first();
        if ($user) {
            Auth::login($user);
        }

        return $next($request);
    }
}
