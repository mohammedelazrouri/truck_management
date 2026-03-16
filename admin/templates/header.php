<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Gestion des Camions</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="admin-nav">
        <div class="nav-container">
            <?php
                $role = $_SESSION['admin_role'] ?? '';
                $homeLink = ($role === 'principal') ? 'dashboard.php' : 'trips_readonly.php';
            ?>
            <a href="<?= $homeLink ?>" class="nav-logo">🚚 Admin Dashboard</a>
            <ul class="nav-menu">
                <?php if($role !== 'pointer'): ?>
                    <li><a href="<?= $homeLink ?>">📊 Dashboard</a></li>
                <?php endif; ?>

                <?php if($role !== 'pointer'): ?>
                    <li><a href="trips.php">📍 Trajets</a></li>
                    <li><a href="trucks.php">🚚 Camions</a></li>
                    <li><a href="cv_pdf.php">📝 CV PDF</a></li>
                <?php endif; ?>

                <?php if($role === 'principal'): ?>
                    <li><a href="register_admin.php">➕ Créer Admin</a></li>
                    <li><a href="users.php">👥 Gestion Utilisateurs</a></li>
                    <li><a href="add_driver.php">👤 Ajouter Conducteur</a></li>
                <?php endif; ?>

                <?php if($role === 'pointer'): ?>
                    <li><a href="pointer.php">🏠 Accueil Pointer</a></li>
                <?php endif; ?>

                <li><a href="logout.php">🚪 Déconnexion</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
