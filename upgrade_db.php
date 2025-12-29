<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo->exec("ALTER TABLE users ADD COLUMN qr_image VARCHAR(255) NULL AFTER qr_token");
    echo "Column qr_image added successfully.\n";
} catch (PDOException $e) {
    echo "Error adding column (might already exist): " . $e->getMessage() . "\n";
}
?>
