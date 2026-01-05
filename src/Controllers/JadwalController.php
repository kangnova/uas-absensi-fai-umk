<?php
namespace App\Controllers;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

use App\Models\Schedule;
use PDO;

class JadwalController {
    private $scheduleModel;
    
    public function __construct(PDO $pdo) {
        $this->scheduleModel = new Schedule($pdo);
    }

    public function index() {
        $prodi = $_GET['prodi'] ?? 'PIAUD';
        $filter_date = $_GET['date'] ?? null;
        
        // Get all schedules
        $all_schedules = $this->scheduleModel->getAll(); // Sorted by date, start_time
        
        // Filter
        $schedules = array_filter($all_schedules, function($s) use ($prodi, $filter_date) {
            $match_prodi = (strcasecmp($s['prodi'], $prodi) === 0);
            $match_date = $filter_date ? ($s['date'] == $filter_date) : true;
            return $match_prodi && $match_date;
        });

        // Grouping for the View
        // Structure: $grouped[date][session_id] => [ 'start' => ..., 'end' => ..., 'courses' => [ 'I' => $row, 'III' => $row ] ]
        $grouped = [];
        
        foreach ($schedules as $s) {
            $date = $s['date'];
            // Session key: Group by session_name only to merge rows
            $sess_key = $s['session_name']; 
            
            if (!isset($grouped[$date])) {
                $grouped[$date] = [];
            }
            if (!isset($grouped[$date][$sess_key])) {
                $grouped[$date][$sess_key] = [
                    'session_name' => $s['session_name'], // e.g. "1"
                    'start' => $s['start_time'],
                    'end' => $s['end_time'],
                    'semesters' => []
                ];
            }
            
            // Normalize Semester key
            // Expected: I, III, V, VII, VII Non Reg
            $sem = strtoupper(trim($s['semester'] ?? '')); 
            // Handle if semester is empty, maybe put in 'Others'
            if (empty($sem)) $sem = 'Unknown';
            
            $grouped[$date][$sess_key]['semesters'][$sem] = $s;
        }

        // Columns to display
        $semester_columns = ['I', 'III', 'V', 'VII', 'VII Non Reg'];

        require __DIR__ . '/../../public/views/jadwal_view.php';
    }
}
