<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../lib/Validator.php';
require_once __DIR__ . '/../lib/Database.php';

class EventController extends BaseController {
    private $db;
    private $validator;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->validator = new Validator();
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