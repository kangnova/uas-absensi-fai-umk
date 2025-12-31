<?php
namespace App\Models;

use PDO;

class User {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO users (nama, nip_nidn, jabatan, prodi, qr_token) VALUES (:nama, :nip, :jabatan, :prodi, :token)");
        
        // Handle jabatan if array (for checkboxes)
        $jabatan = is_array($data['jabatan']) ? implode(',', $data['jabatan']) : $data['jabatan'];

        return $stmt->execute([
            'nama' => $data['nama'],
            'nip' => $data['nip'],
            'jabatan' => $jabatan,
            'prodi' => $data['prodi'],
            'token' => $data['token']
        ]);
    }

    public function delete($id) {
        $this->pdo->prepare("DELETE FROM attendance WHERE user_id = ?")->execute([$id]);
        return $this->pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function findByToken($token) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE qr_token = :token");
        $stmt->execute(['token' => $token]);
        return $stmt->fetch();
    }

    public function findByNip($nip) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE nip_nidn = :nip");
        $stmt->execute(['nip' => $nip]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE users SET nama = :nama, jabatan = :jabatan, prodi = :prodi WHERE id = :id");
        // Jabatan array handling
        $jabatan = is_array($data['jabatan']) ? implode(',', $data['jabatan']) : $data['jabatan'];
        
        return $stmt->execute([
            'id' => $id,
            'nama' => $data['nama'],
            'jabatan' => $jabatan,
            'prodi' => $data['prodi']
        ]);
    }

    public function updateToken($id, $token) {
        $stmt = $this->pdo->prepare("UPDATE users SET qr_token = :token WHERE id = :id");
        return $stmt->execute(['token' => $token, 'id' => $id]);
    }

    public function updateQrImage($id, $imageName) {
        $stmt = $this->pdo->prepare("UPDATE users SET qr_image = :image WHERE id = :id");
        return $stmt->execute(['image' => $imageName, 'id' => $id]);
    }
}
