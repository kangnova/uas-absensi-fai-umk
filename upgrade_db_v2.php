<?php
require_once __DIR__ . '/config/database.php';

try {
    // 1. Add PRODI column to users
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN prodi ENUM('PAI', 'PIAUD') NULL AFTER nama");
        echo "Column 'prodi' added to users.\n";
    } catch (PDOException $e) { /* Ignore if exists */ }

    // 2. Modify JABATAN to SET to allow multiple roles
    // Note: detailed implementation might vary, but SET is standard for multi-select.
    // Existing data 'Panitia' or 'Pengawas' is valid for SET.
    try {
        $pdo->exec("ALTER TABLE users MODIFY COLUMN jabatan SET('Panitia', 'Pengawas') NOT NULL");
        echo "Column 'jabatan' modified to SET.\n";
    } catch (PDOException $e) { 
        echo "Error modifying 'jabatan' (might be incompatible data?): " . $e->getMessage() . "\n";
    }

    // 3. Create EXAM_SCHEDULES table
    $pdo->exec("CREATE TABLE IF NOT EXISTS exam_schedules (
        id INT PRIMARY KEY AUTO_INCREMENT,
        date DATE NOT NULL,
        session_name VARCHAR(100) NOT NULL, -- e.g. 'Sesi 1', 'Jam ke-1'
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Table 'exam_schedules' created.\n";

    // 4. Add SCHEDULE_ID to attendance
    try {
        $pdo->exec("ALTER TABLE attendance ADD COLUMN schedule_id INT NULL AFTER user_id");
        $pdo->exec("ALTER TABLE attendance ADD INDEX (schedule_id)");
        echo "Column 'schedule_id' added to attendance.\n";
    } catch (PDOException $e) { /* Ignore if exists */ }

    echo "Database upgrade v2 completed successfully.\n";

} catch (PDOException $e) {
    die("Global Error: " . $e->getMessage() . "\n");
}
?>
