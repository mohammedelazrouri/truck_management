<?php

require_once 'session_check.php';
require_once '../config/db.php';

require_once __DIR__ . '/Controllers/TruckController.php';
require_once __DIR__ . '/Models/Truck.php';
require_once __DIR__ . '/Services/TruckTicketService.php';

$controller = new \Admin\Controllers\TruckController($pdo);
$controller->create();
