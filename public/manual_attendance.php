<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

use App\Models\User;
use App\Models\Schedule;
use App\Controllers\ScanController;

$userModel = new User($pdo);
$scheduleModel = new Schedule($pdo);
$scanController = new ScanController($pdo);
$users = $userModel->getAll();

$message = null;
$msg_type = '';

// Handle Manual ABSEN CLICK
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    
    $user_id = $_POST['user_id'];
    $schedule_id = !empty($_POST['schedule_id']) ? $_POST['schedule_id'] : null;

    $user = $userModel->getById($user_id);

    if ($user) {
        if ($schedule_id) {
            // Retroactive / Specific Schedule Attendance
            require_once __DIR__ . '/../src/Models/Attendance.php';
            $attendanceModel = new \App\Models\Attendance($pdo);
            
            // Check existing
            $existing = $attendanceModel->checkSchedule($user_id, $schedule_id);
            $sch_info = $scheduleModel->getById($schedule_id);
            $session_name = $sch_info ? $sch_info['session_name'] : 'Sesi Terpilih';

            if ($existing) {
                 $message = "User sudah absen pada jadwal ini (" . $session_name . ").";
                 $msg_type = 'warning';
            } else {
                 if ($attendanceModel->create($user_id, 'Hadir', $schedule_id)) {
                      $message = "Sukses: Kehadiran manual dicatat untuk " . htmlspecialchars($user['nama']) . " pada " . $session_name;
                      $msg_type = 'success';
                 } else {
                      $message = "Gagal mencatat kehadiran.";
                      $msg_type = 'danger';
                 }
            }

        } else {
            // Default: Scan Token Logic (Current Active Schedule)
            $result = $scanController->process($user['qr_token']);
            
            if ($result['status'] === 'success') {
                $message = "Sukses: " . $result['message'];
                $msg_type = 'success';
            } elseif ($result['status'] === 'warning') {
                $message = "Peringatan: " . $result['message'];
                $msg_type = 'warning';
            } else {
                $message = "Error: " . $result['message'];
                $msg_type = 'danger';
            }
        }
    } else {
        $message = "User tidak ditemukan.";
        $msg_type = 'danger';
    }
}

// Fetch all schedules for dropdown
$all_schedules_list = $scheduleModel->getAll();
// Sort by date DESC
usort($all_schedules_list, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// QUICK ABSEN LOGIC
$filter_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$daily_schedules = $scheduleModel->getByDate($filter_date);

// Extract Pengawas from today's schedules
$pengawas_today = [];
foreach ($daily_schedules as $sch) {
    if (!empty($sch['pengawas'])) {
        $names = array_map('trim', explode(',', $sch['pengawas']));
        foreach ($names as $nm) {
            $pengawas_today[] = $nm;
        }
    }
}
$pengawas_today = array_unique($pengawas_today);

// Filter Users: PANITIA or PENGAWAS ON DUTY
$quick_users = [];
foreach ($users as $u) {
    $is_panitia = strpos($u['jabatan'], 'Panitia') !== false;
    $is_on_duty = in_array($u['nama'], $pengawas_today);

    if ($is_panitia || $is_on_duty) {
        // Customize status: Check if already present? (Optional enhancement)
        $quick_users[] = $u;
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Manual - UAS FAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body { 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow">
        <div class="container">
            <a class="navbar-brand" href="#">Absensi UAS FAI</a>
            <div class="d-flex">
                <a href="index.php" class="nav-link text-white me-3">Home</a>
                <a href="dashboard.php" class="nav-link text-white me-3">Dashboard</a>
                <a href="logout.php" class="nav-link text-danger fw-bold">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <?php if ($message): ?>
            <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- COL 1: Single Search -->
            <div class="col-md-5">
                <div class="card shadow border-0 rounded-lg h-100">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h5 class="mb-0">🔍 Cari Nama Manual</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Pilih Jadwal / Sesi (Opsional)</label>
                                <select class="form-select select2" name="schedule_id">
                                    <option value="">-- Otomatis (Sesuai Jam Sekarang) --</option>
                                    <?php foreach ($all_schedules_list as $sl): ?>
                                        <option value="<?= $sl['id'] ?>">
                                            <?= date('d M Y', strtotime($sl['date'])) ?> - <?= htmlspecialchars($sl['session_name']) ?> - <?= htmlspecialchars($sl['mata_kuliah']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted d-block mt-1">Pilih ini jika input <strong>susulan</strong> (lewat jam).</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Cari User Apapun</label>
                                <select class="form-select select2" name="user_id" required>
                                    <option value="">-- Cari Nama --</option>
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?= $u['id'] ?>">
                                            <?= htmlspecialchars($u['nama']) ?> - <?= htmlspecialchars($u['jabatan']) ?> (<?= htmlspecialchars($u['prodi'] ?? '-') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">✅ Absen Hadir</button>
                            </div>
                        </form>
                        <div class="mt-4 text-center">
                              <a href="scan.php" class="btn btn-outline-secondary w-100">📸 Kembali ke Scanner</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COL 2: Quick List -->
            <div class="col-md-7">
                 <div class="card shadow border-0 rounded-lg h-100">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📋 Absen Cepat (Harian)</h5>
                        <form method="GET" class="d-flex" style="max-width: 200px;">
                            <input type="date" name="date" class="form-control form-control-sm" value="<?= $filter_date ?>" onchange="this.form.submit()">
                        </form>
                    </div>
                    <div class="card-body p-0">
                        <div class="alert alert-light m-3 small border">
                            <i class="bi bi-info-circle"></i> Menampilkan <strong>Panitia</strong> & <strong>Pengawas</strong> yang bertugas pada tanggal terpilih.
                        </div>
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-3">Nama</th>
                                        <th>Jabatan</th>
                                        <th class="text-end pe-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($quick_users)): ?>
                                        <tr><td colspan="3" class="text-center py-4 text-muted">Tidak ada jadwal pengawas / petugas hari ini.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($quick_users as $qu): ?>
                                        <tr>
                                            <td class="ps-3 fw-bold"><?= htmlspecialchars($qu['nama']) ?></td>
                                            <td class="small text-muted"><?= htmlspecialchars($qu['jabatan']) ?></td>
                                            <td class="text-end pe-3">
                                                 <form method="POST" class="d-inline">
                                                    <input type="hidden" name="user_id" value="<?= $qu['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-success px-3">Hadir</button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({ placeholder: "Ketik nama...", allowClear: true, theme: "classic", width: '100%' });
        });
    </script>
</body>
</html>
