<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

use App\Models\User;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\NotoSans;
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
$result = Builder::create()
    ->writer(new PngWriter())
    ->writerOptions([])
    ->data($user['qr_token'])
    ->encoding(new Encoding('UTF-8'))
    ->errorCorrectionLevel(ErrorCorrectionLevel::High)
    ->size(300)
    ->margin(10)
    ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
    ->labelText($user['nama'])
    ->labelFont(new NotoSans(14))
    ->labelAlignment(LabelAlignment::Center)
    ->validateResult(false)
    ->build();

// Save to file
$uploadDir = __DIR__ . '/../uploads/qrcodes/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$fileName = 'qr_user_' . $user['id'] . '.png';
$result->saveToFile($uploadDir . $fileName);

// Output image to browser
header('Content-Type: ' . $result->getMimeType());
echo $result->getString();
?>
