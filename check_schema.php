<?php
require_once __DIR__ . '/config/database.php';
try {
    $stmt = $pdo->query("DESCRIBE exam_schedules");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($columns);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
