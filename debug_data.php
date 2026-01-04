<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

use App\Models\User;

$userModel = new User($pdo);
$users = $userModel->getAll();

echo "=== CHECKING DUPLICATES ===\n";
$names = [];
foreach ($users as $u) {
    if (!isset($names[$u['nama']])) {
        $names[$u['nama']] = [];
    }
    $names[$u['nama']][] = $u;
}

foreach ($names as $name => $list) {
    if (count($list) > 1) {
        echo "DUPLICATE FOUND: $name\n";
        foreach ($list as $u) {
            echo " - ID: {$u['id']} | NIP: {$u['nip_nidn']} | Prodi: {$u['prodi']} | Jabatan: {$u['jabatan']}\n";
        }
    }
}
