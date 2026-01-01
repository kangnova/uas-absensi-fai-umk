<?php
require_once __DIR__ . '/config/database.php';

try {
    // Modify PRODI column to SET to allow multiple prodis (e.g. 'PAI,PIAUD')
    $sql = "ALTER TABLE users MODIFY COLUMN prodi SET('PAI', 'PIAUD') NULL";
    
    $pdo->exec($sql);
    echo "Table 'users' updated: Column 'prodi' changed to SET type for multiple values.\n";

} catch (PDOException $e) {
    die("Error upgrading database: " . $e->getMessage() . "\n");
}
?>
