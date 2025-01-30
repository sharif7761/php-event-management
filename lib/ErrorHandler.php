<?php

class ErrorHandler {
    public static function handleError($message, $code = 500) {
        $_SESSION['error'] = $message;
        header("HTTP/1.1 " . $code);
//        header("Location: /");
        exit;
    }
}