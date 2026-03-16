<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

// KPI 1
$scanned_today = $pdo->query("
SELECT COUNT(DISTINCT truck_id)
FROM scan_logs
WHERE DATE(scanned_at)=CURDATE()
")->fetchColumn();

// KPI 2
$active = $pdo->query("
SELECT COUNT(*) FROM trips
WHERE status='in_progress'
")->fetchColumn();

// KPI 3
$avg = $pdo->query("
SELECT AVG(TIMESTAMPDIFF(MINUTE,start_time,end_time))
FROM trips WHERE status='completed'
")->fetchColumn();

// chart
$chart = $pdo->query("
SELECT DATE(scanned_at) day,COUNT(*) total
FROM scan_logs
GROUP BY day ORDER BY day DESC LIMIT 7
")->fetchAll(PDO::FETCH_ASSOC);

// last scans
$scans = $pdo->query("
SELECT s.*,t.plaque,
IFNULL(tr.status,'--') trip_status,
s.status type,
DATE_FORMAT(s.scanned_at,'%d/%m %H:%i') date
FROM scan_logs s
JOIN trucks t ON s.truck_id=t.id
LEFT JOIN trips tr ON tr.truck_id=t.id
ORDER BY s.scanned_at DESC
LIMIT 20
")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
'kpi'=>[
'scanned_today'=>$scanned_today,
'active_trips'=>$active,
'avg_duration'=>round($avg,1)
],
'chart'=>$chart,
'scans'=>$scans
]);
<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

// KPI 1
$scanned_today = $pdo->query("
SELECT COUNT(DISTINCT truck_id)
FROM scan_logs
WHERE DATE(scanned_at)=CURDATE()
")->fetchColumn();

// KPI 2
$active = $pdo->query("
SELECT COUNT(*) FROM trips
WHERE status='in_progress'
")->fetchColumn();

// KPI 3
$avg = $pdo->query("
SELECT AVG(TIMESTAMPDIFF(MINUTE,start_time,end_time))
FROM trips WHERE status='completed'
")->fetchColumn();

// chart
$chart = $pdo->query("
SELECT DATE(scanned_at) day,COUNT(*) total
FROM scan_logs
GROUP BY day ORDER BY day DESC LIMIT 7
")->fetchAll(PDO::FETCH_ASSOC);

// last scans
$scans = $pdo->query("
SELECT s.*,t.plaque,
IFNULL(tr.status,'--') trip_status,
s.status type,
DATE_FORMAT(s.scanned_at,'%d/%m %H:%i') date
FROM scan_logs s
JOIN trucks t ON s.truck_id=t.id
LEFT JOIN trips tr ON tr.truck_id=t.id
ORDER BY s.scanned_at DESC
LIMIT 20
")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
'kpi'=>[
'scanned_today'=>$scanned_today,
'active_trips'=>$active,
'avg_duration'=>round($avg,1)
],
'chart'=>$chart,
'scans'=>$scans
]);
