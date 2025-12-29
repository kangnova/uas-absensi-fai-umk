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
        $stmt = $this->pdo->prepare("INSERT INTO exam_schedules (date, prodi, session_name, mata_kuliah, start_time, end_time, pengawas) VALUES (:date, :prodi, :session, :mk, :start, :end, :pengawas)");
        
        // Pengawas is array, implode it
        $pengawas = is_array($data['pengawas']) ? implode(', ', $data['pengawas']) : $data['pengawas'];

        return $stmt->execute([
            'date' => $data['date'],
            'prodi' => $data['prodi'],
            'session' => $data['session'],
            'mk' => $data['mata_kuliah'],
            'start' => $data['start'],
            'end' => $data['end'],
            'pengawas' => $pengawas
        ]);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM exam_schedules WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE exam_schedules SET date = :date, prodi = :prodi, session_name = :session, mata_kuliah = :mk, start_time = :start, end_time = :end, pengawas = :pengawas WHERE id = :id");
        
        $pengawas = is_array($data['pengawas']) ? implode(', ', $data['pengawas']) : $data['pengawas'];

        return $stmt->execute([
            'id' => $id,
            'date' => $data['date'],
            'prodi' => $data['prodi'],
            'session' => $data['session'],
            'mk' => $data['mata_kuliah'],
            'start' => $data['start'],
            'end' => $data['end'],
            'pengawas' => $pengawas
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
