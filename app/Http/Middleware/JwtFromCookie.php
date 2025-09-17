<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtFromCookie
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // 1️⃣ Prefer Authorization header if present, else extract from cookies
            $authHeader = $request->headers->get('Authorization');
            $token = null;
            if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $m)) {
                $token = $m[1];
            } else {
                // new cookie name first, then fallback to legacy name
                $token = $request->cookie('jwt_access') ?: $request->cookie('access_token');
                if (!$token) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthenticated: no access token',
                    ], 401);
                }
            }

            // 2️⃣ Ensure Authorization header is set for JWT parsing if not present
            if (!$authHeader) {
                $request->headers->set('Authorization', 'Bearer ' . $token);
            }

            // 3️⃣ Authenticate token
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated: invalid token',
                ], 401);
            }

            // 4️⃣ Ensure this is an access token, not refresh
            $payload = JWTAuth::setToken($token)->getPayload();
            if ($payload->get('type') !== 'access') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token type: refresh token not allowed here',
                ], 401);
            }

            // 5️⃣ Attach user to request for controllers
            $request->merge(['auth_user' => $user]);

            return $next($request);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Access token expired, please refresh',
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Access token invalid',
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }
}
