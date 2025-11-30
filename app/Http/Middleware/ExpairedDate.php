<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpairedDate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if($user->expires_at <= now()){
            $user->update([
                'expires_at' => now()->addMonth()
            ]);
            return $next($request);
        }
        return response()->json([
            'message' => 'Your session has expired. Please login again.',
            'status' => 401,
        ], 401);
    }
}
