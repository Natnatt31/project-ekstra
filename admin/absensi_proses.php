<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

$tanggal = $_POST['tanggal'] ?? date('Y-m-d');
$siswaIds = $_POST['siswa_id'] ?? [];

if (empty($siswaIds)) {
    header('Location: absensi.php?error=Tidak ada data untuk disimpan!');
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO absensi (siswa_id, tanggal, status, keterangan)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE status = VALUES(status), keterangan = VALUES(keterangan)
    ");

    foreach ($siswaIds as $siswaId) {
        $status = $_POST["status_$siswaId"] ?? '';
        $keterangan = trim($_POST["keterangan_$siswaId"] ?? '');

        if ($status) {
            $stmt->execute([$siswaId, $tanggal, $status, $keterangan ?: null]);
        }
    }

    $pdo->commit();
    header("Location: absensi.php?tanggal=$tanggal&success=Absensi berhasil disimpan!");
} catch (PDOException $e) {
    $pdo->rollBack();
    header("Location: absensi.php?tanggal=$tanggal&error=Gagal menyimpan absensi!");
}
exit;
