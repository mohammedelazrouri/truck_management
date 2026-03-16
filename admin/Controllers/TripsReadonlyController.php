<?php

namespace Admin\Controllers;

class TripsReadonlyController
{
    public function index(): void
    {
        require __DIR__ . '/../Views/trips/readonly.php';
    }
}
