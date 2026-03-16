<?php
require_once 'session_check.php';
require_once 'check_principal.php';
require_once '../config/db.php';
require_once '../config/send_mail.php';

$message = "";

if ($_POST) {

    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // إذا role ما وصلش، نعطيو default
    $role = isset($_POST['role']) ? $_POST['role'] : 'admin';

    // تحقق من الإيميل إذا موجود بالفعل
    $check = $pdo->prepare("SELECT id FROM admins WHERE email=?");
    $check->execute([$email]);

    if ($check->fetch()) {
        $message = "Email déjà utilisé.";
    } else {

        $code = rand(100000, 999999); // كود تحقق عشوائي
        $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $stmt = $pdo->prepare(" 
            INSERT INTO admins 
            (nom,email,mot_de_passe,role,is_verified,verification_token,verification_expires)
            VALUES (?,?,?,?,0,?,?)
        ");

        $stmt->execute([$nom, $email, $password, $role, $code, $expires]);

        $result = sendVerificationEmail($email, $nom, $code);

        if ($result === true) {
            $_SESSION['verify_email'] = $email;
            header("Location: verify_email.php"); // redirect لصفحة التحقق
            exit();
        } else {
            $message = "Erreur SMTP : تحقق من إعدادات البريد الإلكتروني.";
        }
    }
}

include 'templates/header.php';
?>

<h2>Créer Admin</h2>
<form method="POST">
    Nom: <input type="text" name="nom" required><br><br>
    Email: <input type="email" name="email" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    Role: 
    <select name="role" required>
        <option value="admin">Admin</option>
        <option value="principal">Principale</option>
        <option value="pointer">Pointer</option>
    </select>
    <br><br>
    <button type="submit">Créer</button>
</form>

<p><?php echo $message; ?></p>
<?php