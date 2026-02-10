<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Servir fichiers statiques (css, js, images, html)
$file = __DIR__ . $path;
if ($path !== '/' && file_exists($file)) {
    return false;
}

// Routes API
if (str_starts_with($path, '/api')) {
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    require __DIR__ . '/api.php';
    exit;
}

// HTML par défaut
//require __DIR__ . '/index.html';
