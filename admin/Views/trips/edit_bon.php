<?php
// Variables provided by controller:
// - $trip
?>

<?php include __DIR__ . '/../../templates/header.php'; ?>

<h2>Modifier Bon - Voyage #<?= htmlspecialchars($trip['id']) ?></h2>

<form method="POST" style="max-width:500px">

    <label>Bon Pour</label>
    <input type="text" name="bon_pour"
           value="<?= htmlspecialchars($trip['bon_pour']) ?>"
           required style="width:100%;padding:8px;margin-bottom:15px;">

    <label>Bon Livraison</label>
    <input type="text" name="bon_livraison"
           value="<?= htmlspecialchars($trip['bon_livraison']) ?>"
           required style="width:100%;padding:8px;margin-bottom:15px;">

    <button type="submit" style="padding:10px 20px;background:#007bff;color:#fff;border:none;border-radius:5px;">
        💾 Enregistrer
    </button>

    <a href="trips.php" style="margin-left:10px;">Annuler</a>

</form>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
