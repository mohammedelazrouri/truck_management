<?php
session_start();

// مسح كل بيانات الجلسة
$_SESSION = [];

// تدمير الجلسة
session_destroy();

// إعادة التوجيه للصفحة الرئيسية أو تسجيل الدخول
header("Location: login.php");
exit;
?>
