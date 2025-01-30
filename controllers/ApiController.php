<?php
require_once __DIR__ . '/../lib/Database.php';

class ApiController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    private function jsonResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function errorResponse($message, $status = 400)
    {
        $this->jsonResponse(['error' => $message], $status);
    }

    public function getAllEvents()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    e.id as event_id,
                    e.name as event_name,
                    e.description,
                    e.event_datetime,
                    e.max_capacity,
                    u.name as created_by
                FROM events e
                JOIN users u ON e.user_id = u.id
                ORDER BY e.event_datetime DESC
            ");
            $stmt->execute();
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $formattedEvents = array_map(function ($event) {
                $stmt = $this->db->prepare("
                    SELECT 
                        id as attendee_id,
                        attendee_name,
                        attendee_email
                    FROM event_attendees 
                    WHERE event_id = ?
                    ORDER BY created_at DESC
                ");
                $stmt->execute([$event['event_id']]);
                $attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Format event data
                return [
                    'event_id' => (int)$event['event_id'],
                    'event_name' => $event['event_name'],
                    'description' => $event['description'],
                    'event_datetime' => $event['event_datetime'],
                    'max_capacity' => (int)$event['max_capacity'],
                    'created_by' => $event['created_by'],
                    'attendees' => $attendees
                ];
            }, $events);

            $this->jsonResponse($formattedEvents);

        } catch (PDOException $e) {
            $this->errorResponse('Database error', 500);
        } catch (Exception $e) {
            $this->errorResponse('Server error', 500);
        }
    }
}