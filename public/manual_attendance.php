<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

use App\Models\User;
use App\Controllers\ScanController;

$userModel = new User($pdo);
$scanController = new ScanController($pdo);
$users = $userModel->getAll();

$message = null;
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user = $userModel->getById($_POST['user_id']);
    if ($user) {
        // Reuse ScanController logic
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
    } else {
        $message = "User tidak ditemukan.";
        $msg_type = 'danger';
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
                <a href="dashboard.php" class="nav-link text-white">Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">📝 Absensi Manual</h4>
                        <small>Gunakan jika Scanner QR terkendala</small>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($message) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Pilih Nama Peserta / Pengawas</label>
                                <select class="form-select select2" name="user_id" required>
                                    <option value="">-- Cari Nama --</option>
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?= $u['id'] ?>">
                                            <?= htmlspecialchars($u['nama']) ?> - <?= htmlspecialchars($u['jabatan']) ?> (<?= htmlspecialchars($u['prodi']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-lg btn-success">
                                    ✅ Input Kehadiran (Hadir)
                                </button>
                                <a href="scan.php" class="btn btn-outline-secondary">
                                    📸 Kembali ke Scanner
                                </a>
                            </div>
                        </form>

                    </div>
                    <div class="card-footer text-center text-muted small py-3">
                        Pastikan Jadwal Ujian sedang AKTIF saat melakukan input.
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
            $('.select2').select2({
                placeholder: "Ketik nama untuk mencari...",
                allowClear: true,
                theme: "classic",
                width: '100%'
            });
        });
    </script>
</body>
</html>
