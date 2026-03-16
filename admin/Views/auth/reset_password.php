<?php
// Variables provided by controller:
// - $token
// - $message
// - $error
// - $admin
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
<div class="login-container">
    <h2>Nouveau mot de passe</h2>

    <?php if ($error): ?>
        <div class="login-error" style="display:block;"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="login-success" style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
            <?php echo $message; ?>
            <p><small>Redirection vers la page de connexion...</small></p>
        </div>
    <?php endif; ?>

    <?php if ($admin && !$message): ?>
        <form method="POST">
            <div class="form-group">
                <label>Nouveau mot de passe</label>
                <input type="password" name="password" required minlength="6">
            </div>
            <div class="form-group">
                <label>Confirmer le mot de passe</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit">Enregistrer</button>
        </form>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 20px;">
        <a href="login.php" style="text-decoration:none; color: #666;">← Retour</a>
    </div>
</div>
</body>
</html>
