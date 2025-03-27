<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        Log::info('RoleMiddleware executed', ['user_id' => $user ? $user->id : 'guest', 'roles' => $roles]);
    
        if (!$user || !in_array($user->role, $roles)) {
            return response()->json(['message' => 'Forbidden. Admins only.'], 403);
        }
    
        return $next($request);
}
}
