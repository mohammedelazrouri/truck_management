<?php

namespace Admin\Services;

class SessionGuard
{
    private array $session;

    public function __construct(array &$session)
    {
        $this->session = &$session;
    }

    public function requireLogin(): void
    {
        if (empty($this->session['admin_id'])) {
            header('Location: login.php');
            exit;
        }
    }

    public function authorizePage(string $currentPage): void
    {
        $role = $this->session['admin_role'] ?? '';

        if ($role === 'admin' && in_array($currentPage, ['dashboard.php', 'register_admin.php', 'add_driver.php'], true)) {
            echo '<h2>Accès refusé : vous n\'êtes pas autorisé à accéder à cette page.</h2>';
            exit;
        }

        if ($role === 'pointer') {
            $allowed = [
                'pointer.php',
                'add_trip.php',
                'end_trip.php',
                'logout.php',
                'login.php',
                'trip_actions.php',
                'get_trip_by_truck.php',
                'complete_trip.php',
            ];

            if (!in_array($currentPage, $allowed, true)) {
                header('Location: pointer.php');
                exit;
            }
        }
    }
}
