<?php
header('Content-Type: application/json');
require_once '../config/db.php';

// Base query with LEFT JOIN to get product info if needed
$sql = "SELECT sc.id, sc.date_livraison, sc.conducteur, sc.matricule,
               sc.bon_pour, sc.bon_livraison,
               tp.quantite, p.nom AS agregat
        FROM situation_chantier sc
        LEFT JOIN trips t ON sc.trip_id = t.id
        LEFT JOIN trip_produits tp ON tp.trip_id = t.id
        LEFT JOIN produits p ON p.id = tp.produit_id
        WHERE 1=1";

$params = [];

/* FILTERS */
if (!empty($_GET['conducteur'])) {
    $sql .= " AND sc.conducteur LIKE ?";
    $params[] = "%".$_GET['conducteur']."%";
}

if (!empty($_GET['matricule'])) {
    $sql .= " AND sc.matricule LIKE ?";
    $params[] = "%".$_GET['matricule']."%";
}

if (!empty($_GET['bon_livraison'])) {
    $sql .= " AND sc.bon_livraison LIKE ?";
    $params[] = "%".$_GET['bon_livraison']."%";
}

if (!empty($_GET['agregat'])) {
    $sql .= " AND p.nom LIKE ?";
    $params[] = "%".$_GET['agregat']."%";
}

if (!empty($_GET['date_from'])) {
    $sql .= " AND sc.date_livraison >= ?";
    $params[] = $_GET['date_from'];
}

if (!empty($_GET['date_to'])) {
    $sql .= " AND sc.date_livraison <= ?";
    $params[] = $_GET['date_to'];
}

$sql .= " ORDER BY sc.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
exit;