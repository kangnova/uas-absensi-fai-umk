<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Schedule;
use PDO;

class DashboardController {
    private $userModel;
    private $attendanceModel;
    private $scheduleModel;
    
    public function __construct(PDO $pdo) {
        $this->userModel = new User($pdo);
        $this->attendanceModel = new Attendance($pdo);
        $this->scheduleModel = new Schedule($pdo);
    }

    public function index() {
        $success_msg = null;
        $error_msg = null;

        // Handle Add User
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_user') {
            try {
                // Checkboxes for jabatan
                $jabatan = isset($_POST['jabatan']) ? $_POST['jabatan'] : [];
                if (empty($jabatan)) {
                    throw new \Exception("Minimal pilih satu jabatan.");
                }

                $this->userModel->create([
                    'nama' => $_POST['nama'],
                    'nip' => $_POST['nip'],
                    'jabatan' => $jabatan,
                    'prodi' => $_POST['prodi'],
                    'token' => bin2hex(random_bytes(16))
                ]);
                $success_msg = "User berhasil ditambahkan.";
            } catch (\Exception $e) {
                $error_msg = "Error: " . $e->getMessage();
            }
        }

        // Handle Add Schedule
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_schedule') {
            try {
                $this->scheduleModel->create([
                    'date' => $_POST['date'],
                    'session' => $_POST['session'],
                    'start' => $_POST['start'],
                    'end' => $_POST['end']
                ]);
                $success_msg = "Jadwal berhasil ditambahkan.";
            } catch (\Exception $e) {
                $error_msg = "Error: " . $e->getMessage();
            }
        }

        // Handle Delete User
        if (isset($_GET['delete'])) {
            try {
                $this->userModel->delete($_GET['delete']);
                $success_msg = "User berhasil dihapus.";
            } catch (\Exception $e) {
                $error_msg = "Error: " . $e->getMessage();
            }
        }

        // Handle Delete Schedule
        if (isset($_GET['delete_schedule'])) {
            try {
                $this->scheduleModel->delete($_GET['delete_schedule']);
                $success_msg = "Jadwal berhasil dihapus.";
            } catch (\Exception $e) {
                $error_msg = "Error: " . $e->getMessage();
            }
        }

        $users = $this->userModel->getAll();
        $attendance = $this->attendanceModel->getRecent();
        $schedules = $this->scheduleModel->getAll();

        require __DIR__ . '/../../public/views/dashboard_view.php';
    }
}
