<?php
require_once 'session_check.php';
include 'templates/header.php';
?>

<div class="container">
    <h1>Toutes les actions</h1>
    <p>Cette page affiche tous les boutons qui mènent aux différentes pages du système.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="trips_readonly.php" class="btn btn-primary">Dashboard</a>
        <a href="trips.php" class="btn btn-secondary">Trajets</a>
        <a href="trucks.php" class="btn btn-secondary">Camions</a>
        <a href="add_trip.php" class="btn btn-success">Créer un voyage</a>
        <a href="end_trip.php" class="btn btn-info">Fin de voyage (scan QR)</a>
        <a href="in_progress_trips.php" class="btn btn-warning">Voyages en cours</a>
        <a href="add_driver.php" class="btn btn-success">Ajouter conducteur</a>
        <a href="manage_points_goods.php" class="btn btn-info">Gestion Points & Marchandises</a>
        <a href="add_truck.php" class="btn btn-success">Ajouter Camion</a>
        <a href="trucks.php" class="btn btn-secondary">Liste Camions</a>
        <a href="register_admin.php" class="btn btn-danger">Créer un admin</a>
        <a href="forgot_password.php" class="btn btn-primary">Mot de passe oublié</a>
        <a href="logout.php" class="btn btn-outline">Se déconnecter</a>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
