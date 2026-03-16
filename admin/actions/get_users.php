<?php
require_once '../session_check.php';
require_once '../../config/db.php';

header('Content-Type: application/json; charset=utf-8');

// Only principal can view all users
$role = $_SESSION['admin_role'] ?? '';
if ($role !== 'principal') {
    echo json_encode(['success' => false, 'message' => 'Accès refusé']);
    exit;
}

$nom = isset($_GET['nom']) ? trim($_GET['nom']) : '';
$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$roleFilter = isset($_GET['role']) ? trim($_GET['role']) : '';

try {
    $query = "SELECT id, nom, email, role, is_verified, created_at FROM admins WHERE 1=1";
    $params = [];

    if ($nom) {
        $query .= " AND nom LIKE ?";
        $params[] = "%$nom%";
    }

    if ($email) {
        $query .= " AND email LIKE ?";
        $params[] = "%$email%";
    }

    if ($roleFilter) {
        $query .= " AND role = ?";
        $params[] = $roleFilter;
    }

    $query .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $users]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
?>
