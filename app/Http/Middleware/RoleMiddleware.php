<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

//assign role to user
class RoleMiddleware
{
    /**
     * @param  Request   $request  
     * @param  Closure   $next     
     * @param  string ...$roles    
     *                             
     *                             
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
            abort(403, 'Unauthorized.');
        }
        return $next($request);
    }
}
