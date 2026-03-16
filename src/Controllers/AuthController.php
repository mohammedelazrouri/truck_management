<?php
namespace App\Controllers;

class AuthController
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function loginForm()
    {
        // render login view
        require __DIR__.'/../Views/auth/login.php';
    }

    public function login()
    {
        // simple post handling, placeholder
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // authenticate...
        header('Location: /dashboard');
    }

    public function logout()
    {
        session_destroy();
        header('Location: /');
    }
}
