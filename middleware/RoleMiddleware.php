<?php
class RoleMiddleware {
    public static function isAdmin() {
        return $_SESSION['role'] === 'admin';
    }

    public static function authorizeEvent($eventId, $db) {
        if (self::isAdmin()) {
            return true;
        }

        $stmt = $db->prepare("SELECT id FROM events WHERE id = ? AND user_id = ?");
        $stmt->execute([$eventId, $_SESSION['user_id']]);
        return $stmt->fetch() ? true : false;
    }
}