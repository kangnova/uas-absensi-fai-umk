<?php
require_once __DIR__ . '/config/database.php';

try {
    // Get all schedule IDs
    $stmt = $pdo->query("SELECT id FROM exam_schedules");
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $semesters = ['I', 'III', 'V', 'VII', 'VII Non Reg'];
    
    $stmtUpdate = $pdo->prepare("UPDATE exam_schedules SET semester = ? WHERE id = ?");
    
    foreach ($ids as $key => $id) {
        // Round robin assignment
        $sem = $semesters[$key % count($semesters)];
        $stmtUpdate->execute([$sem, $id]);
    }
    
    echo "Updated " . count($ids) . " schedules with sample semesters.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
