## Vet Clinic API Documentation

This document describes the available REST endpoints, authentication, cookies/headers, and practical integration examples for a Next.js frontend.

### Base URL
- Replace `YOUR_DOMAIN` and port as appropriate.
- Local development usually serves the API at `http://localhost:8000` (Laravel `php artisan serve`).

- **Base**: `http://YOUR_DOMAIN[:PORT]/api`

### Authentication Overview
- The API uses JWT-based auth with two tokens:
  - **Access token**: short-lived, stored as an HTTP-only cookie named `jwt_access` (legacy: `access_token`).
  - **Refresh token**: long-lived, stored as an HTTP-only cookie named `refresh_token`.
- Middleware `jwt.cookie` authenticates protected endpoints by reading the `Authorization: Bearer <token>` header or, if absent, the `jwt_access` cookie (fallback to `access_token`).
- Access token includes claim `type=access`. Refresh token includes claim `type=refresh`.

Cookie properties (per server response):
- `httpOnly: true`, `secure: true` (use HTTPS in production), `sameSite: 'Strict'`.

### Endpoints

#### POST /api/auth/register
- **Description**: Register a new user.
- **Body (JSON or form)**:
  - `name` (string, required)
  - `email` (string, required, unique)
  - `password` (string, required, min 6)
  - `password_confirmation` (string, must match password)
- **Responses**:
  - 201 Created
    ```json
    {
      "success": true,
      "message": "User registered successfully",
      "user": { "id": 1, "name": "...", "email": "...", ... }
    }
    ```
  - 422 Validation error
    ```json
    { "success": false, "message": "Validation errors", "errors": { ... } }
    ```

#### POST /api/auth/login
- **Description**: Authenticate user and receive tokens.
- **Body (JSON or form)**:
  - `email` (string, required)
  - `password` (string, required)
- **Responses**:
  - 200 OK, sets cookies `jwt_access`, `access_token` (legacy), and `refresh_token`.
    ```json
    {
      "message": "Login successful",
      "access_token": "<JWT>",
      "expires_in": <seconds>,
      "refresh_token": "<JWT>",
      "refresh_expires_in": <seconds>
    }
    ```
  - 401 Unauthorized: `{ "error": "Unauthorized" }`

#### POST /api/auth/refresh
- **Description**: Issue a new access token using the `refresh_token` cookie.
- **Auth**: No access token required. Requires `refresh_token` cookie with `type=refresh`.
- **Responses**:
  - 200 OK, sets new `jwt_access` (and legacy `access_token`) cookie.
    ```json
    { "access_token": "<JWT>", "token_type": "bearer", "expires_in": <seconds> }
    ```
  - 401 Unauthorized (missing/invalid/incorrect-type refresh token): `{ "error": "..." }`

#### GET /api/auth/me
- **Description**: Return the authenticated user.
- **Auth**: Requires `jwt_access` cookie (or `Authorization: Bearer <access token>` header).
- **Responses**:
  - 200 OK
    ```json
    { "success": true, "user": { "id": 1, "name": "...", "email": "...", ... } }
    ```
  - 401 Unauthorized (no access token / invalid / refresh token used):
    ```json
    { "success": false, "message": "Unauthenticated" }
    ```
    or
    ```json
    { "success": false, "message": "Invalid token type: refresh token not allowed here" }
    ```

#### POST /api/auth/logout
- **Description**: Invalidate the current access token and clear auth cookies.
- **Auth**: Requires current access token (via header or cookie).
- **Responses**:
  - 200 OK: `{ "message": "Successfully logged out" }`
  - 500 Internal error: `{ "error": "Failed to logout" }`

### Common Error Responses
- 401 Unauthenticated
  - `{ "success": false, "message": "Unauthenticated: no access token" }`
  - `{ "success": false, "message": "Access token expired, please refresh" }`
  - `{ "success": false, "message": "Access token invalid" }`
  - `{ "success": false, "message": "Invalid token type: refresh token not allowed here" }`
- 422 Validation errors
- 500 Server errors

### Integration Notes (Next.js)

Use `fetch` with `credentials: 'include'` so cookies are sent/received. Prefer HTTPS in production to satisfy `secure` cookies.

Example: login action (server action or client component)
```ts
// POST /api/auth/login
await fetch(process.env.NEXT_PUBLIC_API_BASE + '/auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  credentials: 'include',
  body: JSON.stringify({ email, password })
});
```

Example: get current user
```ts
// GET /api/auth/me
const res = await fetch(process.env.NEXT_PUBLIC_API_BASE + '/auth/me', {
  method: 'GET',
  credentials: 'include'
});
if (res.status === 401) {
  // try refresh
  const r = await fetch(process.env.NEXT_PUBLIC_API_BASE + '/auth/refresh', {
    method: 'POST',
    credentials: 'include'
  });
  if (r.ok) {
    // retry me
    return fetch(process.env.NEXT_PUBLIC_API_BASE + '/auth/me', {
      credentials: 'include'
    });
  }
}
```

Optional: using Authorization header instead of cookie
```ts
const token = /* store access token from login if you want header-based auth */
const res = await fetch(process.env.NEXT_PUBLIC_API_BASE + '/auth/me', {
  headers: { Authorization: `Bearer ${token}` },
  credentials: 'include' // keep for refresh flows/cookie sync
});
```

### CORS & Cookies
- Ensure your Next.js origin is allowed in `config/cors.php` and that `supports_credentials` is enabled.
- When using `secure` cookies locally, prefer `https://localhost` or set `secure` to `false` in non-HTTPS dev environments if necessary.

### Notes & Conventions
- All auth endpoints are under `/api/auth/*`.
- Responses use JSON. Errors include simple `error` or `{ success: false, message }` shapes.
- Legacy cookie `access_token` is currently also set for backward compatibility; prefer `jwt_access`.

### Roadmap (placeholders)
Migrations and models exist for domain resources (owners, pets, treatments, appointments, billing, roles), but routes/controllers are not exposed yet. Once added, extend this document with:
- `GET/POST/PUT/DELETE` routes and bodies
- Validation rules
- Pagination & filtering patterns



