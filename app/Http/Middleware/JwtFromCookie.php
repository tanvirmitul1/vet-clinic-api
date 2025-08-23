<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtFromCookie
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // 1️⃣ Extract token from cookie
            $token = $request->cookie('token');
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated: no token',
                ], 401);
            }

            // 2️⃣ Set token in Authorization header (required by JWTAuth)
            $request->headers->set('Authorization', 'Bearer ' . $token);

            // 3️⃣ Authenticate token
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated: invalid token',
                ], 401);
            }

            // 4️⃣ Attach user to request for controllers
            $request->merge(['auth_user' => $user]);

            return $next($request);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token expired',
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token invalid',
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }
}
