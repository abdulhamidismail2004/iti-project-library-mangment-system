<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }
        
        if (auth()->user()->usertype !== 'student') {
            abort(403, 'Access denied. Students only.');
        }
        
        return $next($request);
    }
}