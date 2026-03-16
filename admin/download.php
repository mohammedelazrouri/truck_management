<?php
// download.php
$filename = $_GET['file'] ?? '';
$path = __DIR__ . '/../tickets/' . basename($filename); // sécurise le nom de fichier

if (!file_exists($path)) {
    die('❌ Fichier introuvable.');
}

// Forcer le téléchargement
header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . basename($path) . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;