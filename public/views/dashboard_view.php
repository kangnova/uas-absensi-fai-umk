<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - UAS Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Absensi UAS FAI</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link" href="scan.php">Scanner</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($success_msg) && $success_msg): ?>
            <div class="alert alert-success"><?= $success_msg ?></div>
        <?php endif; ?>
        <?php if (isset($error_msg) && $error_msg): ?>
            <div class="alert alert-danger"><?= $error_msg ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Form & Users List -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Tambah Peserta (Panitia/Pengawas)</div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="add_user">
                            <div class="mb-3">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>NIP/NIDN</label>
                                <input type="text" name="nip" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Jabatan</label>
                                <select name="jabatan" class="form-select" required>
                                    <option value="Panitia">Panitia</option>
                                    <option value="Pengawas">Pengawas</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Data Peserta</div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>QR Code</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?= htmlspecialchars($u['nama']) ?><br><small class="text-muted"><?= htmlspecialchars($u['nip_nidn']) ?></small></td>
                                    <td><?= htmlspecialchars($u['jabatan']) ?></td>
                                    <td class="text-center">
                                        <?php if (!empty($u['qr_image'])): ?>
                                            <img src="../uploads/qrcodes/<?= htmlspecialchars($u['qr_image']) ?>" alt="QR" style="width: 50px; height: 50px;">
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="generate-qr.php?user_id=<?= $u['id'] ?>" target="_blank" class="btn btn-sm btn-info text-white" title="Generate QR">QR</a>
                                            <a href="detail.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-primary" title="Detail">Det</a>
                                            <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')" title="Hapus">Del</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Monitoring Attendance -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">Log Absensi (Realtime)</div>
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jam Masuk</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($attendance as $a): ?>
                                <tr>
                                    <td><?= htmlspecialchars($a['nama']) ?></td>
                                    <td><?= date('H:i:s', strtotime($a['timestamp_in'])) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $a['status'] == 'Hadir' ? 'success' : 'warning' ?>">
                                            <?= $a['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
