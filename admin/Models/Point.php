<?php

namespace Admin\Models;

use PDO;

class Point
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(string $name, string $type): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO points (nom, type) VALUES (?, ?)');
        return $stmt->execute([$name, $type]);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM points ORDER BY id DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
