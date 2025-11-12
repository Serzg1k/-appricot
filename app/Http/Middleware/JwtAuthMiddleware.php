<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class JwtAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization', '');
        if (!preg_match('/^Bearer\s+(.+)$/i', $header, $m)) {
            return response()->json(['status' => 'failure'], 401);
        }

        $token = $m[1] ?? null;
        if (!$token) {
            return response()->json(['status' => 'failure'], 401);
        }

        try {
            $secret = (string) config('jwt.secret', 'your-256-bit-secret');
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            $request->attributes->set('jwt', $decoded);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'failure'], 401);
        }

        return $next($request);
    }
}
