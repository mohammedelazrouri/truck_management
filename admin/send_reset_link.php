<?php

require_once '../config/db.php';
require_once __DIR__ . '/Controllers/SendResetLinkController.php';
require_once __DIR__ . '/Models/Admin.php';

$controller = new \Admin\Controllers\SendResetLinkController($pdo);
$controller->handle();
