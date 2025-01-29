<?php
require_once __DIR__ . '/BaseController.php';

class AuthController extends BaseController
{

    public function showLoginForm()
    {
        $this->view('auth/login');
    }

    public function showRegistrationForm()
    {
        $this->view('auth/register');
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/login');
    }
}