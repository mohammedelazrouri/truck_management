<?php

namespace Admin\Controllers;

use Admin\Models\Admin;

class AuthController
{
    private Admin $admin;

    public function __construct($pdo)
    {
        $this->admin = new Admin($pdo);
    }

    public function showLoginForm(): void
    {
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            echo json_encode([ 'success' => false, 'message' => 'Veuillez remplir tous les champs' ]);
            return;
        }

        $admin = $this->admin->findByEmail($email);
        if (!$admin) {
            echo json_encode([ 'success' => false, 'message' => 'البريد غير موجود' ]);
            return;
        }

        if ($admin['is_verified'] != 1) {
            echo json_encode([ 'success' => false, 'message' => 'يرجى التحقق من البريد قبل تسجيل الدخول' ]);
            return;
        }

        if (!$this->admin->verifyPassword($password, $admin['mot_de_passe'])) {
            echo json_encode([ 'success' => false, 'message' => 'كلمة المرور غير صحيحة' ]);
            return;
        }

        // حفظ بيانات الجلسة
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_nom']  = $admin['nom'];
        $_SESSION['admin_role'] = $admin['role'];

        echo json_encode([
            'success'     => true,
            'admin_id'    => $admin['id'],
            'admin_nom'   => $admin['nom'],
            'admin_role'  => $admin['role'],
        ]);
    }
}
