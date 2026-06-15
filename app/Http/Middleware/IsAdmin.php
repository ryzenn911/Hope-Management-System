<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }
        return redirect('/')->withErrors(['error' => 'អ្នកគ្មានសិទ្ធិចូលទៅកាន់ទំព័រ Admin ឡើយ!']);
    }
}
