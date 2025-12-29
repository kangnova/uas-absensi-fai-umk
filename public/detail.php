<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

use App\Models\User;

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$userModel = new User($pdo);
$user = $userModel->getById($_GET['id']);

if (!$user) {
    die("User not found.");
}

require __DIR__ . '/views/detail_view.php';
?>
