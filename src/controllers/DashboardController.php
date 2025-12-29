<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Attendance;
use PDO;

class DashboardController {
    private $userModel;
    private $attendanceModel;
    
    public function __construct(PDO $pdo) {
        $this->userModel = new User($pdo);
        $this->attendanceModel = new Attendance($pdo);
    }

    public function index() {
        $success_msg = null;
        $error_msg = null;

        // Handle Add User
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_user') {
            try {
                $this->userModel->create([
                    'nama' => $_POST['nama'],
                    'nip' => $_POST['nip'],
                    'jabatan' => $_POST['jabatan'],
                    'token' => bin2hex(random_bytes(16))
                ]);
                $success_msg = "User berhasil ditambahkan.";
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

        $users = $this->userModel->getAll();
        $attendance = $this->attendanceModel->getRecent();

        require __DIR__ . '/../../public/views/dashboard_view.php';
    }
}
