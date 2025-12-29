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
                                <div class="col-12">
                                    <label class="small">Program Studi</label>
                                    <select name="prodi" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Prodi --</option>
                                        <option value="PAI">PAI</option>
                                        <option value="PIAUD">PIAUD</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="small">Mata Kuliah</label>
                                    <input type="text" name="mata_kuliah" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-12">
                                    <label class="small">Pengawas (Bisa pilih data banyak)</label>
                                    <select name="pengawas[]" class="form-select form-select-sm" multiple style="height: 100px;">
                                        <?php foreach ($supervisors as $spv): ?>
                                            <option value="<?= htmlspecialchars($spv['nama']) ?>"><?= htmlspecialchars($spv['nama']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted" style="font-size: 0.75rem;">*Tahan CTRL untuk pilih lebih dari satu</small>
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
                                        <strong><?= date('d M', strtotime($s['date'])) ?></strong> - <span class="badge bg-info text-dark"><?= htmlspecialchars($s['prodi'] ?? '-') ?></span> <?= htmlspecialchars($s['session_name']) ?><br>
                                        <span class="text-muted"><?= date('H:i', strtotime($s['start_time'])) ?> - <?= date('H:i', strtotime($s['end_time'])) ?></span><br>
                                        <em>MK: <?= htmlspecialchars($s['mata_kuliah'] ?? '-') ?></em><br>
                                        <small>Pengawas: <?= htmlspecialchars($s['pengawas'] ?? '-') ?></small>
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
                
                <!-- TABS -->
                <ul class="nav nav-tabs mb-3" id="mainTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="panitia-tab" data-bs-toggle="tab" data-bs-target="#panitia" type="button" role="tab">👔 LAPORAN PANITIA</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pai-tab" data-bs-toggle="tab" data-bs-target="#pai" type="button" role="tab">📚 PRODI PAI</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="piaud-tab" data-bs-toggle="tab" data-bs-target="#piaud" type="button" role="tab">🧸 PRODI PIAUD</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="master-tab" data-bs-toggle="tab" data-bs-target="#master" type="button" role="tab">👤 MASTER DATA</button>
                    </li>
                </ul>

                <div class="tab-content" id="mainTabContent">
                    
                    <?php
                    // Helper to sort by presence
                    function sortByPresence($users, $attendance) {
                        $present = [];
                        $absent = [];
                        foreach ($users as $u) {
                            $is_present = false;
                            foreach ($attendance as $a) {
                                if ($a['user_id'] == $u['id']) {
                                    $u['attendance_info'] = $a; // Attach attendance info
                                    $is_present = true;
                                    break;
                                }
                            }
                            if ($is_present) {
                                $present[] = $u;
                            } else {
                                $u['attendance_info'] = null;
                                $absent[] = $u;
                            }
                        }
                        // Sort present by time (desc) if needed, currently just listing
                        return array_merge($present, $absent);
                    }

                    // Prepare Data
                    $all_panitia = array_filter($users, fn($u) => strpos($u['jabatan'], 'Panitia') !== false);
                    $pengawas_pai = array_filter($users, fn($u) => strpos($u['jabatan'], 'Pengawas') !== false && $u['prodi'] === 'PAI');
                    $pengawas_piaud = array_filter($users, fn($u) => strpos($u['jabatan'], 'Pengawas') !== false && $u['prodi'] === 'PIAUD');

                    $sorted_panitia = sortByPresence($all_panitia, $attendance);
                    $sorted_pai = sortByPresence($pengawas_pai, $attendance);
                    $sorted_piaud = sortByPresence($pengawas_piaud, $attendance);
                    ?>

                    <!-- PANITIA REPORT (UNIFIED) -->
                    <div class="tab-pane fade show active" id="panitia" role="tabpanel">
                        <h6 class="text-dark border-bottom pb-2">📋 Laporan Kehadiran Panitia (Gabungan)</h6>
                        <table class="table table-bordered table-sm small table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Status Kehadiran</th>
                                    <th>Sesi Jadwal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sorted_panitia as $u): ?>
                                    <tr class="<?= !empty($u['attendance_info']) ? 'table-success' : '' ?>">
                                        <td><?= htmlspecialchars($u['nama'] ?? '') ?></td>
                                        <td>
                                            <?php if (!empty($u['attendance_info'])): ?>
                                                <span class="badge bg-success">Hadir: <?= date('H:i', strtotime($u['attendance_info']['timestamp_in'])) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Belum Hadir</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($u['attendance_info']['session_name'])): ?>
                                                <?= htmlspecialchars($u['attendance_info']['session_name']) ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- PRODI PAI REPORT -->
                    <div class="tab-pane fade" id="pai" role="tabpanel">
                        <h6 class="text-primary border-bottom pb-2">📋 Laporan Pengawas (PAI)</h6>
                        <table class="table table-bordered table-sm small table-hover">
                            <thead class="table-light"><tr><th>Nama</th><th>Status Kehadiran</th><th>Sesi Jadwal</th></tr></thead>
                            <tbody>
                                <?php foreach ($sorted_pai as $u): ?>
                                    <tr class="<?= !empty($u['attendance_info']) ? 'table-success' : '' ?>">
                                        <td><?= htmlspecialchars($u['nama'] ?? '') ?></td>
                                        <td>
                                            <?php if (!empty($u['attendance_info'])): ?>
                                                <span class="badge bg-success">Hadir: <?= date('H:i', strtotime($u['attendance_info']['timestamp_in'])) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Belum Hadir</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $u['attendance_info']['session_name'] ?? '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- PRODI PIAUD REPORT -->
                    <div class="tab-pane fade" id="piaud" role="tabpanel">
                        <h6 class="text-success border-bottom pb-2">📋 Laporan Pengawas (PIAUD)</h6>
                        <table class="table table-bordered table-sm small table-hover">
                            <thead class="table-light"><tr><th>Nama</th><th>Status Kehadiran</th><th>Sesi Jadwal</th></tr></thead>
                            <tbody>
                                <?php foreach ($sorted_piaud as $u): ?>
                                    <tr class="<?= !empty($u['attendance_info']) ? 'table-success' : '' ?>">
                                        <td><?= htmlspecialchars($u['nama'] ?? '') ?></td>
                                        <td>
                                            <?php if (!empty($u['attendance_info'])): ?>
                                                <span class="badge bg-success">Hadir: <?= date('H:i', strtotime($u['attendance_info']['timestamp_in'])) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Belum Hadir</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $u['attendance_info']['session_name'] ?? '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
                                            <td><?= htmlspecialchars($u['nama'] ?? '') ?><br><small class="text-muted"><?= htmlspecialchars($u['nip_nidn'] ?? '') ?></small></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($u['prodi'] ?? '-') ?></span></td>
                                            <td><?= htmlspecialchars($u['jabatan'] ?? '') ?></td>
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
