<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/Controllers/VerifyEmailController.php';
require_once __DIR__ . '/Models/Admin.php';

$controller = new \Admin\Controllers\VerifyEmailController($pdo);
$controller->handle();
