<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kehadiran - Semua User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 14px; }
        @media print {
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
            .card { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-light">
    
    <div class="container py-4">
        <!-- Action Buttons -->
        <div class="mb-3 no-print">
            <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak Semua Laporan</button>
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        </div>

        <?php foreach ($all_reports as $index => $rep): ?>
            <?php 
                $user = $rep['user'];
                $report_data = $rep['data'];
            ?>
            
            <div class="card shadow-sm mb-5 <?= $index > 0 ? 'page-break' : '' ?>">
                <div class="card-body p-5">
                    
                    <!-- HEADER -->
                    <div class="text-center mb-5">
                        <h3 class="fw-bold">LAPORAN KEHADIRAN UJIAN</h3>
                        <h5 class="text-muted">FAKULTAS AGAMA ISLAM - UMK</h5>
                    </div>

                    <!-- USER INFO -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr><td style="width: 120px;">Nama</td><td>: <strong><?= htmlspecialchars($user['nama']) ?></strong></td></tr>
                                <tr><td>NIP/NIDN</td><td>: <?= htmlspecialchars($user['nip_nidn']) ?></td></tr>
                                <tr><td>Jabatan</td><td>: <?= htmlspecialchars($user['jabatan']) ?></td></tr>
                                <tr><td>Prodi</td><td>: <?= htmlspecialchars($user['prodi']) ?></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="card bg-light d-inline-block p-3">
                                <h6 class="mb-1 text-muted">Statistik Kehadiran</h6>
                                <h2 class="mb-0 fw-bold <?= $user['stats']['hadir'] < $user['stats']['target'] ? 'text-danger' : 'text-success' ?>">
                                    <?= $user['stats']['hadir'] ?> / <?= $user['stats']['target'] ?>
                                </h2>
                                <small>Sesi Hadir / Total Wajib</small>
                            </div>
                        </div>
                    </div>

                    <!-- TABLE -->
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Detail Jadwal & Kehadiran</h6>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Tanggal</th>
                                <th>Sesi</th>
                                <th>Mata Kuliah</th>
                                <th>Peran</th>
                                <th>Status</th>
                                <th>Jam Masuk</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($report_data)): ?>
                                <tr><td colspan="6" class="text-center text-muted">Tidak ada jadwal yang ditugaskan.</td></tr>
                            <?php else: ?>
                                <?php foreach ($report_data as $row): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($row['date'])) ?></td>
                                    <td><?= htmlspecialchars($row['session']) ?></td>
                                    <td><?= htmlspecialchars($row['mk']) ?></td>
                                    <td><?= htmlspecialchars($row['role']) ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'Hadir'): ?>
                                            <span class="badge bg-success">Hadir</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Tidak Hadir</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $row['time_in'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="mt-5 text-end">
                        <p>Klaten, <?= date('d F Y') ?></p>
                        <br><br><br>
                        <p class="fw-bold">( Panitia Ujian )</p>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>

    </div>

</body>
</html>
