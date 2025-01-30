<?php
require_once __DIR__ . '/../controllers/ApiController.php';
$router = Router::getInstance();

// api endpoint
$router->get('/api/events', [ApiController::class, 'getAllEvents']);