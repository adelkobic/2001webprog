<?php

function request_body() {
    $request = Flight::request();
    if (method_exists($request, 'getBody')) {
        $raw = $request->getBody();
        if (is_string($raw) && $raw !== '') {
            $json = json_decode($raw, true);
            if (is_array($json)) {
                return $json;
            }
        }
    }
    $data = $request->data;
    if (is_object($data) && method_exists($data, 'getData')) {
        $body = $data->getData();
        if (is_array($body)) {
            return $body;
        }
    }
    return [];
}

function json_exit($data, $code) {
    if (!headers_sent()) {
        http_response_code($code);
        header('Content-Type: application/json');
        $config = Flight::get('config');
        if (is_array($config) && !empty($config['frontend_url'])) {
            header('Access-Control-Allow-Origin: ' . $config['frontend_url']);
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        }
    }
    echo json_encode($data);
    exit;
}

function query_params() {
    $query = Flight::request()->query;
    if (is_object($query) && method_exists($query, 'getData')) {
        $params = $query->getData();
        if (is_array($params)) {
            return $params;
        }
    }
    return [];
}

function send_json($data, $code = 200) {
    Flight::json($data, $code);
}

function current_user() {
    $user = Flight::get('user');
    if (!$user) {
        throw new AppException('Please log in.', 401);
    }
    return $user;
}

function public_group($callback) {
    Flight::group('/api', $callback, [new CorsMiddleware()]);
}

function auth_group($callback) {
    Flight::group('/api', $callback, [new AuthMiddleware()]);
}

function role_group($roles, $callback) {
    Flight::group('/api', $callback, [
        new AuthMiddleware(),
        new RoleMiddleware($roles),
    ]);
}
