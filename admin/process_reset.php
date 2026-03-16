<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

if (!isset($_POST['token'], $_POST['password'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

$token = trim($_POST['token']);
$password = trim($_POST['password']);

// Vérifier le token et sa validité
$stmt = $pdo->prepare("SELECT id FROM admins WHERE reset_token = ? AND token_expiry > NOW()");
$stmt->execute([$token]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo json_encode(['success' => false, 'message' => 'Lien invalide ou expiré']);
    exit;
}

// Hacher le nouveau mot de passe
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Mettre à jour le mot de passe et supprimer le token
$stmt = $pdo->prepare("UPDATE admins SET mot_de_passe = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
$stmt->execute([$hashed_password, $admin['id']]);

echo json_encode(['success' => true, 'message' => 'Mot de passe réinitialisé avec succès. Redirection...']);
?>
