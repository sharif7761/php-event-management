<?php

class ErrorHandler {
    public static function handleError($message, $redirectTo = '/', $code = 500) {
        $_SESSION['error'] = $message;
        header("HTTP/1.1 " . $code);
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? $redirectTo;
        header("Location: " . $redirectUrl);
        exit;
    }
}