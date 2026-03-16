<?php
require_once '../session_check.php';
require_once '../../config/db.php';

// Lire JSON POST
$input = json_decode(file_get_contents('php://input'), true);
$trip_id = intval($input['id'] ?? 0);

if (!$trip_id) {
    echo json_encode(['success' => false, 'message' => 'ID invalide']);
    exit;
}

try {
    // Optionnel: utiliser transaction
    $pdo->beginTransaction();

    // Supprimer les marchandises liées
    $pdo->prepare("DELETE FROM goods WHERE trip_assigne = ?")->execute([$trip_id]);

    // Supprimer les scans liés
    $pdo->prepare("DELETE FROM scans WHERE trip_id = ?")->execute([$trip_id]);

    // Supprimer le voyage
    $pdo->prepare("DELETE FROM trips WHERE id = ?")->execute([$trip_id]);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}