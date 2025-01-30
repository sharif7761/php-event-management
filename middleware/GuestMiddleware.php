<?php
require_once __DIR__ . '/BaseMiddleware.php';

class GuestMiddleware extends BaseMiddleware {
    public function handle($next) {
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        return $next();
    }
}