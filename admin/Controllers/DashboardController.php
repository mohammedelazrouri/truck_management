<?php

namespace Admin\Controllers;

class DashboardController
{
    public function index(): void
    {
        require __DIR__ . '/../Views/dashboard.php';
    }
}
