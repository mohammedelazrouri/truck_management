<?php
require_once '../session_check.php';
require_once '../../config/db.php';

// ===== Vérifier action =====
$action = $_GET['action'] ?? '';

if ($action !== 'add' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../trips_readonly.php');
    exit;
}

// ===== Récupération =====
$truck_id  = intval($_POST['truck_id'] ?? 0);
$driver_id = intval($_POST['driver_id'] ?? 0);
$origin    = intval($_POST['origin'] ?? 0);
$products  = $_POST['products'] ?? [];
$admin_id  = $_SESSION['admin_id'] ?? null;

// ===== Validation =====
if (!$truck_id || !$driver_id || !$origin || empty($products) || !$admin_id) {
    header('Location: ../add_trip.php?error=missing_fields');
    exit;
}

try {

    $pdo->beginTransaction();

    // ===== 1) Vérifier que le camion est disponible =====
    $stmtCheckTruck = $pdo->prepare("SELECT statut FROM trucks WHERE id=? FOR UPDATE");
    $stmtCheckTruck->execute([$truck_id]);
    $truck = $stmtCheckTruck->fetch();

    if (!$truck || $truck['statut'] !== 'disponible') {
        throw new Exception("Camion non disponible");
    }

    // ===== 2) Créer le trip =====
    $stmtTrip = $pdo->prepare("
        INSERT INTO trips 
        (truck_id, driver_id, origin, status, start_time, created_by)
        VALUES (?, ?, ?, 'in_progress', NOW(), ?)
    ");
    $stmtTrip->execute([$truck_id, $driver_id, $origin, $admin_id]);
    $trip_id = $pdo->lastInsertId();

    // ===== 3) Ajouter scan initial =====
    $stmtScan = $pdo->prepare("
        INSERT INTO scans (trip_id, type_scan, location_id)
        VALUES (?, 'truck_start', ?)
    ");
    $stmtScan->execute([$trip_id, $origin]);

    // ===== 4) Insérer produits dans trip_produits =====
    $stmtProduct = $pdo->prepare("
        INSERT INTO trip_produits 
        (trip_id, produit_id, quantite, poids, unite)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($products as $p) {

        $produit_id = intval($p['produit_id'] ?? 0);
        $quantite   = floatval($p['quantite'] ?? 0);
        $poids      = floatval($p['poids'] ?? 0);
        $unite      = $p['unite'] ?? 'unite';

        if (!$produit_id || $quantite <= 0) {
            throw new Exception("Produit invalide");
        }

        $stmtProduct->execute([
            $trip_id,
            $produit_id,
            $quantite,
            $poids ?: null,
            $unite
        ]);
    }

    // ===== 5) Mettre camion en mission =====
    $pdo->prepare("
        UPDATE trucks 
        SET statut='en_mission' 
        WHERE id=?
    ")->execute([$truck_id]);

    $pdo->commit();

    header("Location: ../trips.php?success=created");
    exit;

} catch (Exception $e) {

    $pdo->rollBack();
    error_log($e->getMessage());

    header("Location: ../add_trip.php?error=db");
    exit;
}