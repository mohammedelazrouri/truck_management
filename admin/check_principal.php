<?php
// start session only if not already active
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once '../config/db.php';

// ===== تحقق من تسجيل الدخول =====
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// ===== تحقق من الدور =====
if ($_SESSION['admin_role'] !== 'principal') {
    echo "<h2>Accès refusé : Vous n'êtes pas autorisé à accéder à cette page.</h2>";
    exit;
}
?>