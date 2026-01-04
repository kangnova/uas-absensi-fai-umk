<?php
require_once __DIR__ . '/config/database.php';
$stmt = $pdo->query("SELECT id, date, session_name, prodi, semester, mata_kuliah FROM exam_schedules LIMIT 20");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
