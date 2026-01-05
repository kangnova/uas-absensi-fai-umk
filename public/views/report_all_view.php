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

        <div class="card shadow-sm mb-5">
            <div class="card-body p-3">
                <!-- HEADER -->
                <div class="text-center mb-4">
                    <h3 class="fw-bold">REKAPITULASI KEHADIRAN UJIAN</h3>
                    <h5 class="text-muted">FAKULTAS AGAMA ISLAM - UMK</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm">
                        <thead class="table-light text-center align-middle">
                            <!-- Helper to calculate total columns -->
                            <?php 
                            $dates = array_keys($matrix_headers);
                            ?>
                            <tr>
                                <th rowspan="2" style="width: 50px;">No.</th>
                                <th rowspan="2" style="min-width: 200px;">Nama</th>
                                <th colspan="3" class="bg-warning text-dark">Statistik</th>
                                <?php foreach ($matrix_headers as $date => $sessions): ?>
                                    <th colspan="<?= count($sessions) ?>"><?= date('d F Y', strtotime($date)) ?></th>
                                <?php endforeach; ?>
                            </tr>
                            <tr>
                                <th class="bg-light text-center small">Wajib</th>
                                <th class="bg-success text-white text-center small">Hadir</th>
                                <th class="bg-danger text-white text-center small">Absen</th>
                                <?php foreach ($matrix_headers as $sessions): ?>
                                    <?php foreach ($sessions as $session): ?>
                                        <th><?= htmlspecialchars($session) ?></th>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($all_reports)): ?>
                                <tr><td colspan="100%" class="text-center text-muted">Tidak ada data user.</td></tr>
                            <?php else: ?>
                                <?php $no = 1; ?>
                                <?php foreach ($all_reports as $rep): ?>
                                    <?php 
                                        $user = $rep['user'];
                                        $row = $rep['matrix_row'];
                                        $stats = $rep['stats'] ?? ['wajib'=>0, 'hadir'=>0, 'alfa'=>0];
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($user['nama'] ?? '') ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($user['jabatan'] ?? '') ?></small>
                                        </td>
                                        <!-- Stats Columns -->
                                        <td class="text-center fw-bold bg-light"><?= $stats['wajib'] ?></td>
                                        <td class="text-center fw-bold text-success"><?= $stats['hadir'] ?></td>
                                        <td class="text-center fw-bold text-danger"><?= $stats['alfa'] ?></td>

                                        <?php foreach ($matrix_headers as $date => $sessions): ?>
                                            <?php foreach ($sessions as $session): ?>
                                                <td class="text-center">
                                                    <?= isset($row[$date][$session]) ? htmlspecialchars($row[$date][$session]) : '' ?>
                                                </td>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-end no-print">
                    <small class="text-muted">Dicetak pada: <?= date('d F Y H:i:s') ?></small>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
