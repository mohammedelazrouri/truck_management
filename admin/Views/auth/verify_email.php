<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification Email</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

<div class="login-container">
    <h2>Vérification Email</h2>
    <p style="color: #666; text-align: center; margin-bottom: 20px;">
        Entrez le code reçu par email pour vérifier votre compte.
    </p>

    <?php if (!empty($message)): ?>
        <div class="login-error" style="margin-bottom: 20px;">
            <?php echo htmlspecialchars($message, ENT_QUOTES); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="code">Code</label>
            <input type="text" name="code" id="code" required>
        </div>

        <button type="submit">Vérifier</button>
    </form>
</div>

</body>
</html>
