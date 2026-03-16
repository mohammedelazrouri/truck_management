<?php
require_once '../../config/db.php';

// Start session without redirect
session_start();

header('Content-Type: application/json');

try {
    // Check if user is authenticated
    if (!isset($_SESSION['admin_id'])) {
        http_response_code(403);
        echo json_encode([]);
        exit;
    }

    // Get type from query parameter
    $type = isset($_GET['type']) ? trim($_GET['type']) : null;

    // Determine which types to fetch
    $types = [];
    if ($type === 'origin') {
        $types = ['origin', 'both'];
    } elseif ($type === 'destination') {
        $types = ['destination', 'both'];
    } else {
        $types = ['origin', 'destination', 'both'];
    }

    // Create placeholders for SQL IN clause
    $placeholders = implode(',', array_fill(0, count($types), '?'));

    // Fetch points
    $stmt = $pdo->prepare("SELECT id, nom FROM Points WHERE type IN ($placeholders) ORDER BY nom ASC");
    $stmt->execute($types);
    $points = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($points);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
