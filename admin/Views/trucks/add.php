<?php
// Variables provided by controller:
// - $successMsg
// - $errorMsg
// - $messageType
?>

<?php include __DIR__ . '/../../templates/header.php'; ?>

<div class="add-truck-container">
    <div class="page-header">
        <h1>🚚 Ajouter un nouveau camion</h1>
        <a href="trucks.php" class="btn btn-outline">← Retour</a>
    </div>

    <?php if (!empty($errorMsg)): ?>
        <div class="badge badge-danger" style="display: block; text-align: center; margin-bottom: 1rem; padding: 1rem; width: 100%; border-radius: var(--radius-md);">❌ <?= htmlspecialchars($errorMsg) ?></div>
    <?php elseif (!empty($successMsg)): ?>
        <div class="badge badge-success" style="display: block; text-align: center; margin-bottom: 1rem; padding: 1rem; width: 100%; border-radius: var(--radius-md);"><?= $successMsg ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="post">
            <fieldset>
                <legend>Informations du camion</legend>
                <div class="form-group">
                    <label for="plaque">Plaque d'immatriculation *</label>
                    <input type="text" id="plaque" name="plaque" placeholder="Ex: AB-123-CD" required>
                </div>
                <div class="form-group">
                    <label for="type">Type de camion</label>
                    <input type="text" id="type" name="type" placeholder="Ex: Semi-remorque, Benne">
                </div>
                <div class="form-group">
                    <label for="code">Code</label>
                    <input type="text" id="code" name="code" placeholder="Ex: 123456789" required>
                </div>
                <div class="form-group">
                    <label for="statut">Statut *</label>
                    <select id="statut" name="statut" required>
                        <option value="">-- Sélectionnez un statut --</option>
                        <option value="disponible">✅ Disponible</option>
                        <option value="en_mission">🚗 En mission</option>
                        <option value="en_maintenance">🔧 En maintenance</option>
                    </select>
                </div>
            </fieldset>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" style="width: 100%;">Ajouter le camion</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
