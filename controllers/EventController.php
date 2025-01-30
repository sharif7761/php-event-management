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
}