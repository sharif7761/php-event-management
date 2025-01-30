<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../lib/Validator.php';
require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../lib/EventHelper.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

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
            $_SESSION['error'] = "Error fetching events";
            $this->redirect('/events');
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
            $_SESSION['error'] = "Error creating event";
            $this->redirect('/events/create');
        }
    }

    public function edit($id) {
        try {
            if (!RoleMiddleware::authorizeEvent($id, $this->db)) {
                $_SESSION['error'] = "Access denied";
                $this->redirect('/events');
                return;
            }

            $event = $this->eventHelper->getEventDetails(
                $id,
                RoleMiddleware::isAdmin() ? null : $_SESSION['user_id']
            );

            if (!$event) {
                $_SESSION['error'] = "Event not found";
                $this->redirect('/events');
                return;
            }

            $this->view('events/edit', [
                'title' => 'Edit Event',
                'event' => $event,
                'isAdmin' => RoleMiddleware::isAdmin()
            ]);
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error fetching event";
            $this->redirect('/events');
        }
    }

    public function update($id) {
        try {
            if (!RoleMiddleware::authorizeEvent($id, $this->db)) {
                $_SESSION['error'] = "Access denied";
                $this->redirect('/events');
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
            $_SESSION['error'] = "Error updating event";
            $this->redirect("/events/edit/$id");
        }
    }

    public function delete($id) {
        try {
            if (!RoleMiddleware::authorizeEvent($id, $this->db)) {
                $_SESSION['error'] = "Access denied";
                $this->redirect('/events');
                return;
            }

            $stmt = $this->db->prepare("DELETE FROM events WHERE id = ?");
            $stmt->execute([$id]);

            $_SESSION['success'] = "Event deleted successfully";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error deleting event";
        }
        $this->redirect('/events');
    }

    public function show($id) {
        try {
            if (!RoleMiddleware::authorizeEvent($id, $this->db)) {
                $_SESSION['error'] = "Access denied";
                $this->redirect('/events');
                return;
            }

            $event = $this->eventHelper->getEventDetails(
                $id,
                RoleMiddleware::isAdmin() ? null : $_SESSION['user_id']
            );

            if (!$event) {
                $_SESSION['error'] = "Event not found";
                $this->redirect('/events');
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
            $_SESSION['error'] = "Error fetching event details";
            $this->redirect('/events');
        }
    }
}