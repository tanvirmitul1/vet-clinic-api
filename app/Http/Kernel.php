<?php

protected $routeMiddleware = [
    // ... other middleware
    'jwt.cookie' => \App\Http\Middleware\JwtFromCookie::class,
];
