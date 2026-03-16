<?php

namespace Admin\Controllers;

use Admin\Models\Truck;
use Admin\Models\Trip;

class ScanTruckApiController
{
    private Truck $truck;
    private Trip $trip;

    public function __construct($pdo)
    {
        $this->truck = new Truck($pdo);
        $this->trip = new Trip($pdo);
    }

    public function checkTruck(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $qr = trim($_GET['qr'] ?? '');
        if ($qr === '') {
            echo json_encode(['error' => 'QR manquant']);
            return;
        }

        $truck = $this->truck->findByQr($qr);
        if (!$truck) {
            echo json_encode(['error' => 'Camion introuvable']);
            return;
        }

        $trip = $this->trip->getInProgressTripByTruckId((int)$truck['id']);

        if ($trip) {
            echo json_encode(['status' => 'open_trip', 'truck' => $truck, 'trip' => $trip]);
        } else {
            echo json_encode(['status' => 'no_trip', 'truck' => $truck]);
        }
    }

    public function startTrip(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $data = json_decode(file_get_contents('php://input'), true) ?: [];
        $truckId = intval($data['truck_id'] ?? 0);
        $origin = intval($data['origin'] ?? 0);
        $cargo = trim($data['cargo'] ?? '');
        $adminId = $_SESSION['admin_id'] ?? 0;

        if (!$truckId || !$origin || $cargo === '' || !$adminId) {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            return;
        }

        try {
            $tripId = $this->trip->startTrip($truckId, $origin, $cargo, $adminId);
            $this->truck->updateStatus($truckId, 'en_mission');
            echo json_encode(['success' => true, 'trip_id' => $tripId]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
        }
    }
}
