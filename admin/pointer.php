<?php
require_once 'session_check.php';
include 'templates/header.php';
?>

<div class="page-header">
    <h1>Welcome</h1>
</div>

<div style="display: flex; flex-wrap: wrap; gap: 1rem;">
    <a href="add_trip.php" class="btn btn-primary">add_trip</a>
    <a href="end_trip.php" class="btn btn-outline">end_trip</a>
</div>

<?php
include 'templates/footer.php';
?>
