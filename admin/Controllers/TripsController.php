<?php

namespace Admin\Controllers;

class TripsController
{
    public function index(): void
    {
        require __DIR__ . '/../Views/trips/dashboard.php';
    }
}
