<?php
// Bootstrap file: loads configuration, starts session, connects to DB
require_once __DIR__ . '/../vendor/autoload.php';

// load config
$config = require __DIR__ . '/../config/app.php';

// start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// database connection
$dsn = sprintf('%s:host=%s;dbname=%s;charset=utf8mb4',
    $config['db']['driver'],
    $config['db']['host'],
    $config['db']['database']
);
$pdo = new PDO($dsn, $config['db']['username'], $config['db']['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// make available globally (could also use container)
$GLOBALS['pdo'] = $pdo;
