<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Absensi UAS FAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%); 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
        }
        .main-card { width: 100%; max-width: 500px; }
    </style>
</head>
<body>
    <div class="container main-card">
        <div class="card shadow-lg border-0">
            <div class="card-body text-center p-5">
                <img src="https://via.placeholder.com/100?text=UMK" alt="Logo" class="mb-4 rounded-circle" style="display:none;"> <!-- Placeholder for logo -->
                <h2 class="mb-4 fw-bold text-primary">Absensi UAS FAI</h2>
                <p class="text-muted mb-5">Universitas Muhammadiyah Klaten</p>
                
                <div class="d-grid gap-3">
                    <a href="scan.php" class="btn btn-lg btn-success shadow-sm">
                        📸 Scan Absensi
                    </a>
                    <a href="manual_attendance.php" class="btn btn-lg btn-secondary shadow-sm">
                        📝 Absen Manual
                    </a>
                    <a href="dashboard.php" class="btn btn-lg btn-outline-primary shadow-sm">
                        📊 Dashboard Admin
                    </a>
                    <a href="logout.php" class="btn btn-lg btn-danger shadow-sm mt-3">
                        🚪 Keluar
                    </a>
                </div>

                <div class="mt-4 text-muted small">
                    &copy; <?= date('Y') ?> FAI UMK
                </div>
            </div>
        </div>
    </div>
</body>
</html>
