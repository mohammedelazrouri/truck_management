<?php

namespace Admin\Controllers;

use Admin\Models\Trip;

class EndTripController
{
    private Trip $trip;

    public function __construct($pdo)
    {
        $this->trip = new Trip($pdo);
    }

    public function index(): void
    {
        $destinations = $this->trip->getDestinations();
        require __DIR__ . '/../Views/trips/end.php';
    }
}
