<?php
require_once __DIR__ . '/config/database.php';

try {
    // Add Semester column
    $sql = "ALTER TABLE exam_schedules ADD COLUMN semester VARCHAR(20) AFTER prodi";
    $pdo->exec($sql);
    echo "Table 'exam_schedules' updated: Column 'semester' added.\n";
} catch (PDOException $e) {
    // Ignore if already exists or handle specific error code
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "Column 'semester' already exists.\n";
    } else {
        die("Error upgrading database: " . $e->getMessage() . "\n");
    }
}
