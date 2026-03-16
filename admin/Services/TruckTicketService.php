<?php

namespace Admin\Services;

require_once __DIR__ . '/../../phpqrcode/qrlib.php';
require_once __DIR__ . '/../../fpdf/fpdf.php';

class TruckTicketService
{
    public function __construct(private string $qrDir, private string $pdfDir)
    {
        if (!is_dir($this->qrDir)) {
            mkdir($this->qrDir, 0777, true);
        }
        if (!is_dir($this->pdfDir)) {
            mkdir($this->pdfDir, 0777, true);
        }
    }

    public function generateTicket(int $truckId, string $plaque, string $type, string $code): string
    {
        // Generate QR code
        $truckData = json_encode([
            'id' => $truckId,
            'plaque' => $plaque,
            'type' => $type,
            'code' => $code,
        ]);

        $qrFile = rtrim($this->qrDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . "truck_{$truckId}.png";
        \QRcode::png($truckData, $qrFile, 0, 4);

        // Generate PDF ticket
        $pdfFile = rtrim($this->pdfDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . "truck_{$truckId}.pdf";

        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Ticket du camion', 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, "ID: $truckId", 0, 1);
        $pdf->Cell(0, 10, "Plaque: $plaque", 0, 1);
        $pdf->Cell(0, 10, "Type: $type", 0, 1);
        $pdf->Cell(0, 10, "Code: $code", 0, 1);
        $pdf->Ln(10);

        if (file_exists($qrFile)) {
            $pdf->Image($qrFile, 80, $pdf->GetY(), 50, 50);
        }

        $pdf->Output('F', $pdfFile);

        return $pdfFile;
    }
}
