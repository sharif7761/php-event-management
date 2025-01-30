<?php
class EventHelper {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getEventQuery($searchParam, $userId = null) {
        $params = [];
        $sql = "SELECT e.*, 
                       u.name as creator_name,
                       (SELECT COUNT(*) FROM event_attendees WHERE event_id = e.id) as attendees_count 
                FROM events e 
                JOIN users u ON e.user_id = u.id
                WHERE e.name LIKE ?";

        $params[] = $searchParam;

        if ($userId) {
            $sql .= " AND e.user_id = ?";
            $params[] = $userId;
        }

        $sql .= " ORDER BY e.event_datetime DESC";

        return [
            'sql' => $sql,
            'params' => $params
        ];
    }

    public function getEventDetails($eventId, $userId = null) {
        $params = [$eventId];
        $sql = "SELECT e.*, u.name as creator_name 
                FROM events e
                JOIN users u ON e.user_id = u.id
                WHERE e.id = ?";

        if ($userId) {
            $sql .= " AND e.user_id = ?";
            $params[] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
}