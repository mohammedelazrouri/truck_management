<?php
require_once '../session_check.php';
require_once '../../config/db.php';
require_once '../../config/send_mail.php';

header('Content-Type: application/json; charset=utf-8');

// Only principal can update users
$role = $_SESSION['admin_role'] ?? '';
if ($role !== 'principal') {
    echo json_encode(['success' => false, 'message' => 'Accès refusé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$user_id   = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$new_role  = isset($_POST['role']) ? trim($_POST['role']) : '';
$new_email = isset($_POST['email']) ? trim($_POST['email']) : '';

if (!$user_id || !in_array($new_role, ['principal', 'admin', 'pointer']) || !filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

$currentUserId = $_SESSION['admin_id'] ?? 0;

try {
    // fetch existing user info
    $stmt = $pdo->prepare("SELECT email, nom FROM admins WHERE id = ?");
    $stmt->execute([$user_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$existing) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur introuvable']);
        exit;
    }

    $emailChanged = false;
    if ($existing['email'] !== $new_email) {
        // ensure new email is not already used by someone else
        $check = $pdo->prepare("SELECT id FROM admins WHERE email = ? AND id != ?");
        $check->execute([$new_email, $user_id]);
        if ($check->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
            exit;
        }
        $emailChanged = true;
    }

    // begin transaction
    $pdo->beginTransaction();

    if ($emailChanged) {
        $code = rand(100000, 999999);
        $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $update = $pdo->prepare("UPDATE admins SET email = ?, is_verified = 0, verification_token = ?, verification_expires = ? WHERE id = ?");
        $update->execute([$new_email, $code, $expires, $user_id]);

        // send verification mail
        $mailResult = sendVerificationEmail($new_email, $existing['nom'], $code);
        if ($mailResult !== true) {
            // rollback transaction and return error
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo json_encode(['success' => false, 'message' => 'Erreur SMTP : impossible d\'envoyer le mail de vérification']);
            exit;
        }

        // always set verify_email in session so that the redirect page can use it
        $_SESSION['verify_email'] = $new_email;
    }

    // update role if changed or always
    $stmtRole = $pdo->prepare("UPDATE admins SET role = ? WHERE id = ?");
    $stmtRole->execute([$new_role, $user_id]);

    $pdo->commit();

    $response = ['success' => true, 'message' => 'Utilisateur mis à jour avec succès', 'email_changed' => $emailChanged];
    if ($emailChanged) {
        // always ask client to redirect so the verification page can be shown
        $response['redirect'] = true;
    }
    echo json_encode($response);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error updating user: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
}
?>