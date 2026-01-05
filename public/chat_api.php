<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

session_start();
// Security: Optional, but good to have
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

use App\Controllers\ChatController;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);
    $message = $input['message'] ?? '';

    if (empty($message)) {
        echo json_encode(['response' => 'Ketikan sesuatu...']);
        exit;
    }

    try {
        $chatController = new ChatController($pdo);
        $response = $chatController->handleRequest($message);
        echo json_encode(['response' => $response]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['response' => 'Maaf, terjadi kesalahan sistem.']);
    }
}
