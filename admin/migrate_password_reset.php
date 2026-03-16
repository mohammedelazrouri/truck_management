<?php
/**
 * Migration Script for Password Reset Feature
 * Run this once to add the required columns to the admins table
 */

require_once '../config/db.php';

try {
    // Add reset_token and token_expiry columns to admins table
    $sql = "ALTER TABLE admins ADD COLUMN reset_token VARCHAR(255) NULL DEFAULT NULL AFTER mot_de_passe;";
    $pdo->exec($sql);
    echo "✓ Column reset_token added successfully<br>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') === false) {
        echo "✗ Error adding reset_token: " . $e->getMessage() . "<br>";
    } else {
        echo "⚠ Column reset_token already exists<br>";
    }
}

try {
    $sql = "ALTER TABLE admins ADD COLUMN token_expiry DATETIME NULL DEFAULT NULL AFTER reset_token;";
    $pdo->exec($sql);
    echo "✓ Column token_expiry added successfully<br>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') === false) {
        echo "✗ Error adding token_expiry: " . $e->getMessage() . "<br>";
    } else {
        echo "⚠ Column token_expiry already exists<br>";
    }
}

echo "<p style='color: green; margin-top: 20px;'><strong>Migration completed!</strong> You can now use the forgot password feature.</p>";
?>
