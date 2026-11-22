<?php

class CorsMiddleware {
    public function before($params) {
        $config = Flight::get('config');
        if (!empty($config['frontend_url'])) {
            header('Access-Control-Allow-Origin: ' . $config['frontend_url']);
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        }
    }
}
