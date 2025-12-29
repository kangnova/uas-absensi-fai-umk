CREATE DATABASE IF NOT EXISTS uas_absensi_fai;
USE uas_absensi_fai;

CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    nip_nidn VARCHAR(50) UNIQUE NOT NULL,
    jabatan ENUM('Panitia', 'Pengawas') NOT NULL,
    qr_token VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    timestamp_in TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    timestamp_out TIMESTAMP NULL,
    status ENUM('Hadir', 'Telat') DEFAULT 'Hadir',
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert sample data
INSERT INTO users (nama, nip_nidn, jabatan, qr_token) VALUES
('Ahmad Fauzi', '196912121994031001', 'Pengawas', 'a1b2c3d4e5f67890'),
('Siti Nurhaliza', '197801011999032001', 'Panitia', 'f1e2d3c4b5a67890');
