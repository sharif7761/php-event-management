<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/EventController.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

$router = Router::getInstance();

// auth routes
$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->get('/register', [AuthController::class, 'showRegistrationForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/register', [AuthController::class, 'register']);

// Authenticated routes
$router->get('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);

// manage events routes
$router->get('/events', [EventController::class, 'index'], [AuthMiddleware::class]);
$router->get('/events/create', [EventController::class, 'create'], [AuthMiddleware::class]);
$router->post('/events', [EventController::class, 'store'], [AuthMiddleware::class]);
$router->get('/events/edit/:id', [EventController::class, 'edit'], [AuthMiddleware::class]);
$router->post('/events/:id', [EventController::class, 'update'], [AuthMiddleware::class]);
$router->post('/events/delete/:id', [EventController::class, 'delete'], [AuthMiddleware::class]);
$router->get('/events/show/:id', [EventController::class, 'show'], [AuthMiddleware::class]);
$router->get('/events/:id/download-attendees', [EventController::class, 'downloadAttendees'], [AuthMiddleware::class]);