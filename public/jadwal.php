<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../vendor/autoload.php';

use App\Controllers\JadwalController;

$controller = new JadwalController($pdo);
$controller->index();
