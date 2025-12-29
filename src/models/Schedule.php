<?php
namespace App\Models;

use PDO;

class Schedule {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM exam_schedules ORDER BY date DESC, start_time ASC");
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO exam_schedules (date, session_name, start_time, end_time) VALUES (:date, :session, :start, :end)");
        return $stmt->execute([
            'date' => $data['date'],
            'session' => $data['session'],
            'start' => $data['start'],
            'end' => $data['end']
        ]);
    }

    public function delete($id) {
        return $this->pdo->prepare("DELETE FROM exam_schedules WHERE id = ?")->execute([$id]);
    }

    public function getActiveSchedule() {
        $today = date('Y-m-d');
        $now = date('H:i:s');

        $stmt = $this->pdo->prepare("SELECT * FROM exam_schedules WHERE date = :date AND :now BETWEEN start_time AND end_time LIMIT 1");
        $stmt->execute(['date' => $today, 'now' => $now]);
        return $stmt->fetch();
    }
}
