<?php
require_once __DIR__ . '/config/database.php';

try {
    echo "Upgrading database v3...\n";

    // Add missing columns to exam_schedules
    $columns = [
        'prodi' => "VARCHAR(50) NULL AFTER date",
        'semester' => "VARCHAR(50) NULL AFTER prodi",
        'mata_kuliah' => "VARCHAR(255) NULL AFTER session_name",
        'pengawas' => "TEXT NULL AFTER end_time"
    ];

    foreach ($columns as $name => $definition) {
        try {
            $pdo->exec("ALTER TABLE exam_schedules ADD COLUMN $name $definition");
            echo "Added column '$name'.\n";
        } catch (PDOException $e) {
            echo "Column '$name' likely already exists or error: " . $e->getMessage() . "\n";
        }
    }

    echo "Database upgrade v3 completed.\n";

} catch (PDOException $e) {
    die("Global Error: " . $e->getMessage() . "\n");
}
?>
