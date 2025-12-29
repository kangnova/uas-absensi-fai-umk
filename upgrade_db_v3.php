<?php
require_once __DIR__ . '/config/database.php';

try {
    // Modify exam_schedules to add new columns
    $sql = "ALTER TABLE exam_schedules 
            ADD COLUMN prodi ENUM('PAI', 'PIAUD') NOT NULL AFTER date,
            ADD COLUMN mata_kuliah VARCHAR(255) NOT NULL AFTER session_name,
            ADD COLUMN pengawas TEXT NULL AFTER end_time";
    
    $pdo->exec($sql);
    echo "Table 'exam_schedules' updated with new columns (prodi, mata_kuliah, pengawas).\n";

} catch (PDOException $e) {
    die("Error adding columns: " . $e->getMessage() . "\n");
}
?>
