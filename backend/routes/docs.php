<?php

Flight::route('GET /openapi.yaml', function () {
    $yaml = file_get_contents(__DIR__ . '/../../openapi.yaml');
    header('Content-Type: application/yaml');
    header('Access-Control-Allow-Origin: ' . Flight::get('config')['frontend_url']);
    echo $yaml;
});
