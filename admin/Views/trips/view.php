<?php
// Variables provided by controller:
// - $trip
// - $trucks
// - $drivers
// - $admins
// - $points
// - $message (optional)
// - $success (optional)
?>

<?php include __DIR__ . '/../../templates/header.php'; ?>

<div class="page-header">
    <h1>Détails du Voyage #<?= htmlspecialchars($trip['id']) ?></h1>
</div>

<?php if (!empty($success)): ?>
    <div class="badge badge-success" style="display: block; text-align: center; margin-bottom: 1rem; padding: 1rem; width: 100%; border-radius: var(--radius-md);">
        <?= htmlspecialchars($message) ?>
    </div>
<?php elseif (!empty($message)): ?>
    <div class="badge badge-danger" style="display: block; text-align: center; margin-bottom: 1rem; padding: 1rem; width: 100%; border-radius: var(--radius-md);">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <h3>Détails du Voyage</h3>
        <dl class="row">
            <dt class="col-sm-3">Truck:</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($trip['plaque']) ?></dd>

            <dt class="col-sm-3">Driver:</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($trip['driver_name']) ?></dd>

            <dt class="col-sm-3">Created by:</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($trip['created_name']) ?></dd>

            <dt class="col-sm-3">Completed by:</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($trip['completed_name']) ?></dd>

            <dt class="col-sm-3">Origin:</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($trip['origin_name']) ?></dd>

            <dt class="col-sm-3">Destination:</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($trip['destination_name']) ?></dd>

            <dt class="col-sm-3">Start Time:</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($trip['start_time']) ?></dd>

            <dt class="col-sm-3">End Time:</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($trip['end_time']) ?></dd>

            <dt class="col-sm-3">Status:</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($trip['status']) ?></dd>

            <dt class="col-sm-3">Cancel Reason:</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($trip['cancel_reason']) ?></dd>

            <dt class="col-sm-3">Bon Pour:</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($trip['bon_pour']) ?></dd>

            <dt class="col-sm-3">Bon Livraison:</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($trip['bon_livraison']) ?></dd>
        </dl>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h3>Modifier le Voyage</h3>
        <form method="POST">
            <div class="form-group">
                <label>Truck</label>
                <select name="truck_id" class="form-control">
                    <?php foreach ($trucks as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= $trip['truck_id'] == $t['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['plaque']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Driver</label>
                <select name="driver_id" class="form-control">
                    <?php foreach ($drivers as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= $trip['driver_id'] == $d['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Created By</label>
                <select name="created_by" class="form-control">
                    <?php foreach ($admins as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= $trip['created_by'] == $a['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Completed By</label>
                <select name="completed_by" class="form-control">
                    <option value="">-- None --</option>
                    <?php foreach ($admins as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= $trip['completed_by'] == $a['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Origin</label>
                <select name="origin" class="form-control">
                    <?php foreach ($points as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $trip['origin'] == $p['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Destination</label>
                <select name="destination" class="form-control">
                    <?php foreach ($points as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $trip['destination'] == $p['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Start Time</label>
                <input type="datetime-local" name="start_time" class="form-control"
                       value="<?= $trip['start_time'] ? date('Y-m-d\TH:i', strtotime($trip['start_time'])) : '' ?>">
            </div>

            <div class="form-group">
                <label>End Time</label>
                <input type="datetime-local" name="end_time" class="form-control"
                       value="<?= $trip['end_time'] ? date('Y-m-d\TH:i', strtotime($trip['end_time'])) : '' ?>">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="pending" <?= $trip['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="in_progress" <?= $trip['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="completed" <?= $trip['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= $trip['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>

            <div class="form-group">
                <label>Cancel Reason</label>
                <textarea name="cancel_reason" class="form-control"><?= htmlspecialchars($trip['cancel_reason']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Bon Pour</label>
                <input type="text" name="bon_pour" class="form-control" value="<?= htmlspecialchars($trip['bon_pour']) ?>">
            </div>

            <div class="form-group">
                <label>Bon Livraison</label>
                <input type="text" name="bon_livraison" class="form-control" value="<?= htmlspecialchars($trip['bon_livraison']) ?>">
            </div>

            <button type="submit" name="update_trip" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
