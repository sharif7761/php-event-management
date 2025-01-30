<?php
require_once __DIR__ . '/BaseMiddleware.php';

class AuthMiddleware extends BaseMiddleware {
    public function handle($next) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please login to access this page';
            header('Location: /login');
            exit;
        }
        return $next();
    }
}