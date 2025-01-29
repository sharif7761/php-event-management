<?php
session_start();

require_once __DIR__ . '/../lib/Router.php';
require_once __DIR__ . '/../routes/web.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

try {
    Router::getInstance()->dispatch($method, $uri);
} catch (Exception $e) {
    http_response_code((int)$e->getCode() ?: 500);
    require_once __DIR__ . '/../views/errors/error.php';
}