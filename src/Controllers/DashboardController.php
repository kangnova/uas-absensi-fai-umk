<?php
namespace App\Controllers;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

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
        // Handle Template Downloads
        if (isset($_GET['action'])) {
            if ($_GET['action'] === 'template_user') {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="template_users.csv"');
                $output = fopen('php://output', 'w');
                fputcsv($output, ['Nama Lengkap', 'NIP/NIDN', 'Jabatan (Panitia/Pengawas)', 'Prodi (PAI/PIAUD)']);
                fputcsv($output, ['Contoh Nama', '123456789', 'Pengawas', 'PAI']);
                fputcsv($output, ['Contoh Panitia', '987654321', 'Panitia', 'PAI']);
                fclose($output);
                exit;
            }
            if ($_GET['action'] === 'template_schedule') {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="template_jadwal.csv"');
                $output = fopen('php://output', 'w');
                fputcsv($output, ['Tanggal (YYYY-MM-DD)', 'Sesi', 'Prodi', 'Mata Kuliah', 'Mulai (HH:MM)', 'Selesai (HH:MM)', 'Pengawas (Pisahkan koma)']);
                fputcsv($output, ['2025-01-20', 'Sesi 1', 'PAI', 'Filsafat', '08:00', '10:00', 'Budi Santoso, Siti Aminah']);
                fclose($output);
                exit;
            }
            // Handle Report View
            if ($_GET['action'] === 'report' && isset($_GET['id'])) {
                $user = $this->userModel->getById($_GET['id']);
                if (!$user) die("User not found");

                $schedules = $this->scheduleModel->getAll();
                $all_attendance = $this->attendanceModel->getAll(); // Need all to filter
                
                // Calculate Stats
                $this->calculateUserStats($user, $schedules, $all_attendance);

                // Prepare Detailed Report Data
                $report_data = [];
                // Sort schedules by date
                
                // For Panitia: Show ALL schedules
                // For Pengawas: Show ASSIGNED schedules
                $is_panitia = strpos($user['jabatan'], 'Panitia') !== false;
                
                foreach ($schedules as $s) {
                    $is_assigned = (isset($s['pengawas']) && strpos($s['pengawas'], $user['nama']) !== false);
                    
                    // Check attendance first
                    $att_info = null;
                    foreach ($all_attendance as $a) {
                        if ($a['user_id'] == $user['id'] && $a['schedule_id'] == $s['id']) {
                            $att_info = $a;
                            break;
                        }
                    }

                    if ($is_panitia || $is_assigned || $att_info) {
                        
                        $report_data[] = [
                            'date' => $s['date'],
                            'session' => $s['session_name'],
                            'mk' => $s['mata_kuliah'],
                            'role' => $is_assigned ? 'Pengawas/Bertugas' : ($att_info ? 'Pengganti/Hadir' : 'Panitia'),
                            'status' => $att_info ? 'Hadir' : 'Tidak Hadir',
                            'time_in' => $att_info ? date('H:i:s', strtotime($att_info['timestamp_in'])) : '-'
                        ];
                    }
                }

                require __DIR__ . '/../../public/views/report_view.php';
                exit;
            }

            // Handle Report All
            if ($_GET['action'] === 'report_all') {
                $users = $this->userModel->getAll();
                $schedules = $this->scheduleModel->getAll();
                $all_attendance = $this->attendanceModel->getAll();

                $all_reports = [];

                foreach ($users as $user) {
                    // Calculate Stats
                    $this->calculateUserStats($user, $schedules, $all_attendance);

                    // Prepare Detailed Report Data
                    $report_data = [];
                    $is_panitia = strpos($user['jabatan'], 'Panitia') !== false;

                    foreach ($schedules as $s) {
                        $is_assigned = (isset($s['pengawas']) && strpos($s['pengawas'], $user['nama']) !== false);

                        // Check attendance first
                        $att_info = null;
                        foreach ($all_attendance as $a) {
                            if ($a['user_id'] == $user['id'] && $a['schedule_id'] == $s['id']) {
                                $att_info = $a;
                                break;
                            }
                        }

                        // Show if: Panitia OR Assigned OR Attended (Substitute)
                        if ($is_panitia || $is_assigned || $att_info) {
                            $report_data[] = [
                                'date' => $s['date'],
                                'session' => $s['session_name'],
                                'mk' => $s['mata_kuliah'],
                                'role' => $is_assigned ? 'Pengawas/Bertugas' : ($att_info ? 'Pengganti/Hadir' : 'Panitia'),
                                'status' => $att_info ? 'Hadir' : 'Tidak Hadir',
                                'time_in' => $att_info ? date('H:i:s', strtotime($att_info['timestamp_in'])) : '-'
                            ];
                        }
                    }
                    
                    $all_reports[] = [
                        'user' => $user,
                        'data' => $report_data
                    ];
                }

                require __DIR__ . '/../../public/views/report_all_view.php';
                exit;
            }
        }

        // Handle Import Users
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'import_users') {
            if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
                $file = fopen($_FILES['file']['tmp_name'], 'r');
                
                // Detect Delimiter
                $firstLine = fgets($file);
                $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
                rewind($file);

                fgetcsv($file, 0, $delimiter); // Skip header
                $count = 0;
                while (($row = fgetcsv($file, 0, $delimiter)) !== false) {
                    if (count($row) < 4) continue;
                    $nama = $row[0];
                    $nip = $row[1];
                    $jabatan = $row[2]; // Can be "Panitia" or "Panitia, Pengawas"
                    $prodi = $row[3];

                    // Check if NIP exists
                    $existing = $this->userModel->findByNip($nip);
                    if ($existing) {
                        // Merge Jabatan
                        $current_roles = explode(',', $existing['jabatan']);
                        $new_roles = explode(',', $jabatan);
                        $merged_roles = array_unique(array_merge($current_roles, array_map('trim', $new_roles)));
                        
                        // Merge Prodi
                        $current_prodis = isset($existing['prodi']) ? explode(',', $existing['prodi']) : [];
                        $new_prodis = explode(',', $prodi);
                        $merged_prodis = array_unique(array_merge($current_prodis, array_map('trim', $new_prodis)));

                        // Update
                        $this->userModel->update($existing['id'], [
                            'nama' => $nama, // Update name just in case
                            'jabatan' => $merged_roles, // Array
                            'prodi' => $merged_prodis // Array
                        ]);
                    } else {
                        // Create
                        $this->userModel->create([
                            'nama' => $nama,
                            'nip' => $nip,
                            'jabatan' => $jabatan,
                            'prodi' => $prodi,
                            'token' => bin2hex(random_bytes(16))
                        ]);
                    }
                    $count++;
                }
                fclose($file);
                $success_msg = "Berhasil import $count data users.";
            } else {
                $error_msg = "Gagal upload file.";
            }
        }

        // Handle Import Schedules
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'import_schedules') {
            if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
                $file = fopen($_FILES['file']['tmp_name'], 'r');
                
                // Detect Delimiter
                $firstLine = fgets($file);
                $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
                rewind($file);

                fgetcsv($file, 0, $delimiter); // Skip header
                $count = 0;
                while (($row = fgetcsv($file, 0, $delimiter)) !== false) {
                    if (count($row) < 7) continue;
                    $this->scheduleModel->create([
                        'date' => $row[0],
                        'session' => $row[1],
                        'prodi' => $row[2],
                        'mata_kuliah' => $row[3],
                        'start' => $row[4],
                        'end' => $row[5],
                        'pengawas' => $row[6] // String from CSV
                    ]);
                    $count++;
                }
                fclose($file);
                $success_msg = "Berhasil import $count jadwal.";
            } else {
                $error_msg = "Gagal upload file.";
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_user') {
            try {
                // Checkboxes for jabatan
                $jabatan = isset($_POST['jabatan']) ? $_POST['jabatan'] : [];
                if (empty($jabatan)) {
                    throw new \Exception("Minimal pilih satu jabatan.");
                }

                $nip = $_POST['nip'];
                // Check duplicates Logic for Manual Add
                $existing = $this->userModel->findByNip($nip);
                
                if ($existing) {
                    // Update existing
                    $current_roles = explode(',', $existing['jabatan']);
                    $merged_roles = array_unique(array_merge($current_roles, $jabatan));
                    
                    // Merge Prodi explicitly for manual add if user selects different prodi
                    // Note: Manual add usually comes with a single prodi selection, but we can treat it as adding permission
                    $new_prodi = $_POST['prodi']; // Assuming string based on UI
                    $current_prodis = isset($existing['prodi']) ? explode(',', $existing['prodi']) : [];
                    $merged_prodis = array_unique(array_merge($current_prodis, (array)$new_prodi));

                    $this->userModel->update($existing['id'], [
                        'nama' => $_POST['nama'],
                        'jabatan' => $merged_roles,
                        'prodi' => $merged_prodis
                    ]);
                    $success_msg = "User dengan NIP $nip sudah ada. Data diperbarui (Jabatan & Prodi digabung).";
                } else {
                    $this->userModel->create([
                        'nama' => $_POST['nama'],
                        'nip' => $_POST['nip'],
                        'jabatan' => $jabatan,
                        'prodi' => $_POST['prodi'],
                        'token' => bin2hex(random_bytes(16))
                    ]);
                    $success_msg = "User berhasil ditambahkan.";
                }
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
        
        // Filter users to find potential supervisors (Pengawas) OR Substitutes (Panitia)
        $supervisors = array_filter($users, fn($u) => strpos($u['jabatan'], 'Pengawas') !== false || strpos($u['jabatan'], 'Panitia') !== false);
        
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
            $this->calculateUserStats($u, $schedules, $all_attendance, $stats);
        }
        unset($u); // Break reference

        require __DIR__ . '/../../public/views/dashboard_view.php';
    }

    private function calculateUserStats(&$u, $schedules, $all_attendance, &$global_stats = null) {
        $u['stats'] = [
            'target' => 0,
            'hadir' => 0,
            'absen' => 0,
            'substitution_count' => 0
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

        // 1.5 Substitution Count
        if (strpos($u['jabatan'], 'Pengawas') === false) {
             foreach ($schedules as $sch) {
                if (isset($sch['pengawas']) && strpos($sch['pengawas'], $u['nama']) !== false) {
                    $u['stats']['substitution_count']++;
                }
            }
        }

        // 2. Calculate Hadir
        $my_attendance = array_filter($all_attendance, fn($a) => $a['user_id'] == $u['id']);
        $u['stats']['hadir'] = count($my_attendance);

        // 3. Calculate Absen
        $u['stats']['absen'] = max(0, $u['stats']['target'] - $u['stats']['hadir']);

        // Update Global Stats if provided
        if ($global_stats !== null) {
             if (strpos($u['jabatan'], 'Panitia') !== false) {
                $global_stats['panitia_total_target'] += $u['stats']['target'];
                $global_stats['panitia_hadir'] += $u['stats']['hadir'];
            } 
            if (strpos($u['jabatan'], 'Pengawas') !== false) {
                $global_stats['pengawas_total_target'] += $u['stats']['target'];
                $global_stats['pengawas_hadir'] += $u['stats']['hadir'];
            }
        }
    }
}
