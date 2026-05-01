<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->two_factor_enabled && $user->tokenCan('2fa-verify')) {
            return response()->json([
                'success' => false,
                'message' => '2FA verification required',
            ], 403);
        }

        return $next($request);
    }
}