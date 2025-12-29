<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

use App\Models\User;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

if (!isset($_GET['user_id'])) {
    die("Error: User ID is required.");
}

$userModel = new User($pdo);
$user = $userModel->getById($_GET['user_id']);

if (!$user) {
    die("Error: User not found.");
}

// Generate token if not exists
if (empty($user['qr_token'])) {
    $token = bin2hex(random_bytes(16));
    $userModel->updateToken($user['id'], $token);
    $user['qr_token'] = $token;
}

// Generate QR Code
$builder = new Builder(
    writer: new PngWriter(),
    writerOptions: [],
    data: $user['qr_token'],
    encoding: new Encoding('UTF-8'),
    errorCorrectionLevel: ErrorCorrectionLevel::High,
    size: 300,
    margin: 10,
    roundBlockSizeMode: RoundBlockSizeMode::Margin,
    labelText: $user['nama'],
    labelFont: new OpenSans(14),
    labelAlignment: LabelAlignment::Center,
    validateResult: false
);
$result = $builder->build();

// Save to file
$uploadDir = __DIR__ . '/../uploads/qrcodes/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$sanitized_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $user['nama']);
$fileName = 'qr_' . $sanitized_name . '.png';
$result->saveToFile($uploadDir . $fileName);

// Update user record with QR image filename
$userModel->updateQrImage($user['id'], $fileName);

// Output image to browser
header('Content-Type: ' . $result->getMimeType());
echo $result->getString();
?>
