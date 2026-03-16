<?php

namespace Admin\Models;

use PDO;

class Product
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function existsByName(string $name): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM produits WHERE nom = ?');
        $stmt->execute([$name]);
        return (bool) $stmt->fetchColumn();
    }

    public function create(string $name, string $code, string $description): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO produits (nom, code_produit, description) VALUES (?, ?, ?)');
        return $stmt->execute([$name, $code, $description]);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM produits ORDER BY id DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
