<?php

namespace Admin\Models;

use PDO;

class Trip
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getDrivers(): array
    {
        $stmt = $this->pdo->query('SELECT id, nom FROM drivers ORDER BY nom');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrigins(): array
    {
        $stmt = $this->pdo->query("SELECT id, nom FROM points WHERE type IN ('origin','both') ORDER BY nom");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProducts(): array
    {
        $stmt = $this->pdo->query('SELECT id, nom FROM produits ORDER BY nom');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, bon_pour, bon_livraison FROM trips WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $trip = $stmt->fetch(PDO::FETCH_ASSOC);
        return $trip ?: null;
    }

    public function getWithRelations(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT t.*, 
                   tr.plaque,
                   d.nom AS driver_name,
                   o.nom AS origin_name,
                   p.nom AS destination_name,
                   a1.nom AS created_name,
                   a2.nom AS completed_name
            FROM trips t
            LEFT JOIN trucks tr ON t.truck_id = tr.id
            LEFT JOIN drivers d ON t.driver_id = d.id
            LEFT JOIN points o ON t.origin = o.id
            LEFT JOIN points p ON t.destination = p.id
            LEFT JOIN admins a1 ON t.created_by = a1.id
            LEFT JOIN admins a2 ON t.completed_by = a2.id
            WHERE t.id = :id"
        );
        $stmt->execute(['id' => $id]);
        $trip = $stmt->fetch(PDO::FETCH_ASSOC);
        return $trip ?: null;
    }

    public function updateTrip(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE trips SET
                truck_id = :truck_id,
                driver_id = :driver_id,
                created_by = :created_by,
                completed_by = :completed_by,
                origin = :origin,
                destination = :destination,
                start_time = :start_time,
                end_time = :end_time,
                status = :status,
                cancel_reason = :cancel_reason,
                bon_pour = :bon_pour,
                bon_livraison = :bon_livraison
            WHERE id = :id"
        );

        return $stmt->execute([
            'truck_id' => $data['truck_id'],
            'driver_id' => $data['driver_id'],
            'created_by' => $data['created_by'],
            'completed_by' => $data['completed_by'] ?: null,
            'origin' => $data['origin'],
            'destination' => $data['destination'],
            'start_time' => $data['start_time'] ?: null,
            'end_time' => $data['end_time'] ?: null,
            'status' => $data['status'],
            'cancel_reason' => $data['cancel_reason'],
            'bon_pour' => $data['bon_pour'],
            'bon_livraison' => $data['bon_livraison'],
            'id' => $id,
        ]);
    }

    public function getTrucks(): array
    {
        $stmt = $this->pdo->query('SELECT id, plaque FROM trucks ORDER BY plaque');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAdmins(): array
    {
        $stmt = $this->pdo->query('SELECT id, nom FROM admins ORDER BY nom');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPoints(): array
    {
        $stmt = $this->pdo->query('SELECT id, nom FROM points ORDER BY nom');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateBon(int $id, string $bonPour, string $bonLivraison): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE trips SET bon_pour = :bon_pour, bon_livraison = :bon_livraison WHERE id = :id'
        );

        return $stmt->execute([
            'bon_pour' => $bonPour,
            'bon_livraison' => $bonLivraison,
            'id' => $id,
        ]);
    }

    public function getDestinations(): array
    {
        $stmt = $this->pdo->query("SELECT id, nom FROM points WHERE type IN ('destination','both')");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInProgressTripByTruckPlaque(string $plaque): ?array
    {
        $stmt = $this->pdo->prepare("\
            SELECT 
                t.id AS trip_id,
                t.truck_id,
                t.driver_id,
                t.origin,
                t.start_time,
                tr.plaque,
                d.nom AS driver
            FROM trips t
            JOIN trucks tr ON t.truck_id = tr.id
            LEFT JOIN drivers d ON t.driver_id = d.id
            WHERE tr.plaque = :plaque
            AND t.status = 'in_progress'
            LIMIT 1
        ");

        $stmt->execute(['plaque' => $plaque]);
        $trip = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$trip) {
            return null;
        }

        $stmtProd = $this->pdo->prepare("\
            SELECT 
                p.nom,
                tp.quantite,
                tp.poids,
                tp.unite
            FROM trip_produits tp
            JOIN produits p ON tp.produit_id = p.id
            WHERE tp.trip_id = :trip_id
        ");

        $stmtProd->execute(['trip_id' => $trip['trip_id']]);
        $trip['products'] = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

        return $trip;
    }

    public function getInProgressTrips(string $search = ''): array
    {
        $where = "WHERE t.status = 'in_progress'";
        $params = [];

        if ($search !== '') {
            $where .= " AND (d.nom LIKE :search OR p.nom LIKE :search)";
            $params['search'] = "%$search%";
        }

        $sql = "
            SELECT 
                t.id,
                t.start_time,
                t.cancel_reason,
                d.nom AS driver_name,
                p.nom AS destination_name
            FROM trips t
            LEFT JOIN drivers d ON t.driver_id = d.id
            LEFT JOIN points p ON t.destination = p.id
            $where
            ORDER BY t.id DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cancelTrip(int $tripId, string $reason, int $adminId): void
    {
        $this->pdo->beginTransaction();

        $check = $this->pdo->prepare("SELECT status FROM trips WHERE id = ? FOR UPDATE");
        $check->execute([$tripId]);
        $trip = $check->fetch(PDO::FETCH_ASSOC);

        if (!$trip || $trip['status'] !== 'in_progress') {
            $this->pdo->rollBack();
            throw new \Exception('الرحلة غير موجودة أو غير مفتوحة');
        }

        $update = $this->pdo->prepare(
            "UPDATE trips
             SET status = 'cancelled',
                 cancel_reason = :reason,
                 completed_by = :admin_id,
                 end_time = NOW()
             WHERE id = :trip_id"
        );

        $update->execute([
            'reason' => $reason,
            'admin_id' => $adminId,
            'trip_id' => $tripId,
        ]);

        $this->pdo->prepare("UPDATE trucks SET statut = 'disponible' WHERE id = (SELECT truck_id FROM trips WHERE id = ?)")
            ->execute([$tripId]);

        $this->pdo->commit();
    }

    public function getInProgressTripByTruckId(int $truckId): ?array
    {
        $stmt = $this->pdo->prepare("\
            SELECT 
                t.id AS trip_id,
                t.truck_id,
                t.cargo,
                t.origin,
                t.start_time,
                tr.plaque,
                d.nom AS driver
            FROM trips t
            JOIN trucks tr ON t.truck_id = tr.id
            LEFT JOIN drivers d ON t.driver_id = d.id
            WHERE t.truck_id = :truck_id
            AND t.status = 'in_progress'
            LIMIT 1
        ");

        $stmt->execute(['truck_id' => $truckId]);
        $trip = $stmt->fetch(PDO::FETCH_ASSOC);

        return $trip ?: null;
    }

    public function startTrip(int $truckId, int $originId, string $cargo, int $adminId): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO trips (truck_id, origin, cargo, status, start_time, created_by) VALUES (?, ?, ?, 'in_progress', NOW(), ?)"
        );
        $stmt->execute([$truckId, $originId, $cargo, $adminId]);
        return (int)$this->pdo->lastInsertId();
    }
}
