<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Attendance;
use PDO;

use App\Models\Schedule;

class ScanController {
    private $userModel;
    private $attendanceModel;
    private $scheduleModel;

    public function __construct(PDO $pdo) {
        $this->userModel = new User($pdo);
        $this->attendanceModel = new Attendance($pdo);
        $this->scheduleModel = new Schedule($pdo);
    }

    public function process($token) {
        try {
            $user = $this->userModel->findByToken($token);

            if (!$user) {
                return ['status' => 'error', 'message' => 'QR Code tidak valid / User tidak ditemukan'];
            }

            // Check for active schedule
            $activeSchedule = $this->scheduleModel->getActiveSchedule();
            
            if (!$activeSchedule) {
                 return ['status' => 'error', 'message' => 'Tidak ada jadwal ujian aktif saat ini.'];
            }

            // Check if already present for THIS schedule (or today if logic dictates)
            // Ideally we check per schedule
            $existing = $this->attendanceModel->checkSchedule($user['id'], $activeSchedule['id']);

            if ($existing) {
                return [
                    'status' => 'warning',
                    'message' => 'User sudah absen pada sesi ini (' . $activeSchedule['session_name'] . ')',
                    'detail' => $user
                ];
            }

            $status = (date('H:i') > $activeSchedule['start_time']) ? 'Hadir' : 'Hadir'; // Simplified logic, can add 'Telat' logic based on strict start time
            // If they scan way past start time? Let's keep it simple 'Hadir' for now or 'Telat'
            if (date('H:i:s') > date('H:i:s', strtotime($activeSchedule['start_time'] . ' + 15 minutes'))) {
                $status = 'Telat';
            }

            $this->attendanceModel->create($user['id'], $status, $activeSchedule['id']);

            return [
                'status' => 'success',
                'message' => 'Absensi berhasil mencatat kehadiran pada ' . $activeSchedule['session_name'],
                'detail' => $user
            ];

        } catch (\Exception $e) {
            error_log($e->getMessage());
            return ['status' => 'error', 'message' => 'Database error'];
        }
    }
}
