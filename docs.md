# Dokumentasi Sistem Absensi UAS FAI UMK

## 📋 Overview
Sistem informasi manajemen absensi panitia dan pengawas UAS Fakultas Agama Islam, Universitas Muhammadiyah Klaten. Dibangun dengan PHP Native + MySQL.

## 🛠 Tech Stack
- **Backend**: PHP 8+ Native, MySQL 8+
- **QR Code**: endroid/qr-code (Composer)
- **Scanner**: html5-qrcode (Browser camera)
- **Export**: PHPExcel/PHPFPDF (opsional)

## 📁 Struktur Folder
uas-absensi-fai-umk/
├── composer.json
├── config/
│ ├── database.php
│ └── config.php
├── src/
│ ├── models/
│ ├── controllers/
│ └── helpers/
├── public/
│ ├── index.php
│ ├── dashboard.php
│ ├── generate-qr.php
│ ├── scan.php
│ └── process-scan.php
├── uploads/qrcodes/
├── exports/
├── vendor/
├── database.sql
└── docs.md

## 🗄 Database Schema
CREATE DATABASE uas_absensi_fai;
USE uas_absensi_fai;

CREATE TABLE users (
id INT PRIMARY KEY AUTO_INCREMENT,
nama VARCHAR(100) NOT NULL,
nip_nidn VARCHAR(50) UNIQUE NOT NULL,
jabatan ENUM('Panitia', 'Pengawas') NOT NULL,
qr_token VARCHAR(255) UNIQUE NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE attendance (
id INT PRIMARY KEY AUTO_INCREMENT,
user_id INT NOT NULL,
timestamp_in TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
timestamp_out TIMESTAMP NULL,
status ENUM('Hadir', 'Telat') DEFAULT 'Hadir',
FOREIGN KEY (user_id) REFERENCES users(id)
);

## 🚀 Instalasi (5 Langkah)
1. **Clone/Download** project ke folder web server
2. **Install Dependencies**:
cd uas-absensi-fai-umk
composer require endroid/qr-code

3. **Setup Database**: Import `database.sql` ke phpMyAdmin
4. **Config Database** (`config/database.php`):
<?php $host = 'localhost'; $dbname = 'uas_absensi_fai'; $username = 'root'; $password = ''; $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password); ?>
5. **Permissions**:
chmod 755 uploads/qrcodes/
chmod 755 exports/

## 🔗 URL Akses
http://localhost/uas-absensi-fai-umk/public/

Dashboard: /dashboard.php

Generate QR: /generate-qr.php?user_id=1

Scanner: /scan.php

## ✨ Fitur Utama

### 1. Manajemen Data
Tambah/Edit/Hapus panitia & pengawas

NIP/NIDN unik

Jabatan: Panitia/Pengawas

### 2. Generate QR Code Unik
GET /generate-qr.php?user_id=1

Token: bin2hex(random_bytes(16))

Format: PNG 300x300px

Save: uploads/qrcodes/qr_user_1.png

### 3. Scanner Real-time
Browser camera (mobile/desktop)

html5-qrcode library

Validasi token server-side

Cek duplikat harian

### 4. Dashboard Admin
Real-time monitoring

Statistik kehadiran

Export Excel/PDF

## 📱 Penggunaan

### Generate QR User
Tambah user di admin panel

Klik "Generate QR"

Download/print QR code

### Proses Absensi
Admin buka scan.php

Klik "Mulai Scan"

Panitia scan QR → Auto absen + timestamp

Notifikasi sukses/gagal

## 🔒 Security Features
- Token unik 32 char per user
- Prepared statements (SQL Injection)
- Validasi duplikat absensi harian
- .htaccess protection
- HTTPS recommended

## 🐛 Troubleshooting

| Masalah | Solusi |
|---------|--------|
| QR tidak generate | `composer install` & check `uploads/qrcodes/` permission |
| Scanner gagal | HTTPS atau localhost, allow camera permission |
| Token invalid | Regenerate QR untuk user |
| Duplikat absensi | Sudah fitur bawaan (cek DATE(timestamp_in)) |

## 📊 Contoh Data
-- Insert sample data
INSERT INTO users (nama, nip_nidn, jabatan, qr_token) VALUES
('Ahmad Fauzi', '196912121994031001', 'Pengawas', 'a1b2c3d4e5f67890'),
('Siti Nurhaliza', '197801011999032001', 'Panitia', 'f1e2d3c4b5a67890');

## 🔄 Update & Maintenance
- Backup database mingguan
- Update composer dependencies
- Monitor disk space (uploads/)
- Test scanner di multiple device

## 📞 Kontak
**Fakultas Agama Islam UMKlaten**  
**Developed for UAS 2025 Period**

---
*Last Updated: Desember 2025*
