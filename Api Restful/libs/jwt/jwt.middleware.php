<?php

require_once __DIR__ . '/jwt.php';

class JWTMiddleware extends Middleware {
    public function run($request, $response) {
        $auth_header = $request->authorization ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? ''));
        if (!$auth_header) {
            return;
        }
        $parts = explode(' ', $auth_header);
        if (count($parts) != 2) {
            return;
        }
        if ($parts[0] !== 'Bearer') {
            return;
        }
        $jwt = $parts[1];
        $user = validateJWT($jwt);
        if ($user) {
            $request->user = $user;
        }
        return;
    }
}