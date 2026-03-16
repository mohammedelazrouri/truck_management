<?php

namespace Admin\Controllers;

use Admin\Models\Admin;

class ResetPasswordController
{
    private Admin $admin;

    public function __construct($pdo)
    {
        $this->admin = new Admin($pdo);
    }

    public function handle(): void
    {
        $token = $_GET['token'] ?? '';
        $message = '';
        $error = '';
        $admin = null;

        if (!$token) {
            $error = 'Token مفقود. يرجى استخدام الرابط المرسل إلى بريدك الإلكتروني.';
        } else {
            $admin = $this->admin->findByResetToken($token);
            if (!$admin) {
                $error = 'Ce lien est invalide ou a expiré.';
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $admin) {
            $newPassword = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (strlen($newPassword) < 6) {
                $error = 'Le mot de passe doit contenir au moins 6 caractères.';
            } elseif ($newPassword !== $confirmPassword) {
                $error = 'Les mots de passe ne correspondent pas.';
            } else {
                if ($this->admin->updatePassword((int)$admin['id'], $newPassword)) {
                    $message = 'Votre mot de passe a été réinitialisé avec succès !';
                } else {
                    $error = 'Erreur lors de la mise à jour du mot de passe.';
                }
            }
        }

        require __DIR__ . '/../Views/auth/reset_password.php';
    }
}
