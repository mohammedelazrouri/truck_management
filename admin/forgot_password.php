<?php

require_once 'session_check.php';

require_once __DIR__ . '/Controllers/ForgotPasswordController.php';

$controller = new \Admin\Controllers\ForgotPasswordController();
$controller->index();
