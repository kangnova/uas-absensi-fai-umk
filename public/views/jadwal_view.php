<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal UAS - <?= $prodi ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .schedule-table th, .schedule-table td {
            vertical-align: middle;
            border: 1px solid #000;
        }
        .schedule-table thead th {
            text-align: center;
            background-color: #fff;
            font-weight: bold;
        }
        .header-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        @media print {
            .no-print { display: none !important; }
            .schedule-table { width: 100%; border-collapse: collapse; }
            .schedule-table th, .schedule-table td { border: 1px solid #000 !important; }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 no-print">
        <div class="container">
            <a class="navbar-brand" href="#">Absensi UAS FAI</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link active" href="jadwal.php">Jadwal UAS</a>
                <a class="nav-link" href="manual_attendance.php">Absen Manual</a>
                <a class="nav-link" href="scan.php">Scanner</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        
        <!-- Filters & Actions -->
        <div class="card mb-4 no-print">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Program Studi</label>
                        <select name="prodi" class="form-select" onchange="this.form.submit()">
                            <option value="PAI" <?= $prodi == 'PAI' ? 'selected' : '' ?>>PAI</option>
                            <option value="PIAUD" <?= $prodi == 'PIAUD' ? 'selected' : '' ?>>PIAUD</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Filter Tanggal (Opsional)</label>
                        <input type="date" name="date" class="form-control" value="<?= $filter_date ?>" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
                        <button type="button" onclick="downloadHTML()" class="btn btn-success">Export HTML</button>
                        <button type="button" onclick="window.print()" class="btn btn-danger">Export PDF / Cetak</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Schedule Content -->
        <div class="bg-white p-4" id="schedule-content">
            <div class="header-title">
                JADWAL UJIAN AKHIR SEMESTER GASAL TAHUN AKADEMIK <?= date('Y') ?>/<?= date('Y')+1 ?><br>
                PROGRAM STUDI <?= $prodi == 'PIAUD' ? 'PENDIDIKAN ISLAM ANAK USIA DINI (PIAUD)' : 'PENDIDIKAN AGAMA ISLAM (PAI)' ?><br>
                FAKULTAS AGAMA ISLAM<br>
                UNIVERSITAS MUHAMMADIYAH KLATEN
            </div>

            <table class="table table-bordered schedule-table">
                <thead>
                    <tr>
                        <?php if (!$filter_date): // Show Date column if not filtering by specific date (or just always show it) ?>
                        <th rowspan="2" style="width: 15%;">HARI / TGL</th>
                        <?php endif; ?>
                        <th rowspan="2" style="width: 5%;">JAM</th>
                        <th rowspan="2" style="width: 10%;">WAKTU</th>
                        <th colspan="<?= count($semester_columns) ?>">SEMESTER</th>
                    </tr>
                    <tr>
                        <?php foreach($semester_columns as $col): ?>
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($grouped)): ?>
                        <tr><td colspan="<?= 3 + count($semester_columns) ?>" class="text-center p-4">Belum ada jadwal untuk kriteria ini.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($grouped as $date => $sessions): ?>
                        <?php 
                            //$day_name = strftime('%A', strtotime($date));
                            // Fallback day mappings if setlocale doesn't work on Windows
                            $days = ['Sunday' => 'Ahad', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
                            $day_english = date('l', strtotime($date));
                            $day_indo = $days[$day_english] ?? $day_english;
                            
                            $months = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];
                            $month_english = date('F', strtotime($date));
                            $month_indo = $months[$month_english] ?? $month_english;
                            
                            $date_indo = $day_indo . ', ' . date('j', strtotime($date)) . ' ' . $month_indo . ' ' . date('Y', strtotime($date));
                            
                            $rowspan = count($sessions) * 2; 
                            $first_session = true;
                        ?>
                        
                        <?php foreach ($sessions as $key => $sess): ?>
                            <!-- Course Row -->
                            <tr>
                                <?php if ($first_session && !$filter_date): ?>
                                    <td rowspan="<?= $rowspan ?>" class="text-center fw-bold"><?= $date_indo ?></td>
                                <?php endif; ?>
                                
                                <td class="text-center fw-bold"><?= htmlspecialchars($sess['session_name']) ?></td>
                                <td class="text-center"><?= date('H.i', strtotime($sess['start'])) ?> - <?= date('H.i', strtotime($sess['end'])) ?></td>
                                
                                <?php foreach ($semester_columns as $sem): ?>
                                    <td class="text-center">
                                        <?php if (isset($sess['semesters'][$sem])): ?>
                                            <div class="fw-bold"><?= htmlspecialchars($sess['semesters'][$sem]['mata_kuliah']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Supervisor Row -->
                            <tr>
                                <td colspan="2" class="text-center fw-bold text-uppercase" style="background-color: #f8f9fa;">Pengawas</td>
                                <?php foreach ($semester_columns as $sem): ?>
                                    <td class="text-center align-middle">
                                        <?php if (isset($sess['semesters'][$sem])): ?>
                                            <div style="font-size: 0.85em;"><?= htmlspecialchars($sess['semesters'][$sem]['pengawas']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php $first_session = false; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function downloadHTML() {
        var content = document.getElementById('schedule-content').innerHTML;
        var style = document.getElementsByTagName('style')[0].outerHTML;
        var html = '<!DOCTYPE html><html><head><title>Jadwal UAS</title>' + style + '</head><body>' + content + '</body></html>';
        
        var blob = new Blob([html], {type: 'text/html'});
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'Jadwal_UAS_<?= $prodi ?>.html';
        a.click();
    }
    </script>
</body>
</html>
