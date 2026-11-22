<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    public function before($params) {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $authorization = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        if (!preg_match('/^Bearer\s+(.+)$/i', trim($authorization), $matches)) {
            throw new AppException('Please log in.', 401);
        }

        try {
            $config = Flight::get('config');
            $payload = JWT::decode($matches[1], new Key($config['jwt_secret'], 'HS256'));
            Flight::set('user', (array) $payload);
        } catch (Throwable $error) {
            throw new AppException('Your login session is invalid or has expired.', 401);
        }
    }
}
