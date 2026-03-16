<?php

namespace Admin\Controllers;

use Admin\Models\Trip;

class InProgressTripsController
{
    private Trip $trip;

    public function __construct($pdo)
    {
        $this->trip = new Trip($pdo);
    }

    public function handle(): void
    {
        $errorMsg = '';
        $search = trim($_GET['search'] ?? '');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_trip'])) {
            $cancelResult = $this->cancelTrip();
            if (!empty($cancelResult)) {
                $errorMsg = $cancelResult;
            } else {
                // Redirect to avoid double-submitting the cancel form
                header('Location: in_progress_trips.php');
                exit;
            }
        }

        $trips = $this->trip->getInProgressTrips($search);

        // Variables used by view
        require __DIR__ . '/../Views/trips/in_progress.php';
    }

    private function cancelTrip(): string
    {
        $tripId = intval($_POST['trip_id'] ?? 0);
        $reason = trim($_POST['cancel_reason'] ?? '');
        $adminId = intval($_SESSION['admin_id'] ?? 0);

        if (!$tripId || $reason === '' || !$adminId) {
            return 'البيانات غير صالحة للإلغاء';
        }

        try {
            $this->trip->cancelTrip($tripId, $reason, $adminId);
            return '';
        } catch (\Exception $e) {
            return 'حدث خطأ أثناء الإلغاء: ' . $e->getMessage();
        }
    }
}
