<?php
header('Content-Type: application/json');
require_once '../../config/db.php';

// جلب الرحلات مع التفاصيل و start/end محسوب من scans
$stmt = $pdo->query("
    SELECT 
        t.id,
        t.statut,
        t.cargo,
        truck.plaque AS truck_plaque,
        driver.nom AS driver_name,
        origin.nom AS origin_name,
        destination.nom AS destination_name,
        -- أول start_time من scan truck_start أو t.start_time
        COALESCE(
            (SELECT MIN(s.timestamp) 
             FROM scans s 
             WHERE s.trip_id = t.id AND s.type_scan = 'truck_start'),
            t.start_time
        ) AS start_time,
        -- آخر end_time من scan good_destination أو آخر truck_start أو t.end_time
        COALESCE(
            (SELECT MAX(s.timestamp) 
             FROM scans s 
             WHERE s.trip_id = t.id AND s.type_scan = 'good_destination'),
            (SELECT MAX(s2.timestamp)
             FROM scans s2
             WHERE s2.trip_id = t.id AND s2.type_scan = 'truck_start'),
            t.end_time
        ) AS end_time
    FROM trips t
    LEFT JOIN trucks truck ON t.truck_id = truck.id
    LEFT JOIN drivers driver ON t.driver_id = driver.id
    LEFT JOIN points origin ON t.origin = origin.id
    LEFT JOIN points destination ON t.destination = destination.id
    ORDER BY t.id DESC
");

$trips = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'status' => 'success',
    'data' => $trips
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
