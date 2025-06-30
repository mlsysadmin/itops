<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
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
        // If the user is not authenticated, redirect to login
        if (!Auth::check()) {
            return redirect('/');
        }

        // If the user is authenticated but not an admin, redirect to another page
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Forbidden');
        }

        // Allow admin users to proceed
        return $next($request);
    }
}
