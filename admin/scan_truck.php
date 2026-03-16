<?php

require_once 'session_check.php';
require_once '../config/db.php';

require_once __DIR__ . '/Controllers/ScanTruckController.php';

$controller = new \Admin\Controllers\ScanTruckController();
$controller->index();
