<?php

namespace Admin\Controllers;

class UsersController
{
    public function index(): void
    {
        // Only principal users should see this page
        require_once __DIR__ . '/../check_principal.php';

        require __DIR__ . '/../Views/users/index.php';
    }
}
