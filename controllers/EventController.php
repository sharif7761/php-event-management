<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../lib/Validator.php';
require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../lib/EventHelper.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../lib/ErrorHandler.php';

class EventController extends BaseController {
    private $db;
    private $validator;
    private $eventHelper;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->validator = new Validator();
        $this->eventHelper = new EventHelper($this->db);
    }

    public function index() {
        try {
            $search = $_GET['search'] ?? '';
            $searchParam = "%{$search}%";

            $query = $this->eventHelper->getEventQuery(
                $searchParam,
                RoleMiddleware::isAdmin() ? null : $_SESSION['user_id']
            );

            $stmt = $this->db->prepare($query['sql']);
            $stmt->execute($query['params']);
            $events = $stmt->fetchAll();

            $this->view('events/index', [
                'title' => RoleMiddleware::isAdmin() ? 'All Events' : 'My Events',
                'events' => $events,
                'search' => $search,
                'isAdmin' => RoleMiddleware::isAdmin()
            ]);
        } catch (PDOException $e) {
            ErrorHandler::handleError("Error fetching events.");
        }
    }

    public function create() {
        $this->view('events/create', [
            'title' => 'Create Event'
        ]);
    }
    public function store() {
        $rules = [
            'name' => ['required' => true, 'min' => 3],
            'description' => ['required' => true, 'min' => 10],
            'max_capacity' => ['required' => true],
            'event_datetime' => ['required' => true]
        ];
        if (!$this->validator->validate($_POST, $rules)) {
            $_SESSION['errors'] = $this->validator->getErrors();
            $_SESSION['old'] = $_POST;
            $this->redirect('/events/create');
            return;
        }
        try {
            $stmt = $this->db->prepare("
                INSERT INTO events (name, description, max_capacity, event_datetime, user_id) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['max_capacity'],
                $_POST['event_datetime'],
                $_SESSION['user_id']
            ]);
            $_SESSION['success'] = "Event created successfully";
            $this->redirect('/events');
        } catch (PDOException $e) {
            ErrorHandler::handleError("Error creating event.");
        }
    }

    public function edit($id) {
        try {
            if (!RoleMiddleware::authorizeEvent($id, $this->db)) {
                ErrorHandler::handleError("Access denied", '/events');
                return;
            }

            $event = $this->eventHelper->getEventDetails(
                $id,
                RoleMiddleware::isAdmin() ? null : $_SESSION['user_id']
            );

            if (!$event) {
                ErrorHandler::handleError("Access denied", '/events');
                return;
            }

            $this->view('events/edit', [
                'title' => 'Edit Event',
                'event' => $event,
                'isAdmin' => RoleMiddleware::isAdmin()
            ]);
        } catch (PDOException $e) {
            ErrorHandler::handleError("Error fetching event.");
        }
    }

    public function update($id) {
        try {
            if (!RoleMiddleware::authorizeEvent($id, $this->db)) {
                ErrorHandler::handleError("Access denied", '/events');
                return;
            }

            $rules = [
                'name' => ['required' => true, 'min' => 3],
                'description' => ['required' => true, 'min' => 10],
                'max_capacity' => ['required' => true],
                'event_datetime' => ['required' => true]
            ];

            if (!$this->validator->validate($_POST, $rules)) {
                $_SESSION['errors'] = $this->validator->getErrors();
                $_SESSION['old'] = $_POST;
                $this->redirect("/events/edit/$id");
                return;
            }

            $stmt = $this->db->prepare("
                UPDATE events 
                SET name = ?, description = ?, max_capacity = ?, event_datetime = ? 
                WHERE id = ?
            ");
            $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['max_capacity'],
                $_POST['event_datetime'],
                $id
            ]);

            $_SESSION['success'] = "Event updated successfully";
            $this->redirect('/events');
        } catch (PDOException $e) {
            ErrorHandler::handleError("Error updating event.");
        }
    }

    public function delete($id) {
        try {
            if (!RoleMiddleware::authorizeEvent($id, $this->db)) {
                ErrorHandler::handleError("Access denied.", '/events');
                return;
            }

            $stmt = $this->db->prepare("DELETE FROM events WHERE id = ?");
            $stmt->execute([$id]);

            $_SESSION['success'] = "Event deleted successfully";
        } catch (PDOException $e) {
            ErrorHandler::handleError("Error deleting event.");
        }
        $this->redirect('/events');
    }

    public function show($id) {
        try {
            if (!RoleMiddleware::authorizeEvent($id, $this->db)) {
                ErrorHandler::handleError("Access denied.", '/events');
                return;
            }

            $event = $this->eventHelper->getEventDetails(
                $id,
                RoleMiddleware::isAdmin() ? null : $_SESSION['user_id']
            );

            if (!$event) {
                ErrorHandler::handleError("Event not found.", '/events');
                return;
            }

            $search = $_GET['search'] ?? '';
            $searchParam = "%{$search}%";

            $stmt = $this->db->prepare("
                SELECT * FROM event_attendees 
                WHERE event_id = ? 
                AND (attendee_name LIKE ? OR attendee_email LIKE ?)
                ORDER BY created_at DESC
            ");
            $stmt->execute([$id, $searchParam, $searchParam]);
            $attendees = $stmt->fetchAll();

            $this->view('events/show', [
                'title' => $event['name'],
                'event' => $event,
                'attendees' => $attendees,
                'search' => $search,
                'isAdmin' => RoleMiddleware::isAdmin()
            ]);
        } catch (PDOException $e) {
            ErrorHandler::handleError("Error fetching event details.");
        }
    }

    public function downloadAttendees($id) {
        try {
            $stmt = $this->db->prepare("SELECT name FROM events WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $_SESSION['user_id']]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$event) {
                ErrorHandler::handleError("Event not found.");
                $this->redirect('/events');
                return;
            }

            $eventName = preg_replace('/[^a-zA-Z0-9-_]/', '_', $event['name']); // Sanitize filename
            $filename = "{$eventName}-attendees.csv";

            $stmt = $this->db->prepare("
            SELECT attendee_name, attendee_email, created_at 
            FROM event_attendees 
            WHERE event_id = ?
            ORDER BY created_at DESC
        ");
            $stmt->execute([$id]);
            $attendees = $stmt->fetchAll();

            header('Content-Type: text/csv');
            header("Content-Disposition: attachment; filename=\"$filename\"");

            $output = fopen('php://output', 'w');

            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));


            fputcsv($output, ['Name', 'Email', 'Registration Date']);

            foreach ($attendees as $attendee) {
                fputcsv($output, [
                    $attendee['attendee_name'],
                    $attendee['attendee_email'],
                    date('Y-m-d H:i:s', strtotime($attendee['created_at']))
                ]);
            }

            fclose($output);
            exit;
        } catch (PDOException $e) {
            ErrorHandler::handleError("Error downloading attendees list.", "/events/show/$id");
        }
    }
}