<?php
// Variables provided by controller:
// - $trucks
// - $successMessage
// - $editTruck
// - $ticketDirWeb
?>

<?php require __DIR__ . '/../../templates/header.php'; ?>

<!-- En‑tête -->
<div class="page-header">
    <h2>🚚 Gestion des Camions</h2>
    <?php if (!$editTruck): ?>
        <a href="add_truck.php" class="btn-add">➕ Ajouter un camion</a>
    <?php endif; ?>
</div>

<!-- Messages -->
<?php if ($successMessage): ?>
    <div class="alert alert-success"><?= $successMessage ?></div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error">❌ Erreur lors de l'opération.</div>
<?php endif; ?>

<!-- Formulaire ajout / édition -->
<div class="card form-section">
    <h3><?= $editTruck ? 'Modifier le Camion' : 'Ajouter un nouveau Camion' ?></h3>
    <form action="actions/truck_actions.php?action=<?= $editTruck ? 'edit' : 'add' ?>" method="post">
        <?php if ($editTruck): ?>
            <input type="hidden" name="id" value="<?= $editTruck['id'] ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="form-group">
                <label>Plaque *</label>
                <input type="text" name="plaque" value="<?= $editTruck ? htmlspecialchars($editTruck['plaque']) : '' ?>" required>
            </div>
            <div class="form-group">
                <label>Type *</label>
                <input type="text" name="type" value="<?= $editTruck ? htmlspecialchars($editTruck['type']) : '' ?>" required>
            </div>
            <div class="form-group">
                <label>Code *</label>
                <input type="text" name="code" value="<?= $editTruck ? htmlspecialchars($editTruck['code']) : '' ?>" required>
            </div>
            <div class="form-group">
                <label>Statut *</label>
                <?php $current_statut = $editTruck ? $editTruck['statut'] : 'disponible'; ?>
                <select name="statut" required>
                    <option value="disponible" <?= $current_statut == 'disponible' ? 'selected' : '' ?>>Disponible</option>
                    <option value="en_maintenance" <?= $current_statut == 'en_maintenance' ? 'selected' : '' ?>>En Maintenance</option>
                    <option value="en_mission" <?= $current_statut == 'en_mission' ? 'selected' : '' ?>>En Mission</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit"><?= $editTruck ? '💾 Modifier' : '➕ Ajouter' ?> Camion</button>
            <?php if ($editTruck): ?>
                <a href="trucks.php" class="button secondary">❌ Annuler</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Liste des Camions -->
<div class="card form-section">
    <h3>Liste des Camions</h3>
    <?php if (empty($trucks)): ?>
        <p style="color: var(--text-muted); text-align: center; padding: 20px;">
            Aucun camion trouvé. <a href="add_truck.php" style="color: var(--accent);">Ajouter un camion</a>
        </p>
    <?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Plaque</th>
                    <th>Type</th>
                    <th>Code</th>
                    <th>Statut</th>
                    <th>QR Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trucks as $truck): ?>
                    <?php $pdfFileName = "truck_{$truck['id']}.pdf"; ?>
                    <tr>
                        <td><?= htmlspecialchars($truck['id']) ?></td>
                        <td><?= htmlspecialchars($truck['plaque']) ?></td>
                        <td><?= htmlspecialchars($truck['type']) ?></td>
                        <td><?= htmlspecialchars($truck['code']) ?></td>
                        <td>
                            <span class="status-badge status-<?= str_replace('_','-', htmlspecialchars($truck['statut'])) ?>">
                                <?= str_replace('_',' ', htmlspecialchars($truck['statut'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (file_exists(__DIR__ . '/../../tickets/' . $pdfFileName)): ?>
                                <a href="download.php?file=<?= $pdfFileName ?>" title="Télécharger QR Code">📄⬇️</a>
                            <?php else: ?>
                                ❌
                            <?php endif; ?>
                            <a href="actions/truck_actions.php?action=regenerate_qr&id=<?= $truck['id'] ?>" title="Régénérer QR Code">🔄</a>
                        </td>
                        <td>
                            <div class="action-links">
                                <a href="trucks.php?edit_id=<?= $truck['id'] ?>">✏️ Modifier</a>
                                <a href="actions/truck_actions.php?action=delete&id=<?= $truck['id'] ?>" class="delete" onclick="return confirm('Supprimer ce camion ?')">🗑️ Supprimer</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../../templates/footer.php'; ?>
