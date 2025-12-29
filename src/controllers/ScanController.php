<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Attendance;
use PDO;

class ScanController {
    private $userModel;
    private $attendanceModel;

    public function __construct(PDO $pdo) {
        $this->userModel = new User($pdo);
        $this->attendanceModel = new Attendance($pdo);
    }

    public function process($token) {
        try {
            $user = $this->userModel->findByToken($token);

            if (!$user) {
                return ['status' => 'error', 'message' => 'QR Code tidak valid / User tidak ditemukan'];
            }

            $existing = $this->attendanceModel->checkToday($user['id']);

            if ($existing) {
                return [
                    'status' => 'warning',
                    'message' => 'User sudah absen hari ini pada ' . date('H:i', strtotime($existing['timestamp_in'])),
                    'detail' => $user
                ];
            }

            $status = (date('H:i') > '08:00') ? 'Telat' : 'Hadir';
            $this->attendanceModel->create($user['id'], $status);

            return [
                'status' => 'success',
                'message' => 'Absensi berhasil mencatat kehadiran.',
                'detail' => $user
            ];

        } catch (\Exception $e) {
            error_log($e->getMessage());
            return ['status' => 'error', 'message' => 'Database error'];
        }
    }
}
