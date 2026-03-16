<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? null;

if (!$action) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Action manquante']);
    exit;
}

try {

switch($action) {

    // ==============================
    // 1️⃣ Get Points
    // ==============================
    case 'get_points':
        $stmt = $pdo->query("SELECT id, nom FROM points ORDER BY nom ASC");
        echo json_encode([
            'success' => true,
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
        break;

    // ==============================
    // 2️⃣ Get Trips with Filters
    // ==============================
    case 'get_trips':

        $origin      = $_GET['origin'] ?? null;
        $destination = $_GET['destination'] ?? null;
        $date_from   = $_GET['date_from'] ?? null;
        $date_to     = $_GET['date_to'] ?? null;
        $status      = $_GET['status'] ?? null;

        $allowedStatuses = ['in_progress','completed','cancelled'];

        $query = "SELECT 
                    t.id,
                    t.status,
                    tr.plaque AS truck_plaque,
                    d.nom AS driver_name,
                    o.nom AS origin_name,
                    dest.nom AS destination_name,
                    t.start_time,
                    t.end_time,
                    t.bon_pour,
                    t.bon_livraison,
                    t.updated_at
                  FROM trips t
                  LEFT JOIN trucks tr ON t.truck_id = tr.id
                  LEFT JOIN drivers d ON t.driver_id = d.id
                  LEFT JOIN points o ON t.origin = o.id
                  LEFT JOIN points dest ON t.destination = dest.id
                  WHERE 1=1";

        $params = [];

        if (!empty($origin)) {
            $query .= " AND t.origin = :origin";
            $params[':origin'] = $origin;
        }

        if (!empty($destination)) {
            $query .= " AND t.destination = :destination";
            $params[':destination'] = $destination;
        }

        if (!empty($date_from)) {
            $query .= " AND DATE(t.start_time) >= :date_from";
            $params[':date_from'] = $date_from;
        }

        if (!empty($date_to)) {
            $query .= " AND DATE(t.start_time) <= :date_to";
            $params[':date_to'] = $date_to;
        }

        if (!empty($status) && in_array(strtolower($status), $allowedStatuses)) {
            $query .= " AND t.status = :status";
            $params[':status'] = strtolower($status);
        }

        $query .= " ORDER BY t.id DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        echo json_encode([
            'success' => true,
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
        break;

    // ==============================
    // 3️⃣ Delete Trip (SAFE)
    // ==============================
    case 'delete_trip':

        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            exit;
        }

        // 🔒 Vérifier si le trip existe
        $check = $pdo->prepare("SELECT status FROM trips WHERE id = ?");
        $check->execute([$id]);
        $trip = $check->fetch(PDO::FETCH_ASSOC);

        if (!$trip) {
            echo json_encode(['success' => false, 'message' => 'Voyage introuvable']);
            exit;
        }

        // 🔒 Ne pas supprimer si en cours
        if ($trip['status'] === 'in_progress') {
            echo json_encode(['success' => false, 'message' => 'Impossible de supprimer un voyage en cours']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM trips WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true]);
        break;

    // ==============================
    // 4️⃣ Update Bon
    // ==============================
    case 'update_bon':

        $input = json_decode(file_get_contents('php://input'), true);

        $id = $input['id'] ?? null;
        $bon_pour = $input['bon_pour'] ?? null;
        $bon_livraison = $input['bon_livraison'] ?? null;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            exit;
        }

        $stmt = $pdo->prepare("
            UPDATE trips 
            SET bon_pour = ?, 
                bon_livraison = ?, 
                updated_at = NOW()
            WHERE id = ?
        ");

        $stmt->execute([$bon_pour, $bon_livraison, $id]);

        echo json_encode(['success' => true]);
        break;

    // ==============================
    // 5️⃣ Truck Stats (Disponible seulement)
    // ==============================
    case 'get_truck_stats':

        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS available
            FROM trucks
            WHERE statut = 'disponible'
        ");

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => [
                'available' => (int)$result['available']
            ]
        ]);
        break;

    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Action non reconnue'
        ]);
        break;
}

} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur'
    ]);
}