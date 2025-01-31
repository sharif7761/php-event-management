<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../lib/ErrorHandler.php';

class HomeController extends BaseController {
    private $db;
    private $itemsPerPage = 5;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->validator = new Validator();
    }
    public function index() {
      try {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'event_datetime';
        $order = $_GET['order'] ?? '';

        $allowedSortFields = ['name', 'max_capacity'];
        $sort = in_array($sort, $allowedSortFields) ? $sort : 'event_datetime';
        $order = in_array(strtoupper($order), ['ASC', 'DESC']) ? strtoupper($order) : '';

        $offset = ($page - 1) * $this->itemsPerPage;
        $currentDateTime = date('Y-m-d H:i:s');
        $searchParam = "%{$search}%";

        $countQuery = "
            SELECT COUNT(*) as total 
            FROM events e
            WHERE e.event_datetime >= ?
            AND (e.name LIKE ? OR e.description LIKE ?)
        ";

        $countStmt = $this->db->prepare($countQuery);
        $countStmt->execute([$currentDateTime, $searchParam, $searchParam]);
        $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        $query = "
            SELECT 
                e.*,
                u.name as creator_name,
                (SELECT COUNT(*) FROM event_attendees WHERE event_id = e.id) as attendees_count
            FROM events e
            JOIN users u ON e.user_id = u.id
            WHERE e.event_datetime >= ?
            AND (e.name LIKE ? OR e.description LIKE ?)
            ORDER BY " . $sort . " " . $order . "
            LIMIT " . (int)$this->itemsPerPage . " OFFSET " . (int)$offset;

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            $currentDateTime,
            $searchParam,
            $searchParam
        ]);

        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalPages = max(1, ceil($totalCount / $this->itemsPerPage));

        $page = min($page, $totalPages);

        return $this->view('home/index', [
            'title' => 'Upcoming Events',
            'events' => $events,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'sort' => $sort,
            'order' => $order
        ]);

        } catch (PDOException $e) {
          ErrorHandler::handleError("An error occurred while fetching events. Please try again later.");
        }
    }

    public function showForm($eventId) {
        try {
            $stmt = $this->db->prepare("
                SELECT e.*, 
                       (SELECT COUNT(*) FROM event_attendees WHERE event_id = e.id) as attendees_count
                FROM events e
                WHERE e.id = ? AND e.event_datetime > NOW()
            ");
            $stmt->execute([$eventId]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                ErrorHandler::handleError("Event not found or has already passed", '/events');
                return;
            }

            $this->view('home/event-register', [
                'title' => 'Register for Event',
                'event' => $event
            ]);

        } catch (PDOException $e) {
            ErrorHandler::handleError("An error occurred. Please try again.", '/events');
        }
    }

    public function register() {
        // Only accept Ajax requests
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid request method']);
            return;
        }

        $rules = [
            'event_id' => ['required' => true],
            'attendee_name' => ['required' => true, 'min' => 2],
            'attendee_email' => ['required' => true, 'email' => true]
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            http_response_code(422);
            echo json_encode(['error' => $this->validator->getErrors()]);
            return;
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                SELECT e.max_capacity, 
                       (SELECT COUNT(*) FROM event_attendees WHERE event_id = ?) as current_attendees 
                FROM events e 
                WHERE e.id = ?
            ");
            $stmt->execute([$_POST['event_id'], $_POST['event_id']]);
            $eventData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$eventData) {
                throw new Exception('Event not found');
            }

            // Check capacity
            if ($eventData['current_attendees'] >= $eventData['max_capacity']) {
                throw new Exception('Event has reached maximum capacity');
            }

            // Check if email already registered
            $stmt = $this->db->prepare("
                SELECT id FROM event_attendees 
                WHERE event_id = ? AND attendee_email = ?
            ");
            $stmt->execute([$_POST['event_id'], $_POST['attendee_email']]);
            if ($stmt->fetch()) {
                throw new Exception('You have already registered for this event');
            }

            // Register attendee
            $stmt = $this->db->prepare("
                INSERT INTO event_attendees (event_id, attendee_name, attendee_email) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $_POST['event_id'],
                $_POST['attendee_name'],
                $_POST['attendee_email']
            ]);

            $this->db->commit();
            echo json_encode(['success' => 'Registration successful']);

        } catch (Exception $e) {
            $this->db->rollBack();
            http_response_code(422);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}