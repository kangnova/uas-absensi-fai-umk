<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Peserta - <?= htmlspecialchars($user['nama']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow-sm mx-auto" style="max-width: 600px;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Detail Peserta</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <?php if (!empty($user['qr_image'])): ?>
                        <img src="../uploads/qrcodes/<?= htmlspecialchars($user['qr_image']) ?>" alt="QR Code" class="img-fluid border rounded p-2" style="max-width: 200px;">
                        <p class="text-muted mt-2 small"><?= htmlspecialchars($user['qr_image']) ?></p>
                    <?php else: ?>
                        <div class="alert alert-warning d-inline-block">Belum ada QR Code</div>
                    <?php endif; ?>
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th class="bg-light" style="width: 30%;">Nama</th>
                        <td><?= htmlspecialchars($user['nama']) ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">NIP/NIDN</th>
                        <td><?= htmlspecialchars($user['nip_nidn']) ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Jabatan</th>
                        <td><?= htmlspecialchars($user['jabatan']) ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Token</th>
                        <td><code><?= htmlspecialchars($user['qr_token']) ?></code></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Terdaftar</th>
                        <td><?= date('d M Y H:i', strtotime($user['created_at'])) ?></td>
                    </tr>
                </table>

                <?php
                // Calculate Public URL
                $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
                $host = $_SERVER['HTTP_HOST'];
                $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                $qr_url = "$protocol://$host$path/generate-qr.php?user_id=" . $user['id'];
                ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Link Akses QR Code (Publik)</label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="<?= $qr_url ?>" id="qrLink" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyLink()">Salin URL</button>
                    </div>
                    <small class="text-muted">Bagikan link ini kepada <?= htmlspecialchars($user['nama']) ?> agar mereka dapat melihat QR Code mereka.</small>
                </div>

                <script>
                function copyLink() {
                    var copyText = document.getElementById("qrLink");
                    copyText.select();
                    copyText.setSelectionRange(0, 99999); // Mobile
                    navigator.clipboard.writeText(copyText.value).then(function() {
                        alert("Link berhasil disalin!");
                    }, function(err) {
                        alert("Gagal menyalin link: " + err);
                    });
                }
                </script>

                <div class="d-grid gap-2">
                    <a href="generate-qr.php?user_id=<?= $user['id'] ?>" class="btn btn-success" target="_blank">Generate Ulang QR Code</a>
                    <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
