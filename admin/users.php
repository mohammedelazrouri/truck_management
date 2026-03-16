<?php

require_once 'session_check.php';

require_once __DIR__ . '/Controllers/UsersController.php';

$controller = new \Admin\Controllers\UsersController();
$controller->index();
