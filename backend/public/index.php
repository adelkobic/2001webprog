<?php

require __DIR__ . '/../../vendor/autoload.php';

$configFile = __DIR__ . '/../config/config.php';
if (!file_exists($configFile)) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'message' => 'Copy backend/config/config.example.php to backend/config/config.php.',
    ]);
    exit;
}

$config = require $configFile;
Flight::set('config', $config);

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    header('Access-Control-Allow-Origin: ' . $config['frontend_url']);
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    http_response_code(204);
    exit;
}

spl_autoload_register(function ($class) {
    foreach (['dao', 'services', 'middleware'] as $folder) {
        $path = __DIR__ . '/../' . $folder . '/' . $class . '.php';
        if (file_exists($path)) {
            require $path;
            return;
        }
    }
});

require __DIR__ . '/../functions.php';

Flight::map('error', function (Throwable $error) {
    $status = $error instanceof AppException ? $error->status : 500;
    $message = $error instanceof AppException ? $error->getMessage() : 'Something went wrong.';

    if (!($error instanceof AppException)) {
        $detail = $error->getMessage() . ' (' . basename($error->getFile()) . ':' . $error->getLine() . ')';
        error_log($detail);
        try {
            Logger::error($detail);
        } catch (Throwable $ignored) {
        }
    }

    json_exit(['message' => $message], $status);
});

Flight::map('notFound', function () {
    json_exit(['message' => 'That page was not found.'], 404);
});

require __DIR__ . '/../routes/auth.php';
require __DIR__ . '/../routes/genres.php';
require __DIR__ . '/../routes/books.php';
require __DIR__ . '/../routes/users.php';
require __DIR__ . '/../routes/rentals.php';
require __DIR__ . '/../routes/reviews.php';
require __DIR__ . '/../routes/docs.php';

Flight::start();
