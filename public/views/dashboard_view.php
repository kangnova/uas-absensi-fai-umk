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
                        <?php 
                        $is_edit = isset($edit_schedule); 
                        $form_action = $is_edit ? 'update_schedule' : 'add_schedule';
                        $form_btn = $is_edit ? 'Simpan Perubahan' : 'Tambah Jadwal';
                        $form_cancel = $is_edit ? '<a href="dashboard.php" class="btn btn-sm btn-secondary mt-2">Batal</a>' : '';
                        
                        // Populate if edit
                        $e_date = $is_edit ? $edit_schedule['date'] : '';
                        $e_session = $is_edit ? $edit_schedule['session_name'] : '';
                        $e_prodi = $is_edit ? $edit_schedule['prodi'] : '';
                        $e_mk = $is_edit ? $edit_schedule['mata_kuliah'] : '';
                        $e_start = $is_edit ? $edit_schedule['start_time'] : '';
                        $e_end = $is_edit ? $edit_schedule['end_time'] : '';
                        $e_pengawas = $is_edit ? explode(', ', $edit_schedule['pengawas']) : [];
                        ?>
                        
                        <form method="POST" class="mb-3">
                            <input type="hidden" name="action" value="<?= $form_action ?>">
                            <?php if($is_edit): ?><input type="hidden" name="schedule_id" value="<?= $edit_schedule['id'] ?>"><?php endif; ?>
                            
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="small">Tanggal</label>
                                    <input type="date" name="date" class="form-control form-control-sm" value="<?= $e_date ?>" required>
                                </div>
                                <div class="col-6">
                                    <label class="small">Sesi</label>
                                    <input type="text" name="session" class="form-control form-control-sm" placeholder="Contoh: Sesi 1" value="<?= $e_session ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="small">Program Studi</label>
                                    <select name="prodi" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Prodi --</option>
                                        <option value="PAI" <?= $e_prodi == 'PAI' ? 'selected' : '' ?>>PAI</option>
                                        <option value="PIAUD" <?= $e_prodi == 'PIAUD' ? 'selected' : '' ?>>PIAUD</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="small">Mata Kuliah</label>
                                    <input type="text" name="mata_kuliah" class="form-control form-control-sm" value="<?= $e_mk ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="small">Pengawas (Ganti jika berhalangan)</label>
                                    <select name="pengawas[]" class="form-select form-select-sm" multiple style="height: 100px;">
                                        <?php foreach ($supervisors as $spv): ?>
                                            <option value="<?= htmlspecialchars($spv['nama']) ?>" <?= in_array($spv['nama'], $e_pengawas) ? 'selected' : '' ?>><?= htmlspecialchars($spv['nama']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted" style="font-size: 0.75rem;">*Tahan CTRL untuk pilih lebih dari satu</small>
                                </div>
                                <div class="col-6">
                                    <label class="small">Mulai</label>
                                    <input type="time" name="start" class="form-control form-control-sm" value="<?= $e_start ?>" required>
                                </div>
                                <div class="col-6">
                                    <label class="small">Selesai</label>
                                    <input type="time" name="end" class="form-control form-control-sm" value="<?= $e_end ?>" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-warning w-100 mt-2"><?= $form_btn ?></button>
                            <?= $form_cancel ?>
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
                                    <div>
                                        <a href="?edit_schedule=<?= $s['id'] ?>" class="text-primary me-2" title="Ganti Pengawas/Edit">Edit</a>
                                        <a href="?delete_schedule=<?= $s['id'] ?>" class="text-danger" onclick="return confirm('Hapus jadwal?')">&times;</a>
                                    </div>
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
                        
                        <!-- Statistics Cards -->
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <div class="card bg-light border-primary">
                                    <div class="card-body p-2 text-center">
                                        <h6 class="card-title text-primary mb-0">Statistik Panitia (Tugas)</h6>
                                        <div class="fs-4 fw-bold">
                                            <?= $stats['panitia_hadir'] ?> / <?= $stats['panitia_total_target'] ?>
                                        </div>
                                        <small class="text-muted">Total Kehadiran Sesi / Total Sesi Wajib</small>
                                        <div class="progress mt-1" style="height: 5px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?= ($stats['panitia_total_target'] > 0 ? ($stats['panitia_hadir']/$stats['panitia_total_target']*100) : 0) ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="card bg-light border-success">
                                    <div class="card-body p-2 text-center">
                                        <h6 class="card-title text-success mb-0">Statistik Pengawas (Tugas)</h6>
                                        <div class="fs-4 fw-bold">
                                            <?= $stats['pengawas_hadir'] ?> / <?= $stats['pengawas_total_target'] ?>
                                        </div>
                                        <small class="text-muted">Total Kehadiran Sesi / Total Tugas Jaga</small>
                                        <div class="progress mt-1" style="height: 5px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= ($stats['pengawas_total_target'] > 0 ? ($stats['pengawas_hadir']/$stats['pengawas_total_target']*100) : 0) ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">Data Semua Peserta & Statistik Individu</div>
                            <div class="card-body">
                                <table class="table table-bordered table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Prodi</th>
                                            <th>Jabatan (Role)</th>
                                            <th class="table-info text-center">Target (Wajib)</th>
                                            <th class="table-success text-center">Hadir</th>
                                            <th class="table-danger text-center">Tidak Hadir</th>
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
                                            <td class="text-center fw-bold"><?= $u['stats']['target'] ?? 0 ?></td>
                                            <td class="text-center text-success fw-bold"><?= $u['stats']['hadir'] ?? 0 ?></td>
                                            <td class="text-center text-danger fw-bold"><?= $u['stats']['absen'] ?? 0 ?></td>
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
