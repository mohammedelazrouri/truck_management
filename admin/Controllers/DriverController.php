<?php

namespace Admin\Controllers;

use Admin\Models\Driver;

class DriverController
{
    private Driver $driver;

    public function __construct($pdo)
    {
        $this->driver = new Driver($pdo);
    }

    public function create(): void
    {
        $message = '';
        $messageType = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => trim($_POST['driver_nom'] ?? ''),
                'email' => trim($_POST['driver_email'] ?? ''),
                'telephone' => trim($_POST['driver_telephone'] ?? ''),
                'password' => $_POST['driver_password'] ?? '',
            ];

            if ($data['nom'] === '' || $data['email'] === '' || $data['telephone'] === '' || $data['password'] === '') {
                $message = 'Tous les champs sont obligatoires.';
                $messageType = 'danger';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $message = 'Email invalide.';
                $messageType = 'danger';
            } elseif (strlen($data['password']) < 6) {
                $message = 'Le mot de passe doit contenir au moins 6 caractères.';
                $messageType = 'danger';
            } elseif ($this->driver->existsByEmail($data['email'])) {
                $message = 'Email déjà utilisé pour un conducteur.';
                $messageType = 'danger';
            } else {
                $data['mot_de_passe'] = password_hash($data['password'], PASSWORD_DEFAULT);

                if ($this->driver->create($data)) {
                    $message = 'Conducteur créé avec succès !';
                    $messageType = 'success';
                } else {
                    $message = 'Erreur lors de la création.';
                    $messageType = 'danger';
                }
            }
        }

        require __DIR__ . '/../Views/drivers/add.php';
    }
}
