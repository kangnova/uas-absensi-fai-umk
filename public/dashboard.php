<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

use App\Controllers\DashboardController;

$controller = new DashboardController($pdo);
$controller->index();
?>
