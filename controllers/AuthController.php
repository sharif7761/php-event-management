<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../lib/Validator.php';
require_once __DIR__ . '/../lib/Database.php';

class AuthController extends BaseController {
    private $db;
    private $validator;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->validator = new Validator();
    }

    public function showLoginForm() {
        $this->view('auth/login');
    }

    public function showRegistrationForm() {
        $this->view('auth/register');
    }

    public function register() {
        $rules = [
            'name' => ['required' => true, 'min' => 3],
            'email' => ['required' => true, 'email' => true],
            'password' => ['required' => true, 'min' => 6],
            'confirm_password' => ['required' => true, 'match' => 'password']
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            $_SESSION['errors'] = $this->validator->getErrors();
            $_SESSION['old'] = $_POST;
            $this->redirect('/register');
            return;
        }

        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$_POST['email']]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = "Email already exists";
                $this->redirect('/register');
                return;
            }

            $stmt = $this->db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([
                $_POST['name'],
                $_POST['email'],
                password_hash($_POST['password'], PASSWORD_DEFAULT)
            ]);

            $_SESSION['success'] = "Registration successful. Please login.";
            $this->redirect('/login');
        } catch (PDOException $e) {
            $_SESSION['error'] = "An error occurred. Please try again.";
            $this->redirect('/register');
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
}