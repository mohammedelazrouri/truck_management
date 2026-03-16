<?php

require_once 'session_check.php';
require_once '../config/db.php';

require_once __DIR__ . '/Controllers/ScanTruckApiController.php';
require_once __DIR__ . '/Models/Truck.php';
require_once __DIR__ . '/Models/Trip.php';

$controller = new \Admin\Controllers\ScanTruckApiController($pdo);
$controller->checkTruck();
