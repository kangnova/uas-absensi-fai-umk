<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

use App\Controllers\ScanController;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['token'])) {
    echo json_encode(['status' => 'error', 'message' => 'Token required']);
    exit;
}

$controller = new ScanController($pdo);
echo json_encode($controller->process($_POST['token']));
?>
