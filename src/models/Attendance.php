<?php
namespace App\Models;

use PDO;

class Attendance {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create($userId, $status, $scheduleId = null) {
        $stmt = $this->pdo->prepare("INSERT INTO attendance (user_id, schedule_id, status, timestamp_in) VALUES (:uid, :sid, :status, NOW())");
        return $stmt->execute(['uid' => $userId, 'sid' => $scheduleId, 'status' => $status]);
    }

    public function getRecent($limit = 50) {
        $stmt = $this->pdo->query("
            SELECT a.*, u.nama, u.nip_nidn, s.session_name
            FROM attendance a 
            JOIN users u ON a.user_id = u.id 
            LEFT JOIN exam_schedules s ON a.schedule_id = s.id
            ORDER BY a.timestamp_in DESC 
            LIMIT $limit
        ");
        return $stmt->fetchAll();
    }

    public function checkToday($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM attendance WHERE user_id = :uid AND DATE(timestamp_in) = CURDATE()");
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetch();
    }

    public function checkSchedule($userId, $scheduleId) {
        $stmt = $this->pdo->prepare("SELECT * FROM attendance WHERE user_id = :uid AND schedule_id = :sid");
        $stmt->execute(['uid' => $userId, 'sid' => $scheduleId]);
        return $stmt->fetch();
    }
}
