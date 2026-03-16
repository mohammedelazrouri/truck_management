<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="/admin/style.css">
</head>
<body class="login-page">
<div class="login-container">
    <h2>Connexion</h2>
    <form action="/login" method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-primary" type="submit">Se connecter</button>
    </form>
</div>
</body>
</html>
