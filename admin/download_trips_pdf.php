<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../fpdf/fpdf.php';

$pdo = null;
try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    die('DB connection failed: ' . $e->getMessage());
}

$stmt = $pdo->query("SELECT id, status, truck_id, driver_id, origin_id, destination_id, start_time, end_time, updated_at FROM trips ORDER BY id DESC LIMIT 1000");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new FPDF('L','mm','A4');
$pdf->SetAutoPageBreak(true,10);
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Liste des voyages',0,1,'C');
$pdf->Ln(2);

$pdf->SetFont('Arial','B',10);
$w = [18,40,30,30,40,40,34,34,34];
$headers = ['ID','Status','Truck','Driver','Origin','Destination','Start','End','Modified'];
foreach($headers as $i=>$h) $pdf->Cell($w[$i],8,$h,1,0,'C');
$pdf->Ln();

$pdf->SetFont('Arial','',9);
foreach($rows as $r){
    $pdf->Cell($w[0],7,$r['id'],1);
    $pdf->Cell($w[1],7,$r['status'],1);
    $pdf->Cell($w[2],7,$r['truck_id'],1);
    $pdf->Cell($w[3],7,$r['driver_id'],1);
    $pdf->Cell($w[4],7,$r['origin_id'],1);
    $pdf->Cell($w[5],7,$r['destination_id'],1);
    $pdf->Cell($w[6],7,$r['start_time'],1);
    $pdf->Cell($w[7],7,$r['end_time'],1);
    $pdf->Cell($w[8],7,$r['updated_at'],1);
    $pdf->Ln();
}

$filename = 'trips_export_' . date('Ymd_His') . '.pdf';
$pdf->Output('D', $filename);
exit;

?>
