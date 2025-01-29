<?php
require_once __DIR__ . '/../controllers/AuthController.php';

$router = Router::getInstance();

// auth routes
$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->get('/register', [AuthController::class, 'showRegistrationForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/register', [AuthController::class, 'register']);