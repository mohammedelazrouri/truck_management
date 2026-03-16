<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/session_check.php';

require_once __DIR__ . '/Controllers/InProgressTripsController.php';
require_once __DIR__ . '/Models/Trip.php';

$controller = new \Admin\Controllers\InProgressTripsController($pdo);
$controller->handle();
