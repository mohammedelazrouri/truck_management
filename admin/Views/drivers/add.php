<?php
// Variables provided by controller:
// - $message
// - $messageType
?>

<?php include __DIR__ . '/../../templates/header.php'; ?>

<div class="page-header">
    <h1>Ajouter Conducteur</h1>
</div>

<?php if (!empty($message)): ?>
    <div class="badge badge-<?= htmlspecialchars($messageType) ?>" style="display: block; text-align: center; margin-bottom: 1rem; padding: 1rem; width: 100%; border-radius: var(--radius-md);">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="card">
    <form method="POST" autocomplete="off">
        <div class="form-group">
            <label for="driver_nom">Nom</label>
            <input type="text" id="driver_nom" name="driver_nom" required>
        </div>
        <div class="form-group">
            <label for="driver_email">Email</label>
            <input type="email" id="driver_email" name="driver_email" required>
        </div>
        <div class="form-group">
            <label for="driver_telephone">Téléphone</label>
            <input type="text" id="driver_telephone" name="driver_telephone" required>
        </div>
        <div class="form-group">
            <label for="driver_password">Mot de passe</label>
            <input type="password" id="driver_password" name="driver_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Créer Conducteur</button>
    </form>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
