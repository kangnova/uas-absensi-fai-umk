<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kehadiran - <?= htmlspecialchars($user['nama']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 14px; }
        @media print {
            .no-print { display: none !important; }
            .card { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-light">
    
    <div class="container py-4">
        <!-- Action Buttons -->
        <div class="mb-3 no-print">
            <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak Laporan</button>
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        </div>

        <div class="card shadow-sm">
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
                <h6 class="fw-bold border-bottom pb-2 mb-3">Rekapitulasi Kehadiran</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm">
                        <thead class="table-light text-center align-middle">
                            <?php 
                            $dates = array_keys($matrix_headers);
                            ?>
                            <tr>
                                <th rowspan="2" style="min-width: 200px;">Nama</th>
                                <?php foreach ($matrix_headers as $date => $sessions): ?>
                                    <th colspan="<?= count($sessions) ?>"><?= date('d F Y', strtotime($date)) ?></th>
                                <?php endforeach; ?>
                            </tr>
                            <tr>
                                <?php foreach ($matrix_headers as $sessions): ?>
                                    <?php foreach ($sessions as $session): ?>
                                        <th><?= htmlspecialchars($session) ?></th>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($user['nama'] ?? '') ?></strong>
                                </td>
                                <?php foreach ($matrix_headers as $date => $sessions): ?>
                                    <?php foreach ($sessions as $session): ?>
                                        <td class="text-center">
                                            <?= isset($matrix_row[$date][$session]) ? htmlspecialchars($matrix_row[$date][$session]) : '' ?>
                                        </td>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 text-end">
                    <p>Klaten, <?= date('d F Y') ?></p>
                    <br><br><br>
                    <p class="fw-bold">( Panitia Ujian )</p>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
