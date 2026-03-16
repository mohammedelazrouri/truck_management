<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/check_principal.php';

require_once __DIR__ . '/Controllers/DriverController.php';
require_once __DIR__ . '/Models/Driver.php';

$controller = new \Admin\Controllers\DriverController($pdo);
$controller->create();
