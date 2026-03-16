<?php

require_once 'session_check.php';
require_once 'check_principal.php';

require_once __DIR__ . '/Controllers/DashboardController.php';

$controller = new \Admin\Controllers\DashboardController();
$controller->index();







