<?php
namespace App\Models;

use PDO;

class Attendance {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create($userId, $status) {
        $stmt = $this->pdo->prepare("INSERT INTO attendance (user_id, status, timestamp_in) VALUES (:uid, :status, NOW())");
        return $stmt->execute(['uid' => $userId, 'status' => $status]);
    }

    public function getRecent($limit = 50) {
        $stmt = $this->pdo->query("
            SELECT a.*, u.nama, u.nip_nidn 
            FROM attendance a 
            JOIN users u ON a.user_id = u.id 
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
}
