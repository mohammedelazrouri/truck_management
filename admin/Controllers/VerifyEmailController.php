<?php

namespace Admin\Controllers;

use Admin\Models\Admin;

class VerifyEmailController
{
    private Admin $admin;

    public function __construct($pdo)
    {
        $this->admin = new Admin($pdo);
    }

    public function handle(): void
    {
        session_start();

        if (!isset($_SESSION['verify_email'])) {
            header('Location: register_admin.php');
            exit();
        }

        $message = '';
        $email = $_SESSION['verify_email'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code'] ?? '');

            if ($this->admin->verifyEmailToken($email, $code)) {
                unset($_SESSION['verify_email']);
                header('Location: trips_readonly.php');
                exit();
            }

            $message = '❌ Code invalide ou expiré.';
        }

        require __DIR__ . '/../Views/auth/verify_email.php';
    }
}
