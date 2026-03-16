<?php

namespace Admin\Controllers;

use Admin\Models\Truck;

class TrucksController
{
    private Truck $truck;

    public function __construct($pdo)
    {
        $this->truck = new Truck($pdo);
    }

    public function index(): void
    {
        $successMessage = null;
        if (isset($_GET['success'])) {
            $success = htmlspecialchars($_GET['success']);
            $successMessage = match ($success) {
                'truck_created' => '✅ Camion ajouté avec succès!',
                'truck_updated' => '✅ Camion modifié avec succès!',
                'deleted' => '✅ Camion supprimé!',
                'qr_regenerated' => '✅ QR Code régénéré!',
                default => '✅ Opération réussie!',
            };
        }

        $editTruck = null;
        if (isset($_GET['edit_id'])) {
            $editTruck = $this->truck->getById((int)$_GET['edit_id']);
        }

        $trucks = $this->truck->getAll();
        $ticketDirWeb = 'tickets/';

        require __DIR__ . '/../Views/trucks/index.php';
    }
}
