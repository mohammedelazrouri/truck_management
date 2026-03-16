<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../phpqrcode/qrlib.php';
require_once __DIR__ . '/../fpdf/fpdf.php'; // inclure FPDF

$action = $_GET['action'] ?? '';

switch ($action) {

    // Ajouter un camion
    case 'add':
        $stmt = $pdo->prepare("
            INSERT INTO trucks (plaque, type, `code`, statut)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $_POST['plaque'],
            $_POST['type'],
            $_POST['code'],
            $_POST['statut']
        ]);
        $truckId = $pdo->lastInsertId();

        generateTruckPDF($truckId, $_POST['plaque'], $_POST['type'], $_POST['code']);

        header("Location: ../trucks.php?success=truck_created");
        exit;

    // Modifier un camion
    case 'edit':
        $stmt = $pdo->prepare("
            UPDATE trucks SET
            plaque = ?,
            type = ?,
            `code` = ?,
            statut = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $_POST['plaque'],
            $_POST['type'],
            $_POST['code'],
            $_POST['statut'],
            $_POST['id']
        ]);

        generateTruckPDF($_POST['id'], $_POST['plaque'], $_POST['type'], $_POST['code']);

        header("Location: ../trucks.php?success=truck_updated");
        exit;

    // Supprimer un camion
    case 'delete':
        $truckId = (int)$_GET['id'];

        $stmt = $pdo->prepare("DELETE FROM trucks WHERE id = ?");
        $stmt->execute([$truckId]);

        // Supprimer PDF si existant
        $pdfFile = __DIR__ . '/../../tickets/truck_' . $truckId . '.pdf';
        if (file_exists($pdfFile)) unlink($pdfFile);

        header("Location: ../trucks.php?success=deleted");
        exit;

    // Régénérer QR Code PDF
    case 'regenerate_qr':
        $truckId = (int)$_GET['id'];

        $stmt = $pdo->prepare("SELECT id, plaque, type, `code` FROM trucks WHERE id = ?");
        $stmt->execute([$truckId]);
        $truck = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$truck) {
            header("Location: ../trucks.php?error=truck_not_found");
            exit;
        }

        generateTruckPDF($truck['id'], $truck['plaque'], $truck['type'], $truck['code']);

        header("Location: ../trucks.php?success=qr_regenerated");
        exit;

    default:
        header("Location: ../trucks.php?error=invalid_action");
        exit;
}

// Fonction pour générer PDF du QR code
function generateTruckPDF($truckId, $plaque, $type, $code) {
    $ticketDir = __DIR__ . '/../../tickets/';
    if (!is_dir($ticketDir)) mkdir($ticketDir, 0777, true);

    $pngFile = $ticketDir . "truck_$truckId.png";
    $pdfFile = $ticketDir . "truck_$truckId.pdf";

    // Générer QR code en PNG
    $truckData = json_encode([
        'id' => $truckId,
        'plaque' => $plaque,
        'type' => $type,
        'code' => $code
    ]);
    QRcode::png($truckData, $pngFile, QR_ECLEVEL_L, 4);

    // Générer PDF avec FPDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, "QR Code du Camion ID $truckId", 0, 1, 'C');
    $pdf->Image($pngFile, 60, 40, 90, 90); // x, y, width, height
    $pdf->Output('F', $pdfFile);

    // Supprimer le PNG
    if (file_exists($pngFile)) unlink($pngFile);
}