<?php

namespace Admin\Controllers;

class ForgotPasswordController
{
    public function index(): void
    {
        require __DIR__ . '/../Views/auth/forgot_password.php';
    }
}
