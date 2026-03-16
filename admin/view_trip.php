<?php

require_once 'session_check.php';

require_once __DIR__ . '/Controllers/TripController.php';

$controller = new \Admin\Controllers\TripController($pdo);
$controller->view();
