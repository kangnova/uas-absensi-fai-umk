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

    <div class="container-fluid px-4">
        <?php if ($success_msg): ?><div class="alert alert-success"><?= $success_msg ?></div><?php endif; ?>
        <?php if ($error_msg): ?><div class="alert alert-danger"><?= $error_msg ?></div><?php endif; ?>

        <div class="row">
            <!-- LEFT COLUMN: Management -->
            <div class="col-md-4">
                
                <!-- SCHEDULE MANAGEMENT -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-warning text-dark fw-bold">📅 Manajemen Jadwal Ujian</div>
                    <div class="card-body">
                        <form method="POST" class="mb-3">
                            <input type="hidden" name="action" value="add_schedule">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="small">Tanggal</label>
                                    <input type="date" name="date" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-6">
                                    <label class="small">Sesi</label>
                                    <input type="text" name="session" class="form-control form-control-sm" placeholder="Contoh: Sesi 1" required>
                                </div>
                                <div class="col-6">
                                    <label class="small">Mulai</label>
                                    <input type="time" name="start" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-6">
                                    <label class="small">Selesai</label>
                                    <input type="time" name="end" class="form-control form-control-sm" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-warning w-100 mt-2">Tambah Jadwal</button>
                        </form>

                        <h6 class="small fw-bold mt-3">Daftar Jadwal</h6>
                        <ul class="list-group list-group-flush small">
                            <?php foreach ($schedules as $s): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= date('d M', strtotime($s['date'])) ?></strong> - <?= htmlspecialchars($s['session_name']) ?><br>
                                        <span class="text-muted"><?= date('H:i', strtotime($s['start_time'])) ?> - <?= date('H:i', strtotime($s['end_time'])) ?></span>
                                    </div>
                                    <a href="?delete_schedule=<?= $s['id'] ?>" class="text-danger" onclick="return confirm('Hapus jadwal?')">&times;</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- USER MANAGEMENT -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white fw-bold">👥 Tambah Peserta Data</div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="add_user">
                            <div class="mb-2">
                                <label class="small">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-2">
                                <label class="small">NIP/NIDN</label>
                                <input type="text" name="nip" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-2">
                                <label class="small">Program Studi</label>
                                <select name="prodi" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih Prodi --</option>
                                    <option value="PAI">Pendidikan Agama Islam (PAI)</option>
                                    <option value="PIAUD">Pendidikan Islam Anak Usia Dini (PIAUD)</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="small d-block">Jabatan (Bisa pilih keduanya)</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="jabatan[]" value="Panitia" id="j_panitia">
                                    <label class="form-check-label small" for="j_panitia">Panitia</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="jabatan[]" value="Pengawas" id="j_pengawas">
                                    <label class="form-check-label small" for="j_pengawas">Pengawas</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary w-100">Simpan User</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Reports & Data -->
            <div class="col-md-8">
                
                <!-- TABS FOR PRODI -->
                <ul class="nav nav-tabs mb-3" id="mainTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pai-tab" data-bs-toggle="tab" data-bs-target="#pai" type="button" role="tab">📚 PRODI PAI</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="piaud-tab" data-bs-toggle="tab" data-bs-target="#piaud" type="button" role="tab">🧸 PRODI PIAUD</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="master-tab" data-bs-toggle="tab" data-bs-target="#master" type="button" role="tab">👤 MASTER DATA</button>
                    </li>
                </ul>

                <div class="tab-content" id="mainTabContent">
                    
                    <!-- PAI REPORT -->
                    <div class="tab-pane fade show active" id="pai" role="tabpanel">
                        <?php 
                        $users_pai = array_filter($users, fn($u) => $u['prodi'] === 'PAI');
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary border-bottom pb-2">📋 Laporan Panitia (PAI)</h6>
                                <table class="table table-bordered table-sm small">
                                    <thead class="table-light"><tr><th>Nama</th><th>Status Kehadiran</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($users_pai as $u): if(strpos($u['jabatan'], 'Panitia') !== false): ?>
                                            <?php 
                                            // Simple check if present in recent attendance (this logic assumes single day/session for simplicity in view, ideally filter by active schedule)
                                            $present = false; 
                                            foreach($attendance as $a) { if($a['user_id'] == $u['id']) $present = $a; }
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($u['nama']) ?></td>
                                                <td>
                                                    <?php if($present): ?>
                                                        <span class="badge bg-success">Hadir: <?= date('H:i', strtotime($present['timestamp_in'])) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Belum Hadir</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endif; endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success border-bottom pb-2">📋 Laporan Pengawas (PAI)</h6>
                                <table class="table table-bordered table-sm small">
                                    <thead class="table-light"><tr><th>Nama</th><th>Status Kehadiran</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($users_pai as $u): if(strpos($u['jabatan'], 'Pengawas') !== false): ?>
                                            <?php 
                                            $present = false; 
                                            foreach($attendance as $a) { if($a['user_id'] == $u['id']) $present = $a; }
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($u['nama']) ?></td>
                                                <td>
                                                    <?php if($present): ?>
                                                        <span class="badge bg-success">Hadir: <?= date('H:i', strtotime($present['timestamp_in'])) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Belum Hadir</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endif; endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- PIAUD REPORT -->
                    <div class="tab-pane fade" id="piaud" role="tabpanel">
                         <?php 
                        $users_piaud = array_filter($users, fn($u) => $u['prodi'] === 'PIAUD');
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary border-bottom pb-2">📋 Laporan Panitia (PIAUD)</h6>
                                <table class="table table-bordered table-sm small">
                                    <thead class="table-light"><tr><th>Nama</th><th>Status Kehadiran</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($users_piaud as $u): if(strpos($u['jabatan'], 'Panitia') !== false): ?>
                                            <?php 
                                            $present = false; 
                                            foreach($attendance as $a) { if($a['user_id'] == $u['id']) $present = $a; }
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($u['nama']) ?></td>
                                                <td>
                                                    <?php if($present): ?>
                                                        <span class="badge bg-success">Hadir: <?= date('H:i', strtotime($present['timestamp_in'])) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Belum Hadir</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endif; endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success border-bottom pb-2">📋 Laporan Pengawas (PIAUD)</h6>
                                <table class="table table-bordered table-sm small">
                                    <thead class="table-light"><tr><th>Nama</th><th>Status Kehadiran</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($users_piaud as $u): if(strpos($u['jabatan'], 'Pengawas') !== false): ?>
                                            <?php 
                                            $present = false; 
                                            foreach($attendance as $a) { if($a['user_id'] == $u['id']) $present = $a; }
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($u['nama']) ?></td>
                                                <td>
                                                    <?php if($present): ?>
                                                        <span class="badge bg-success">Hadir: <?= date('H:i', strtotime($present['timestamp_in'])) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Belum Hadir</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endif; endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- MASTER USER DATA -->
                    <div class="tab-pane fade" id="master" role="tabpanel">
                        <div class="card">
                            <div class="card-header">Data Semua Peserta</div>
                            <div class="card-body">
                                <table class="table table-bordered table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Prodi</th>
                                            <th>Jabatan (Role)</th>
                                            <th>QR Code</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $u): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($u['nama']) ?><br><small class="text-muted"><?= htmlspecialchars($u['nip_nidn']) ?></small></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($u['prodi']) ?></span></td>
                                            <td><?= htmlspecialchars($u['jabatan']) ?></td>
                                            <td class="text-center">
                                                <?php if (!empty($u['qr_image'])): ?>
                                                    <img src="../uploads/qrcodes/<?= htmlspecialchars($u['qr_image']) ?>" alt="QR" style="width: 40px; height: 40px;">
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

                </div>

            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
