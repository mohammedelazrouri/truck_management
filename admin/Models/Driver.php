<?php

namespace Admin\Models;

use PDO;

class Driver
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function existsByEmail(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT id FROM drivers WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return (bool) $stmt->fetch();
    }

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO drivers (nom, email, telephone, mot_de_passe) VALUES (:nom, :email, :telephone, :mot_de_passe)'
        );

        return $stmt->execute([
            'nom' => $data['nom'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'mot_de_passe' => $data['mot_de_passe'],
        ]);
    }
}
