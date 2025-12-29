<?php
require_once __DIR__ . '/config/database.php';

try {
    $sql = file_get_contents(__DIR__ . '/database.sql');
    
    // Remove CREATE DATABASE line since it already exists and we are connected to it (partially)
    // Actually config/database.php connects to the DB name directly.
    // If the DB exists, PDO connection succeeds. 
    // We should split commands by semicolon to execute them one by one if needed, 
    // or typically PDO::exec handles multiple queries if emulation is on.
    
    $pdo->exec($sql);
    echo "Tables created successfully and sample data inserted.\n";
} catch (PDOException $e) {
    die("Error executing SQL: " . $e->getMessage() . "\n");
}
?>
