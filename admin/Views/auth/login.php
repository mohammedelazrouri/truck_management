<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

<div class="login-container">
    <h2>Connexion Administration</h2>
    <div class="login-error" id="error-msg"></div>

    <form id="login-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" required autocomplete="off">
        </div>

        <button type="submit">Se connecter</button>

        <button type="button" onclick="window.location.href='forgot_password.php'" style="background-color: #6c757d; margin-top: 10px;">
            Mot de passe oublié ?
        </button>
    </form>
</div>

<script>
document.getElementById('login-form').addEventListener('submit', function(e){
    e.preventDefault();

    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const errorDiv = document.getElementById('error-msg');

    fetch('auth.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'email=' + encodeURIComponent(email) + '&password=' + encodeURIComponent(password)
    })
    .then(r => r.json())
    .then(res => {
        if(res.success){
            localStorage.setItem('admin_id', res.admin_id);
            localStorage.setItem('admin_nom', res.admin_nom);
            localStorage.setItem('admin_role', res.admin_role);

            if(res.admin_role === 'principal'){
                window.location.href = 'dashboard.php';
            } else if (res.admin_role === 'admin') {
                window.location.href = 'trips_readonly.php';
            } else if (res.admin_role === 'pointer') {
                window.location.href = 'pointer.php';
            } else {
                // fallback
                window.location.href = 'trips_readonly.php';
            }
        } else {
            errorDiv.textContent = res.message;
        }
    })
    .catch(err => {
        console.error(err);
        errorDiv.textContent = "خطأ أثناء الاتصال بالخادم";
    });
});
</script>

</body>
</html>
