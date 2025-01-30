<?php
require_once __DIR__ . '/../controllers/AuthController.php';

$router = Router::getInstance();

// auth routes
$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->get('/register', [AuthController::class, 'showRegistrationForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/register', [AuthController::class, 'register']);

// Authenticated routes
$router->get('/logout', [AuthController::class, 'logout']);

// manage events routes
$router->get('/events', [EventController::class, 'index']);
$router->get('/events/create', [EventController::class, 'create']);
$router->post('/events', [EventController::class, 'store']);
$router->get('/events/edit/:id', [EventController::class, 'edit']);
$router->post('/events/:id', [EventController::class, 'update']);
$router->post('/events/delete/:id', [EventController::class, 'delete']);
$router->get('/events/show/:id', [EventController::class, 'show']);
$router->get('/events/:id/download-attendees', [EventController::class, 'downloadAttendees']);