<?php

session_start();
require_once __DIR__ . '/../config/db.php';

require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/Models/Admin.php';

$controller = new \Admin\Controllers\AuthController($pdo);
$controller->login();
