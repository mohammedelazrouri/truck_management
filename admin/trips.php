<?php

require_once 'session_check.php';

require_once __DIR__ . '/Controllers/TripsController.php';

$controller = new \Admin\Controllers\TripsController();
$controller->index();
