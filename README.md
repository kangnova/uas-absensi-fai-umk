# 🎓 Sistem Absensi UAS FAI UMK (Smart System)

Sistem Informasi Manajemen Absensi Cerdas untuk Panitia dan Pengawas UAS Fakultas Agama Islam, Universitas Muhammadiyah Klaten. 
Dibangun dengan arsitektur **Modern PHP MVC**, sistem ini tidak hanya mencatat kehadiran tetapi juga berfungsi sebagai **Asisten Pribadi Otomatis** bagi panitia.

![Dashboard Preview](https://via.placeholder.com/800x400?text=Sistem+Absensi+UAS+FAI)

## 🚀 Fitur Unggulan & Kecanggihan

### 🤖 1. Smart Chatbot Assistant (Asisten Panitia)
Tidak perlu lagi bingung mencari info di tumpukan kertas jadwal. Cukup tanya chatbot!
- **Natural Language Query**: Paham bahasa manusia sehari-hari.
- **Real-time Database**: Mencari data langsung dari database jadwal & user.
- **Kapabilitas**:
  - *"Siapa bertugas hari ini?"* -> Menampilkan list lengkap Pengawas, Mata Kuliah, Semester, dan Prodi per Sesi.
  - *"Jadwal tanggal 7 Januari"* -> Menampilkan agenda detail.
  - *"Cari Pak Nova"* -> Menemukan profil & jabatan user dalam hitungan detik.

### 📊 2. Laporan & Statistik Cerdas
- **Matrix Attendance Report**: Laporan berbentuk tabel matriks (User x Tanggal/Sesi) yang intuitif.
- **Smart Stats**: Perhitungan otomatis:
  - **Wajib**: Jumlah penugasan jadwal + kewajiban panitia.
  - **Hadir**: Kehadiran aktual (Scan/Manual).
  - **Absen**: Deteksi otomatis ketidakhadiran (Wajib - Hadir).
- **Auto-Session Detection**: Sistem otomatis mengenali sesi (Sesi 1/2) berdasarkan waktu scan real-time.

### 🔁 3. Manajemen Kehadiran Fleksibel
- **QR Code Scanner**: Absensi super cepat menggunakan kamera HP/Laptop.
- **Retroactive Attendance**: Input absen susulan untuk jadwal masa lalu (Fitur Admin).
- **Handling Substitusi**: Mendukung pencatatan kehadiran untuk pengawas pengganti.

### 🛡️ 4. Keamanan & Arsitektur
- **MVC Pattern**: Struktur kode rapi (Model-View-Controller) memudahkan pengembangan.
- **Secure Token**: QR Code berbasis token unik yang aman.
- **Role-Based**: Pembedaan akses Menu Panitia vs Pengawas.

## 🛠️ Teknologi yang Digunakan
- **Core**: PHP 8.2 Native (MVC Structure)
- **Database**: MySQL 8.0
- **Frontend**: Bootstrap 5 + jQuery
- **Libraries**: `endroid/qr-code`, `html5-qrcode`

## 📦 Instalasi & Penggunaan
Lihat [docs.md](docs.md) atau [tutorial_manajemen_absen.md](tutorial_manajemen_absen.md) untuk panduan teknis mendalam.

---
**Developed by Tim IT FAI UMK © 2026**
*Smart System for Smart Campus.*
