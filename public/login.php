<?php
session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    // Hardcoded password as requested for simplicity
    if ($password === '123456') {
        $_SESSION['logged_in'] = true;
        header("Location: index.php");
        exit;
    } else {
        $error = "Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Absensi UAS FAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #2c3e50 0%, #4ca1af 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card { width: 100%; max-width: 400px; }
    </style>
</head>
<body>
    <div class="login-card p-3">
        <div class="card shadow-lg border-0">
            <div class="card-body p-5">
                <h3 class="fw-bold text-center mb-4">Login Akses</h3>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Masukkan Password</label>
                        <input type="password" name="password" class="form-control form-control-lg" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Masuk</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center bg-light text-muted small">
                UAS FAI UMK System
            </div>
        </div>
    </div>

    <!-- CHATBOT WIDGET -->
    <?php include __DIR__ . '/views/chatbot_widget.php'; ?>
</body>
</html>
