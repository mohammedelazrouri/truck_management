<?php
$dir = __DIR__;
$files = array_diff(scandir($dir), ['.', '..', 'index.php']);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Tickets PDF</title>
    <link rel="stylesheet" href="../admin/style.css">

</head>

<body>

<h2>📄 Liste des Tickets PDF</h2>

<div class="grid">

<?php foreach($files as $file): 
    if(pathinfo($file, PATHINFO_EXTENSION) !== 'pdf') continue;

    $size = round(filesize($file)/1024,2)." KB";
    $date = date("d-m-Y H:i", filemtime($file));
?>

<div class="card">
    <div class="file-name"><?= htmlspecialchars($file) ?></div>
    <div class="meta">
        Taille: <?= $size ?><br>
        Date: <?= $date ?>
    </div>
    <a class="btn view" href="<?= $file ?>" target="_blank">Voir</a>
    <a class="btn download" href="<?= $file ?>" download>Télécharger</a>
</div>

<?php endforeach; ?>

</div>

</body>
</html>