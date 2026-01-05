<?php
namespace App\Controllers;

use PDO;
use App\Models\Schedule;
use App\Models\User;

class ChatController {
    private $pdo;
    private $scheduleModel;
    private $userModel;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->scheduleModel = new Schedule($pdo);
        $this->userModel = new User($pdo);
    }

    public function handleRequest($message) {
        $message = strtolower(trim($message));

        // GREETINGS
        if (preg_match('/(halo|hai|pagi|siang|sore|malam|assalam|hi|help|bantuan|menu)/', $message)) {
            return "Halo! Saya **Asisten Panitia UAS**. Berikut hal yang bisa saya bantu:<br><br>" .
                   "📅 **Informasi Jadwal**<br>" .
                   "- 'Jadwal hari ini'<br>" .
                   "- 'Jadwal tanggal 5 Januari'<br><br>" .
                   "👮 **Informasi Pengawas/Petugas**<br>" .
                   "- 'Siapa bertugas hari ini'<br>" .
                   "- 'Petugas Sesi 1'<br>" .
                   "- 'Siapa jaga besok'<br><br>" .
                   "🔍 **Pencarian Data**<br>" .
                   "- 'Cari Pak Nova' (Cari Dosen/Panitia)<br>" .
                   "- 'Pengawas Matkul Filsafat' (Cari per Mapel)";
        }

        // QUERY: JADWAL HARI INI
        if (strpos($message, 'jadwal hari ini') !== false || strpos($message, 'jadwal sekarang') !== false) {
            $date = date('Y-m-d');
            return $this->getScheduleByDate($date, 'Hari ini');
        }

        // QUERY: JADWAL TANGGAL (Ex: 5 januari)
        if (preg_match('/jadwal.*tanggal\s+(\d{1,2})\s+([a-z]+)/', $message, $matches)) {
            $day = $matches[1];
            $monthName = $matches[2];
            $year = date('Y');
            
            $months = [
                'januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04',
                'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08',
                'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12',
                'jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04', 'jun' => '06', 
                'jul' => '07', 'agu' => '08', 'sep' => '09', 'okt' => '10', 'nov' => '11', 'des' => '12'
            ];

            if (isset($months[$monthName])) {
                $monthNum = $months[$monthName];
                $date = "$year-$monthNum-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                $formatted_date = "$day $monthName $year";
                return $this->getScheduleByDate($date, $formatted_date);
            } else {
                return "Maaf, saya tidak mengenali nama bulan '$monthName'.";
            }
        }

        // QUERY: SIAPA BERTUGAS / PETUGAS
        // Catch varied phrasing: "siapa saja yang bertugas", "petugas sesi 1", "pengawas hari ini"
        if (preg_match('/(siapa|petugas|pengawas)\s+(yang|saja)?\s*(bertugas|jaga)?/', $message)) {
            
            // Determine DATE
            $date = date('Y-m-d'); // Default today
            $dateLabel = 'Hari Ini';

            if (strpos($message, 'besok') !== false) {
                $date = date('Y-m-d', strtotime('+1 day'));
                $dateLabel = 'Besok';
            } elseif (preg_match('/tanggal\s+(\d{1,2})\s+([a-z]+)/', $message, $matches)) {
                $day = $matches[1]; $monthName = $matches[2]; $year = date('Y');
                $months = ['januari'=>'01','februari'=>'02','maret'=>'03','april'=>'04','mei'=>'05','juni'=>'06','juli'=>'07','agustus'=>'08','september'=>'09','oktober'=>'10','november'=>'11','desember'=>'12','jan'=>'01','feb'=>'02','mar'=>'03','apr'=>'04','jun'=>'06','jul'=>'07','agu'=>'08','sep'=>'09','okt'=>'10','nov'=>'11','des'=>'12'];
                if (isset($months[$monthName])) {
                    $monthNum = $months[$monthName];
                    $date = "$year-$monthNum-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                    $dateLabel = "$day $monthName";
                }
            }

            // Determine SESSION
            $sessionFilter = null;
            if (strpos($message, 'sesi 1') !== false) $sessionFilter = 'Sesi 1';
            if (strpos($message, 'sesi 2') !== false) $sessionFilter = 'Sesi 2';
            if (strpos($message, 'sesi 3') !== false) $sessionFilter = 'Sesi 3';
            if (strpos($message, 'sesi 4') !== false) $sessionFilter = 'Sesi 4';

            // Only trigger if it contains explicit "hari ini", "besok", "tanggal", "sesi", or just "siapa bertugas"
            // To avoid clashing with "Cari siapa", check constraints.
            // But "Cari" is handled below. "Siapa" is ambiguous.
            // Let's refine strictness: if "bertugas" or "jaga" is present OR "sesi" is present OR "hari ini" is present.
            if (strpos($message, 'bertugas') !== false || strpos($message, 'jaga') !== false || strpos($message, 'hari ini') !== false || strpos($message, 'sesi') !== false) {
                 return $this->getPengawasOnDuty($date, $dateLabel, $sessionFilter);
            }
        }

        // QUERY: CARI DOSEN / PETUGAS
        if (preg_match('/(cari|siapa|info)\s+(bapak|ibu|pak|bu|user)?\s*(.+)/', $message, $matches)) {
            $keyword = trim($matches[3]);
            $keyword = str_replace('?', '', $keyword);
            
            if (strlen($keyword) < 3) return "Kata kunci nama terlalu pendek.";
            // Filter common stop words if user typed "siapa yang bertugas" and it fell through
            if ($keyword == 'yang bertugas' || $keyword == 'saja yang bertugas') {
                 return $this->getPengawasOnDuty(date('Y-m-d'), 'Hari Ini');
            }

            return $this->searchUser($keyword);
        }

        // QUERY: PENGAWAS MATKUL
        if (preg_match('/pengawas.*(mk|matkul|mata kuliah)?\s+(.+)/', $message, $matches)) {
            $keyword = trim($matches[2]);
            return $this->searchSchedulePengawas($keyword);
        }

        return "Maaf, saya belum mengerti. Coba tanya seputar 'Jadwal', 'Siapa bertugas', atau 'Cari Nama'.";
    }

    private function getScheduleByDate($date, $label) {
        $schedules = $this->scheduleModel->getByDate($date);
        if (empty($schedules)) {
            return "Tidak ada jadwal ujian untuk **$label** ($date).";
        }

        $response = "📅 **Jadwal $label ($date):**<br>";
        foreach ($schedules as $s) {
            $pengawas_str = !empty($s['pengawas']) ? "👮 " . $s['pengawas'] : "⚠️ Belum ada pengawas";
            $response .= "- **" . $s['session_name'] . "** (" . substr($s['start_time'],0,5) . "):<br>";
            $response .= "   📚 " . $s['mata_kuliah'] . " (Smstr " . ($s['semester'] ?? '-') . " - " . $s['prodi'] . ")<br>";
            $response .= "   $pengawas_str<br><br>";
        }
        return $response;
    }

    private function searchUser($keyword) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE nama LIKE :k OR jabatan LIKE :k LIMIT 5");
        $stmt->execute(['k' => "%$keyword%"]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($users)) {
            return "Tidak ditemukan user dengan nama '$keyword'.";
        }

        $response = "🔍 **Hasil Pencarian '$keyword':**<br>";
        foreach ($users as $u) {
            $response .= "👤 **" . htmlspecialchars($u['nama']) . "**<br>";
            $response .= "   Jabatan: " . $u['jabatan'] . "<br>";
            if (!empty($u['prodi'])) $response .= "   Prodi: " . $u['prodi'] . "<br>";
            $response .= "<br>";
        }
        return $response;
    }

    private function searchSchedulePengawas($mk_keyword) {
        $stmt = $this->pdo->prepare("SELECT * FROM exam_schedules WHERE mata_kuliah LIKE :k OR pengawas LIKE :k ORDER BY date DESC LIMIT 5");
        $stmt->execute(['k' => "%$mk_keyword%"]);
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($schedules)) {
            return "Tidak ditemukan Info Pengawas/Matkul dengan kata kunci '$mk_keyword'.";
        }

        $response = "📋 **Info Jadwal & Pengawas:**<br>";
        foreach ($schedules as $s) {
            $response .= "📚 **" . $s['mata_kuliah'] . "** (" . date('d M', strtotime($s['date'])) . ")<br>";
            $response .= "   Smt: " . ($s['semester'] ?? '-') . " (" . $s['prodi'] . ")<br>";
            $response .= "   Pengawas: " . $s['pengawas'] . "<br><br>";
        }
        return $response;
    }

    private function getPengawasOnDuty($date, $dateLabel, $sessionFilter = null) {
        // Query Schedules
        $schedules = $this->scheduleModel->getByDate($date);

        if (empty($schedules)) {
            return "Tidak ada jadwal ujian untuk **$dateLabel** ($date).";
        }

        $response = "👮 **Petugas/Pengawas $dateLabel ($date):**<br>";
        
        // Group by Session
        $grouped = [];
        foreach ($schedules as $s) {
            $sessName = $s['session_name']; // e.g. "Sesi 1"
            if ($sessionFilter && stripos($sessName, $sessionFilter) === false) continue;
            $grouped[$sessName][] = $s;
        }

        if (empty($grouped)) {
            return "Tidak ada jadwal untuk **$sessionFilter** pada $dateLabel.";
        }

        ksort($grouped);

        foreach ($grouped as $sessName => $items) {
            $response .= "<br>🔹 **$sessName**:<br>";
            
            // Iterate ITEMS explicitly to show MK, Sem, and Specific Pengawas
            foreach ($items as $item) {
                // Formatting: - MK (Sem - Prodi): [Pengawas]
                $mk = $item['mata_kuliah'];
                $sem = $item['semester'] ?? '?';
                $prodi = $item['prodi'];
                $p_names = $item['pengawas'] ?? 'Belum ditentukan';
                
                $response .= "➤ **$mk** (Smstr $sem - $prodi)<br>";
                $response .= "   &nbsp;&nbsp;&nbsp;👮 Pengawas: $p_names<br>"; 
            }
        }

        return $response;
    }
}
