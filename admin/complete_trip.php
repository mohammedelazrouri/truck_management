<?php
require_once '../config/db.php';
require_once 'session_check.php';

header('Content-Type: application/json; charset=utf-8');

$trip_id     = intval($_POST['id'] ?? 0);
$destination = intval($_POST['destination'] ?? 0);
$admin_id    = $_SESSION['admin_id'] ?? 0;

if (!$trip_id || !$destination || !$admin_id) {
    echo json_encode(['success'=>false,'message'=>'بيانات غير صالحة']);
    exit;
}

try {

    $pdo->beginTransaction();

    // ===== Vérifier trip =====
    $stmt = $pdo->prepare("
        SELECT * FROM trips 
        WHERE id=? AND status='in_progress'
        FOR UPDATE
    ");
    $stmt->execute([$trip_id]);
    $trip = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$trip) {
        throw new Exception("Trip introuvable");
    }

    // ===== Terminer trip =====
    $stmtUpdate = $pdo->prepare("
        UPDATE trips
        SET 
            status='completed',
            destination=?,
            end_time=NOW(),
            completed_by=?
        WHERE id=?
    ");
    $stmtUpdate->execute([$destination, $admin_id, $trip_id]);

    // ===== Rendre camion disponible =====
    $stmtTruck = $pdo->prepare("
        UPDATE trucks 
        SET statut='disponible'
        WHERE id=?
    ");
    $stmtTruck->execute([$trip['truck_id']]);

    $pdo->commit();

    echo json_encode([
        'success'=>true,
        'message'=>'تم إنهاء الرحلة بنجاح'
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([
        'success'=>false,
        'message'=>'خطأ أثناء إنهاء الرحلة'
    ], JSON_UNESCAPED_UNICODE);
}