<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = \App\Models\User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'user'    => $user
        ], 201);
    }

    /**
     * Login and get JWT
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Issue access token with explicit type claim
        if (!$token = JWTAuth::claims(['type' => 'access'])->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $accessTtl = JWTAuth::factory()->getTTL(); // default minutes
        $refreshTtl = 60 * 24 * 7; // 7 days (in minutes)

        // Temporarily set TTL on the factory to issue a longer-lived refresh token
        $originalTtl = JWTAuth::factory()->getTTL();
        JWTAuth::factory()->setTTL($refreshTtl);
        $refreshToken = JWTAuth::claims(['type' => 'refresh'])
            ->fromUser(Auth::user());
        // Restore original TTL for access tokens
        JWTAuth::factory()->setTTL($originalTtl);

        return response()
            ->json([
                'message'       => 'Login successful',
                'access_token'  => $token,
                'expires_in'    => $accessTtl * 60,
                'refresh_token' => $refreshToken,
                'refresh_expires_in' => $refreshTtl * 60
            ])
            ->cookie('jwt_access', $token, $accessTtl, '/', null, true, true, false, 'Strict')
            ->cookie('access_token', $token, $accessTtl, '/', null, true, true, false, 'Strict')
            ->cookie('refresh_token', $refreshToken, $refreshTtl, '/', null, true, true, false, 'Strict');
    }

    public function refresh(Request $request)
    {
        try {
            $refreshToken = $request->cookie('refresh_token');
            if (!$refreshToken) {
                return response()->json(['error' => 'No refresh token'], 401);
            }

            // Parse refresh token
            $payload = JWTAuth::setToken($refreshToken)->getPayload();
            if ($payload->get('type') !== 'refresh') {
                return response()->json(['error' => 'Invalid token type for refresh'], 401);
            }

            $user = JWTAuth::setToken($refreshToken)->authenticate();

            if (!$user) {
                return response()->json(['error' => 'Invalid refresh token'], 401);
            }

            // Issue new access token
            $newAccessToken = JWTAuth::claims(['type' => 'access'])->fromUser($user);

            return response()
                ->json([
                    'access_token' => $newAccessToken,
                    'token_type'   => 'bearer',
                    'expires_in'   => JWTAuth::factory()->getTTL() * 60,
                ])
                ->cookie('jwt_access', $newAccessToken, JWTAuth::factory()->getTTL(), '/', null, true, true, false, 'Strict')
                ->cookie('access_token', $newAccessToken, JWTAuth::factory()->getTTL(), '/', null, true, true, false, 'Strict');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Refresh failed'], 401);
        }
    }

    public function me(Request $request)
    {
        $user = $request->get('auth_user'); // set by JwtFromCookie middleware

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }



    public function logout(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            if ($token) {
                JWTAuth::invalidate($token);
            }
            return response()
                ->json(['message' => 'Successfully logged out'])
                ->cookie('jwt_access', '', -1)
                ->cookie('access_token', '', -1)
                ->cookie('refresh_token', '', -1);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to logout'], 500);
        }
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => JWTAuth::factory()->getTTL() * 60
        ]);
    }

    protected function guard()
    {
        return Auth::guard();
    }
}


// https://chatgpt.com/share/68a9822a-9218-800b-bef0-f0bfc6dd4210