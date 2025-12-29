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
                // Handle multiple pengawas
                $pengawas = isset($_POST['pengawas']) ? $_POST['pengawas'] : [];
                
                $this->scheduleModel->create([
                    'date' => $_POST['date'],
                    'prodi' => $_POST['prodi'],
                    'session' => $_POST['session'],
                    'mata_kuliah' => $_POST['mata_kuliah'],
                    'start' => $_POST['start'],
                    'end' => $_POST['end'],
                    'pengawas' => $pengawas
                ]);
                $success_msg = "Jadwal berhasil ditambahkan.";
            } catch (\Exception $e) {
                $error_msg = "Error: " . $e->getMessage();
            }
        }

        // Handle Update Schedule
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_schedule') {
            try {
                $pengawas = isset($_POST['pengawas']) ? $_POST['pengawas'] : [];
                $this->scheduleModel->update($_POST['schedule_id'], [
                    'date' => $_POST['date'],
                    'prodi' => $_POST['prodi'],
                    'session' => $_POST['session'],
                    'mata_kuliah' => $_POST['mata_kuliah'],
                    'start' => $_POST['start'],
                    'end' => $_POST['end'],
                    'pengawas' => $pengawas
                ]);
                $success_msg = "Jadwal berhasil diperbarui (Penggantian Pengawas tersimpan).";
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
        
        // Handle Edit Schedule (Fetch Data)
        $edit_schedule = null;
        if (isset($_GET['edit_schedule'])) {
            $edit_schedule = $this->scheduleModel->getById($_GET['edit_schedule']);
        }

        $users = $this->userModel->getAll();
        
        // Filter users to find potential supervisors (Pengawas)
        $supervisors = array_filter($users, fn($u) => strpos($u['jabatan'], 'Pengawas') !== false);
        
        $attendance = $this->attendanceModel->getRecent();
        $schedules = $this->scheduleModel->getAll();

        // STATISTICS CALCULATION
        $all_attendance = $this->attendanceModel->getAll(); // Fetch ALL for accurate stats
        
        // Detailed User Stats & Global Stats
        $stats = [
            'panitia_total_target' => 0, // Sum of all panitia individual targets (e.g. 10 scheds * 5 panitia = 50)
            'panitia_hadir' => 0,
            'pengawas_total_target' => 0,
            'pengawas_hadir' => 0
        ];

        // Prepare User Detailed Stats
        // We will inject these directly into $users array for easier view handling
        foreach ($users as &$u) {
            $u['stats'] = [
                'target' => 0,
                'hadir' => 0,
                'absen' => 0
            ];

            // 1. Calculate Target
            if (strpos($u['jabatan'], 'Panitia') !== false) {
                // Panitia target = All schedules
                $u['stats']['target'] = count($schedules);
            } elseif (strpos($u['jabatan'], 'Pengawas') !== false) {
                // Pengawas target = Schedules where they are listed
                $my_schedules = 0;
                foreach ($schedules as $sch) {
                    if (isset($sch['pengawas']) && strpos($sch['pengawas'], $u['nama']) !== false) {
                        $my_schedules++;
                    }
                }
                $u['stats']['target'] = $my_schedules;
            }

            // 2. Calculate Hadir (Actual Presence)
            // Filter attendance for this user
            $my_attendance = array_filter($all_attendance, fn($a) => $a['user_id'] == $u['id']);
            $u['stats']['hadir'] = count($my_attendance);

            // 3. Calculate Absen
            $u['stats']['absen'] = max(0, $u['stats']['target'] - $u['stats']['hadir']);

            // Update Global Stats (Accumulate Actual vs Target)
             if (strpos($u['jabatan'], 'Panitia') !== false) {
                $stats['panitia_total_target'] += $u['stats']['target'];
                $stats['panitia_hadir'] += $u['stats']['hadir'];
            } 
            if (strpos($u['jabatan'], 'Pengawas') !== false) {
                $stats['pengawas_total_target'] += $u['stats']['target'];
                $stats['pengawas_hadir'] += $u['stats']['hadir'];
            }
        }
        unset($u); // Break reference

        // Recalculate global strictly based on the sum logic we just did?
        // The previous global stats logic was "Headcount of People Present vs People Registered".
        // The new request implies "Total Man-Schedules Present vs Total Man-Schedules Scheduled".
        // Let's stick to the new comprehensive logic for the cards too, or keep them as "People"?
        // User asked "statistik pada setiap baris nama", suggesting the cards can stay or update. 
        // Let's update cards to reflect "Kehadiran (Sesi)" rather than just "Orang". It's more accurate for a UAS context.
        // So: Panitia Hadir = Sum of all panitia presence counts. Panitia Total = Sum of all panitia targets.

        require __DIR__ . '/../../public/views/dashboard_view.php';
    }
}
