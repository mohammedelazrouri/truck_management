<?php

namespace Admin\Controllers;

use Admin\Models\Trip;

class TripController
{
    private Trip $trip;

    public function __construct($pdo)
    {
        $this->trip = new Trip($pdo);
    }

    public function create(): void
    {
        $drivers = $this->trip->getDrivers();
        $origins = $this->trip->getOrigins();
        $products = $this->trip->getProducts();

        require __DIR__ . '/../Views/trips/add.php';
    }

    public function view(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id <= 0) {
            die('ID invalide');
        }

        $trip = $this->trip->getWithRelations($id);
        if (!$trip) {
            die('Voyage introuvable');
        }

        $message = '';
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'truck_id' => $_POST['truck_id'] ?? 0,
                'driver_id' => $_POST['driver_id'] ?? 0,
                'created_by' => $_POST['created_by'] ?? 0,
                'completed_by' => $_POST['completed_by'] ?? null,
                'origin' => $_POST['origin'] ?? 0,
                'destination' => $_POST['destination'] ?? 0,
                'start_time' => $_POST['start_time'] ?? null,
                'end_time' => $_POST['end_time'] ?? null,
                'status' => $_POST['status'] ?? '',
                'cancel_reason' => $_POST['cancel_reason'] ?? '',
                'bon_pour' => $_POST['bon_pour'] ?? '',
                'bon_livraison' => $_POST['bon_livraison'] ?? '',
            ];

            if ($this->trip->updateTrip($id, $data)) {
                $success = true;
                $message = 'Voyage modifié avec succès.';
                $trip = $this->trip->getWithRelations($id);
            } else {
                $message = 'Erreur lors de la mise à jour.';
            }
        }

        $trucks = $this->trip->getTrucks();
        $drivers = $this->trip->getDrivers();
        $admins = $this->trip->getAdmins();
        $points = $this->trip->getPoints();

        require __DIR__ . '/../Views/trips/view.php';
    }

    public function editBon(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id <= 0) {
            die('ID manquant');
        }

        $trip = $this->trip->getById($id);
        if (!$trip) {
            die('Voyage introuvable');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bonPour = $_POST['bon_pour'] ?? '';
            $bonLivraison = $_POST['bon_livraison'] ?? '';

            $this->trip->updateBon($id, $bonPour, $bonLivraison);

            header('Location: trips.php');
            exit;
        }

        require __DIR__ . '/../Views/trips/edit_bon.php';
    }
}
