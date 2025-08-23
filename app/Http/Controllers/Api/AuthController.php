<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function __construct()
    {
        // allow login + register without auth, everything else requires JWT
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        $user = \App\Models\User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    /**
     * Login and get JWT
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $ttl = auth()->factory()->getTTL() * 60; // in seconds

        return response()
            ->json(['message' => 'Login successful'])
            ->cookie(
                'token',                           // cookie name
                $token,                            // JWT token
                $ttl / 60,                         // minutes
                '/',                               // path
                null,                              // domain (null = current)
                true,                              // secure (use HTTPS in production)
                true,                              // httpOnly
                false,                             // raw
                'Strict'                           // sameSite: Strict / Lax / None
            );
    }


    // public function me()
    // {
    //     return response()->json($this->guard()->user());
    // }

    public function me(Request $request)
    {
        // Using the user attached by middleware
        $user = $request->auth_user;

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }


    public function logout()
    {
        $this->guard()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    protected function guard()
    {
        return Auth::guard();
    }
}


// https://chatgpt.com/share/68a9822a-9218-800b-bef0-f0bfc6dd4210