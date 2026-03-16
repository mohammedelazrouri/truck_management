<?php

namespace Admin\Controllers;

use Admin\Models\Truck;
use Admin\Services\TruckTicketService;

class TruckController
{
    private Truck $truck;
    private TruckTicketService $ticketService;

    public function __construct($pdo)
    {
        $this->truck = new Truck($pdo);
        $this->ticketService = new TruckTicketService(__DIR__ . '/../../qrcodes', __DIR__ . '/../../tickets');
    }

    public function create(): void
    {
        $successMsg = '';
        $errorMsg = '';
        $messageType = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $plaque = trim($_POST['plaque'] ?? '');
            $type = trim($_POST['type'] ?? '');
            $code = trim($_POST['code'] ?? '');
            $statut = $_POST['statut'] ?? '';

            if ($plaque === '' || $statut === '') {
                $errorMsg = 'Veuillez remplir tous les champs obligatoires.';
                $messageType = 'danger';
            } elseif ($this->truck->existsByPlaque($plaque)) {
                $errorMsg = 'Cette plaque existe déjà.';
                $messageType = 'danger';
            } else {
                $truckId = $this->truck->create([
                    'plaque' => $plaque,
                    'type' => $type,
                    'code' => $code,
                    'statut' => $statut,
                ]);

                $pdfFile = $this->ticketService->generateTicket($truckId, $plaque, $type, $code);

                $successMsg = "✅ Camion ajouté avec succès. PDF prêt: <a href='{$pdfFile}' target='_blank'>Télécharger le ticket</a>";
                $messageType = 'success';
            }
        }

        require __DIR__ . '/../Views/trucks/add.php';
    }
}
