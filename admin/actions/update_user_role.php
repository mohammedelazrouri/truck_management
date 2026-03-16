<?php
require_once '../session_check.php';
require_once '../../config/db.php';

header('Content-Type: application/json; charset=utf-8');

// Only principal can change roles
$role = $_SESSION['admin_role'] ?? '';
if ($role !== 'principal') {
    echo json_encode(['success' => false, 'message' => 'Accès refusé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$new_role = isset($_POST['role']) ? trim($_POST['role']) : '';

if (!$user_id || !in_array($new_role, ['principal', 'admin', 'pointer'])) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

// Prevent changing your own role
$currentUserId = $_SESSION['admin_id'] ?? 0;
if ($user_id == $currentUserId) {
    echo json_encode(['success' => false, 'message' => 'Vous ne pouvez pas modifier votre propre rôle']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE admins SET role = ? WHERE id = ?");
    $stmt->execute([$new_role, $user_id]);

    echo json_encode(['success' => true, 'message' => 'Rôle mis à jour avec succès']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
}
?>
