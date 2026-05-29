<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        try {
            $stmt = $pdo->prepare("INSERT INTO siswa (nis, nama, kelas, jurusan, no_hp, game_divisi) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                trim($_POST['nis']),
                trim($_POST['nama']),
                $_POST['kelas'],
                $_POST['jurusan'],
                trim($_POST['no_hp'] ?? ''),
                $_POST['game_divisi']
            ]);
            header('Location: siswa.php?success=Siswa berhasil ditambahkan!');
        } catch (PDOException $e) {
            $msg = str_contains($e->getMessage(), 'Duplicate') ? 'NIS sudah terdaftar!' : 'Gagal menambahkan siswa!';
            header("Location: siswa.php?error=$msg");
        }
        break;

    case 'edit':
        try {
            $stmt = $pdo->prepare("UPDATE siswa SET nis=?, nama=?, kelas=?, jurusan=?, no_hp=?, game_divisi=?, status=? WHERE id=?");
            $stmt->execute([
                trim($_POST['nis']),
                trim($_POST['nama']),
                $_POST['kelas'],
                $_POST['jurusan'],
                trim($_POST['no_hp'] ?? ''),
                $_POST['game_divisi'],
                $_POST['status'],
                $_POST['id']
            ]);
            header('Location: siswa.php?success=Data siswa berhasil diupdate!');
        } catch (PDOException $e) {
            header('Location: siswa.php?error=Gagal mengupdate data siswa!');
        }
        break;

    case 'delete':
        try {
            $stmt = $pdo->prepare("DELETE FROM siswa WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            header('Location: siswa.php?success=Siswa berhasil dihapus!');
        } catch (PDOException $e) {
            header('Location: siswa.php?error=Gagal menghapus siswa!');
        }
        break;

    default:
        header('Location: siswa.php');
}
exit;
