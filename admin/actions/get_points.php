<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$stmt = $pdo->query("SELECT id, nom FROM points ORDER BY nom ASC");
$points = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'data' => $points
]);