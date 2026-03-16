<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

<div class="login-container">
    <h2>Mot de Passe Oublié</h2>
    <p style="color: #666; text-align: center; margin-bottom: 20px;">
        Entrez votre adresse email pour recevoir un lien de réinitialisation
    </p>

    <div class="login-error" id="error-msg"></div>
    <div class="login-success" id="success-msg"></div>

    <form id="forgot-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="votre@email.com" required>
        </div>

        <button type="submit" id="submit-btn">Envoyer le lien</button>
    </form>

    <div style="text-align: center; margin-top: 20px;">
        <a href="login.php" style="color: var(--primary); text-decoration: none;">← Retour à la connexion</a>
    </div>
</div>

<script>
document.getElementById('forgot-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const email = document.getElementById('email').value.trim();
    const errorDiv = document.getElementById('error-msg');
    const successDiv = document.getElementById('success-msg');
    const submitBtn = document.getElementById('submit-btn');

    errorDiv.textContent = '';
    successDiv.textContent = '';
    submitBtn.disabled = true;
    submitBtn.textContent = 'Envoi en cours...';

    fetch('send_reset_link.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'email=' + encodeURIComponent(email)
    })
    .then(r => {
        if (!r.ok) {
            throw new Error('HTTP error ' + r.status);
        }
        return r.json();
    })
    .then(res => {
        if(res.success) {
            successDiv.textContent = res.message || 'Lien de réinitialisation envoyé à votre email';
            document.getElementById('forgot-form').reset();
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 3000);
        } else {
            errorDiv.textContent = res.message || 'Une erreur est survenue';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Envoyer le lien';
        }
    })
    .catch(err => {
        console.error('Forgot password error', err);
        errorDiv.textContent = 'Erreur de connexion au serveur';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Envoyer le lien';
    });
});
</script>

</body>
</html>
