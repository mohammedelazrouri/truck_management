<?php
require_once '../session_check.php';  // حماية الصفحات
require_once '../../config/db.php';

if (!isset($_GET['action'])) {
    header("Location: ../trips_readonly.php");
    exit;
}

$action = $_GET['action'];

switch($action) {
    // إضافة سائق
    case 'add':
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO Drivers (nom, email, mot_de_passe) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);

        header("Location: ../drivers.php?success=1");
        break;

    // تعديل سائق
    case 'edit':
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];

        $stmt = $pdo->prepare("UPDATE Drivers SET nom = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $id]);

        header("Location: ../drivers.php?updated=1");
        break;

    // حذف سائق
    case 'delete':
        $id = $_GET['id'];
        $stmt = $pdo->prepare("DELETE FROM Drivers WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: ../drivers.php?deleted=1");
        break;

    default:
        header("Location: ../drivers.php");
        break;
}
?>
