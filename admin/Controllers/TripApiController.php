<?php

namespace Admin\Controllers;

use Admin\Models\Trip;

class TripApiController
{
    private Trip $trip;

    public function __construct($pdo)
    {
        $this->trip = new Trip($pdo);
    }

    public function getTripByTruck(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $plaque = trim($_GET['plaque'] ?? '');

        if ($plaque === '') {
            echo json_encode(['success' => false, 'message' => 'رقم اللوحة غير موجود']);
            return;
        }

        $trip = $this->trip->getInProgressTripByTruckPlaque($plaque);

        if (!$trip) {
            echo json_encode(['success' => false, 'message' => 'لا توجد رحلة مفتوحة']);
            return;
        }

        echo json_encode(['success' => true, 'trip' => $trip], JSON_UNESCAPED_UNICODE);
    }
}
