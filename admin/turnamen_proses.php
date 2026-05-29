<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        try {
            $stmt = $pdo->prepare("INSERT INTO turnamen (nama_turnamen, game, tanggal_mulai, tanggal_selesai, lokasi, status, hasil, deskripsi) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([
                trim($_POST['nama_turnamen']),
                $_POST['game'],
                $_POST['tanggal_mulai'],
                $_POST['tanggal_selesai'] ?: null,
                trim($_POST['lokasi'] ?? ''),
                $_POST['status'],
                trim($_POST['hasil'] ?? '') ?: null,
                trim($_POST['deskripsi'] ?? '') ?: null
            ]);
            header('Location: turnamen.php?success=Turnamen berhasil ditambahkan!');
        } catch (PDOException $e) {
            header('Location: turnamen.php?error=Gagal menambahkan turnamen!');
        }
        break;

    case 'edit':
        try {
            $stmt = $pdo->prepare("UPDATE turnamen SET nama_turnamen=?, game=?, tanggal_mulai=?, tanggal_selesai=?, lokasi=?, status=?, hasil=?, deskripsi=? WHERE id=?");
            $stmt->execute([
                trim($_POST['nama_turnamen']),
                $_POST['game'],
                $_POST['tanggal_mulai'],
                $_POST['tanggal_selesai'] ?: null,
                trim($_POST['lokasi'] ?? ''),
                $_POST['status'],
                trim($_POST['hasil'] ?? '') ?: null,
                trim($_POST['deskripsi'] ?? '') ?: null,
                $_POST['id']
            ]);
            header('Location: turnamen.php?success=Turnamen berhasil diupdate!');
        } catch (PDOException $e) {
            header('Location: turnamen.php?error=Gagal mengupdate turnamen!');
        }
        break;

    case 'delete':
        try {
            $stmt = $pdo->prepare("DELETE FROM turnamen WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            header('Location: turnamen.php?success=Turnamen berhasil dihapus!');
        } catch (PDOException $e) {
            header('Location: turnamen.php?error=Gagal menghapus turnamen!');
        }
        break;

    default:
        header('Location: turnamen.php');
}
exit;
