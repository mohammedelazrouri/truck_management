<?php
// Variables provided by controller:
// - $trips
// - $search
// - $errorMsg
?>

<?php include __DIR__ . '/../../templates/header.php'; ?>

<h2>Trips In Progress</h2>

<?php if (!empty($errorMsg)): ?>
    <div style="color:red; font-weight:bold;"><?= htmlspecialchars($errorMsg) ?></div>
<?php endif; ?>

<form method="GET">
    <input type="text" name="search" placeholder="Search driver or destination..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>

<br>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Destination</th>
        <th>Start Date</th>
        <th>Driver</th>
        <th>Action</th>
    </tr>

    <?php if (!empty($trips)): ?>
        <?php foreach ($trips as $trip): ?>
            <tr>
                <td><?= $trip['id'] ?></td>
                <td><?= htmlspecialchars($trip['destination_name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($trip['start_time']) ?></td>
                <td><?= htmlspecialchars($trip['driver_name'] ?? '-') ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="trip_id" value="<?= $trip['id'] ?>">
                        <input type="text" name="cancel_reason" placeholder="Reason..." required>
                        <button type="submit" name="cancel_trip" onclick="return confirm('Are you sure you want to cancel this trip?')">
                            Annuler
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5">No trips found.</td>
        </tr>
    <?php endif; ?>
</table>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
