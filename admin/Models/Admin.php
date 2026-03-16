<?php

namespace Admin\Models;

use PDO;

class Admin
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admins WHERE email = ?');
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        return $admin ?: null;
    }

    public function verifyEmailToken(string $email, string $token): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT id FROM admins WHERE email = ? AND verification_token = ? AND verification_expires > NOW()'
        );
        $stmt->execute([$email, $token]);
        if (!$stmt->fetch()) {
            return false;
        }

        $update = $this->pdo->prepare(
            'UPDATE admins SET is_verified = 1, verification_token = NULL, verification_expires = NULL WHERE email = ?'
        );

        return $update->execute([$email]);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function findByResetToken(string $token): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admins WHERE reset_token = ? AND reset_expires > NOW()');
        $stmt->execute([$token]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        return $admin ?: null;
    }

    public function updatePassword(int $adminId, string $password): bool
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare(
            'UPDATE admins SET mot_de_passe = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?'
        );
        return $stmt->execute([$hashedPassword, $adminId]);
    }

    public function generateResetTokenForEmail(string $email): ?array
    {
        $admin = $this->findByEmail($email);
        if (!$admin) {
            return null;
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->pdo->prepare('UPDATE admins SET reset_token = ?, reset_expires = ? WHERE email = ?');
        $success = $stmt->execute([$token, $expires, $email]);

        if (!$success) {
            return null;
        }

        return [
            'token' => $token,
            'name' => $admin['nom'],
        ];
    }
}
