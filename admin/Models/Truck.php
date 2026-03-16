<?php

namespace Admin\Models;

use PDO;

class Truck
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function existsByPlaque(string $plaque): bool
    {
        $stmt = $this->pdo->prepare('SELECT id FROM trucks WHERE plaque = :plaque LIMIT 1');
        $stmt->execute(['plaque' => $plaque]);
        return (bool) $stmt->fetch();
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO trucks (plaque, type, code, statut) VALUES (:plaque, :type, :code, :statut)'
        );

        $stmt->execute([
            'plaque' => $data['plaque'],
            'type' => $data['type'],
            'code' => $data['code'],
            'statut' => $data['statut'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM trucks ORDER BY id DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM trucks WHERE id = ?');
        $stmt->execute([$id]);
        $truck = $stmt->fetch(PDO::FETCH_ASSOC);
        return $truck ?: null;
    }

    public function findByQr(string $qr): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, plaque, type, statut FROM trucks WHERE qr_code = ?');
        $stmt->execute([$qr]);
        $truck = $stmt->fetch(PDO::FETCH_ASSOC);
        return $truck ?: null;
    }

    public function updateStatus(int $truckId, string $status): bool
    {
        $stmt = $this->pdo->prepare('UPDATE trucks SET statut = ? WHERE id = ?');
        return $stmt->execute([$status, $truckId]);
    }
}
