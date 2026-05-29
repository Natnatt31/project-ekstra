-- ==========================================
-- Globaliti Esport Badung — Database Schema
-- Untuk HeidiSQL / MySQL
-- ==========================================

CREATE DATABASE IF NOT EXISTS `globaliti_esport` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `globaliti_esport`;

-- ========== TABEL ADMIN ==========
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========== TABEL SISWA ==========
DROP TABLE IF EXISTS `siswa`;
CREATE TABLE `siswa` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nis` VARCHAR(20) NOT NULL UNIQUE,
  `nama` VARCHAR(100) NOT NULL,
  `kelas` VARCHAR(20) NOT NULL,
  `jurusan` VARCHAR(50) NOT NULL,
  `no_hp` VARCHAR(20) DEFAULT NULL,
  `game_divisi` ENUM('Mobile Legends','PUBG Mobile','Free Fire','Valorant') NOT NULL,
  `status` ENUM('Aktif','Nonaktif') DEFAULT 'Aktif',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========== TABEL ABSENSI ==========
DROP TABLE IF EXISTS `absensi`;
CREATE TABLE `absensi` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `siswa_id` INT NOT NULL,
  `tanggal` DATE NOT NULL,
  `status` ENUM('Hadir','Izin','Sakit','Alpha') NOT NULL DEFAULT 'Alpha',
  `keterangan` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`siswa_id`) REFERENCES `siswa`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_absen` (`siswa_id`, `tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========== TABEL TURNAMEN ==========
DROP TABLE IF EXISTS `turnamen`;
CREATE TABLE `turnamen` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_turnamen` VARCHAR(150) NOT NULL,
  `game` VARCHAR(50) NOT NULL,
  `tanggal_mulai` DATE NOT NULL,
  `tanggal_selesai` DATE DEFAULT NULL,
  `lokasi` VARCHAR(150) DEFAULT NULL,
  `status` ENUM('Upcoming','Ongoing','Selesai') NOT NULL DEFAULT 'Upcoming',
  `hasil` VARCHAR(100) DEFAULT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- SEED DATA
-- ==========================================

-- Admin default (password: admin123)
INSERT INTO `admin` (`username`, `password`, `nama_lengkap`, `email`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator Globaliti', 'admin@globaliti.id');

-- Data Siswa Ekstrakulikuler
INSERT INTO `siswa` (`nis`, `nama`, `kelas`, `jurusan`, `no_hp`, `game_divisi`, `status`) VALUES
('2024001', 'Kadek Ari Wijaya', 'XI', 'RPL', '081234567001', 'Mobile Legends', 'Aktif'),
('2024002', 'I Made Surya Dharma', 'XI', 'RPL', '081234567002', 'Mobile Legends', 'Aktif'),
('2024003', 'Wayan Dharma Putra', 'XII', 'TKJ', '081234567003', 'Mobile Legends', 'Aktif'),
('2024004', 'Nyoman Agus Pratama', 'X', 'RPL', '081234567004', 'Mobile Legends', 'Aktif'),
('2024005', 'Ketut Bayu Anggara', 'XI', 'MM', '081234567005', 'Mobile Legends', 'Aktif'),
('2024006', 'Gede Putra Mahendra', 'XII', 'TKJ', '081234567006', 'PUBG Mobile', 'Aktif'),
('2024007', 'Komang Dika Saputra', 'XI', 'RPL', '081234567007', 'PUBG Mobile', 'Aktif'),
('2024008', 'Kadek Rian Wijaya', 'X', 'MM', '081234567008', 'PUBG Mobile', 'Aktif'),
('2024009', 'Made Yoga Pratama', 'XI', 'TKJ', '081234567009', 'PUBG Mobile', 'Aktif'),
('2024010', 'Wayan Adi Kusuma', 'XII', 'RPL', '081234567010', 'Free Fire', 'Aktif'),
('2024011', 'Nyoman Pande Arya', 'X', 'MM', '081234567011', 'Free Fire', 'Aktif'),
('2024012', 'Ketut Aris Budiman', 'XI', 'TKJ', '081234567012', 'Free Fire', 'Aktif'),
('2024013', 'Made Angga Saputra', 'XII', 'RPL', '081234567013', 'Valorant', 'Aktif'),
('2024014', 'Kadek Wisnu Wardana', 'XI', 'MM', '081234567014', 'Valorant', 'Aktif'),
('2024015', 'Wayan Saka Pratama', 'X', 'TKJ', '081234567015', 'Valorant', 'Nonaktif');

-- Data Turnamen
INSERT INTO `turnamen` (`nama_turnamen`, `game`, `tanggal_mulai`, `tanggal_selesai`, `lokasi`, `status`, `hasil`, `deskripsi`) VALUES
('Bali Esport Open 2026', 'Mobile Legends', '2026-06-20', '2026-06-22', 'Denpasar, Bali', 'Upcoming', NULL, 'Turnamen Mobile Legends terbesar di Bali dengan 32 tim peserta.'),
('PUBG Mobile Pro League ID S8', 'PUBG Mobile', '2026-05-10', '2026-05-15', 'Online', 'Ongoing', NULL, 'Liga profesional PUBG Mobile Indonesia season 8, babak grup.'),
('Valorant Challengers ID', 'Valorant', '2026-07-05', '2026-07-10', 'Online', 'Upcoming', NULL, 'Kualifikasi terbuka Valorant Challengers Indonesia.'),
('Free Fire Master League', 'Free Fire', '2026-04-20', '2026-04-28', 'Jakarta', 'Selesai', 'Top 4', 'Liga master Free Fire tingkat nasional, babak playoff.'),
('Badung Gaming Festival S3', 'Mobile Legends', '2026-03-10', '2026-03-15', 'Badung, Bali', 'Selesai', 'Juara 1', 'Festival gaming multi-game di Kabupaten Badung, event LAN.'),
('Nusantara Esport Summit', 'PUBG Mobile', '2026-08-01', '2026-08-05', 'Jakarta', 'Upcoming', NULL, 'Summit esport nasional dengan cabang PUBG Mobile dan Valorant.');

-- Data Absensi Sample (hari ini dan beberapa hari lalu)
INSERT INTO `absensi` (`siswa_id`, `tanggal`, `status`, `keterangan`) VALUES
(1, CURDATE(), 'Hadir', NULL),
(2, CURDATE(), 'Hadir', NULL),
(3, CURDATE(), 'Hadir', NULL),
(4, CURDATE(), 'Izin', 'Ada acara keluarga'),
(5, CURDATE(), 'Hadir', NULL),
(6, CURDATE(), 'Hadir', NULL),
(7, CURDATE(), 'Sakit', 'Demam'),
(8, CURDATE(), 'Hadir', NULL),
(9, CURDATE(), 'Hadir', NULL),
(10, CURDATE(), 'Alpha', NULL),
(11, CURDATE(), 'Hadir', NULL),
(12, CURDATE(), 'Hadir', NULL),
(13, CURDATE(), 'Hadir', NULL),
(14, CURDATE(), 'Izin', 'Izin tidak masuk'),
(15, CURDATE(), 'Hadir', NULL),
(1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Hadir', NULL),
(2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Hadir', NULL),
(3, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Sakit', 'Flu'),
(4, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Hadir', NULL),
(5, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Hadir', NULL),
(6, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Hadir', NULL),
(7, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Hadir', NULL),
(8, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Alpha', NULL),
(9, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Hadir', NULL),
(10, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Hadir', NULL);
