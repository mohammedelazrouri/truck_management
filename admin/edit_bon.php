<?php

require_once 'session_check.php';
require_once '../config/db.php';

require_once __DIR__ . '/Controllers/TripController.php';
require_once __DIR__ . '/Models/Trip.php';

$controller = new \Admin\Controllers\TripController($pdo);
$controller->editBon();
