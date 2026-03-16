<?php

namespace Admin\Controllers;

use Admin\Models\Admin;

class SendResetLinkController
{
    private Admin $admin;

    public function __construct($pdo)
    {
        $this->admin = new Admin($pdo);
    }

    public function handle(): void
    {
        // Prevent PHP warnings from breaking JSON
        error_reporting(0);
        ini_set('display_errors', 0);

        header('Content-Type: application/json');

        $response = ['success' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode($response);
            return;
        }

        $email = trim($_POST['email'] ?? '');
        if ($email === '') {
            $response['message'] = 'Veuillez entrer votre email.';
            echo json_encode($response);
            return;
        }

        $resetData = $this->admin->generateResetTokenForEmail($email);
        if (!$resetData) {
            $response['message'] = 'Cet email n\'existe pas.';
            echo json_encode($response);
            return;
        }

        // Build reset link (update host as needed for production)
        $resetLink = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . dirname($_SERVER['PHP_SELF']) . '/reset_password.php?token=' . $resetData['token'];

        require_once __DIR__ . '/../../config/send_mail.php';
        $mailResult = sendVerificationEmail($email, $resetData['name'], $resetLink);

        if ($mailResult === true) {
            $response['success'] = true;
            $response['message'] = 'Lien envoyé ! Vérifiez votre boîte mail.';
        } else {
            $response['message'] = 'Erreur lors de l\'envoi de l\'email.';
        }

        echo json_encode($response);
    }
}
