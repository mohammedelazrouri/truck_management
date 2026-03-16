<?php

require_once __DIR__ . '/Services/SessionGuard.php';

session_start(); // ضروري لتمكين استخدام $_SESSION

$guard = new \Admin\Services\SessionGuard($_SESSION);
$guard->requireLogin();
$guard->authorizePage(basename($_SERVER['PHP_SELF']));
