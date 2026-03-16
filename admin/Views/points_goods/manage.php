<?php
// Variables provided by controller:
// - $message
// - $points
// - $products
?>

<?php include __DIR__ . '/../../templates/header.php'; ?>

<h2>Gestion des Points & Produits</h2>

<?php if ($message): ?>
    <p><strong><?= htmlspecialchars($message) ?></strong></p>
<?php endif; ?>

<!-- ================= POINT FORM ================= -->
<h3>Ajouter Point</h3>
<form method="POST">
    Nom:
    <input type="text" name="point_name" required>

    Type:
    <select name="point_type" required>
        <option value="origin">Origin</option>
        <option value="destination">Destination</option>
        <option value="both">Both</option>
    </select>

    <button type="submit" name="add_point">Ajouter</button>
</form>

<hr>

<!-- ================= PRODUIT FORM ================= -->
<h3>Ajouter Produit</h3>
<form method="POST">
    Nom:
    <input type="text" name="produit_name" required>

    Code Produit:
    <input type="text" name="produit_code">

    Description:
    <input type="text" name="produit_description">

    <button type="submit" name="add_produit">Ajouter</button>
</form>

<hr>

<h3>Liste des Points</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Type</th>
    </tr>
    <?php foreach ($points as $point): ?>
        <tr>
            <td><?= $point['id'] ?></td>
            <td><?= htmlspecialchars($point['nom']) ?></td>
            <td><?= htmlspecialchars($point['type']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<hr>

<h3>Liste des Produits</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Code</th>
        <th>Description</th>
    </tr>
    <?php foreach ($products as $prod): ?>
        <tr>
            <td><?= $prod['id'] ?></td>
            <td><?= htmlspecialchars($prod['nom']) ?></td>
            <td><?= htmlspecialchars($prod['code_produit']) ?></td>
            <td><?= htmlspecialchars($prod['description']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
