<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

$pageTitle = 'Absensi Siswa';
$activePage = 'absensi';

$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$filterDivisi = $_GET['divisi'] ?? '';
$filterStatuses = $_GET['status_filter'] ?? [];
if (!is_array($filterStatuses)) {
    $filterStatuses = $filterStatuses ? explode(',', $filterStatuses) : [];
}
$allowedStatuses = ['Hadir', 'Izin', 'Sakit', 'Alpha'];
$filterStatuses = array_values(array_intersect($allowedStatuses, $filterStatuses));

// Fetch active students
$sqlSiswa = "SELECT * FROM siswa WHERE status='Aktif'";
$paramsSiswa = [];
if ($filterDivisi) {
    $sqlSiswa .= " AND game_divisi = ?";
    $paramsSiswa[] = $filterDivisi;
}
$sqlSiswa .= " ORDER BY nama ASC";
$stmtSiswa = $pdo->prepare($sqlSiswa);
$stmtSiswa->execute($paramsSiswa);
$students = $stmtSiswa->fetchAll();

// Fetch existing attendance for selected date
$existingAbsensi = [];
$stmtAbsen = $pdo->prepare("SELECT siswa_id, status, keterangan FROM absensi WHERE tanggal = ?");
$stmtAbsen->execute([$tanggal]);
foreach ($stmtAbsen->fetchAll() as $row) {
    $existingAbsensi[$row['siswa_id']] = $row;
}

// Daily summary
$summary = $pdo->prepare("
    SELECT status, COUNT(*) as total FROM absensi WHERE tanggal = ? GROUP BY status
");
$summary->execute([$tanggal]);
$summaryData = [];
foreach ($summary->fetchAll() as $row) {
    $summaryData[$row['status']] = (int)$row['total'];
}

require_once 'includes/header.php';
?>

<?php if ($success): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Summary Cards -->
<?php if (!empty($summaryData)): ?>
<div class="stats-grid" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-check"></i></div>
        <div class="stat-value" id="summaryHadir"><?= $summaryData['Hadir'] ?? 0 ?></div>
        <div class="stat-label">Hadir</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-envelope"></i></div>
        <div class="stat-value" id="summaryIzin"><?= $summaryData['Izin'] ?? 0 ?></div>
        <div class="stat-label">Izin</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-medkit"></i></div>
        <div class="stat-value" id="summarySakit"><?= $summaryData['Sakit'] ?? 0 ?></div>
        <div class="stat-label">Sakit</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
        <div class="stat-value" id="summaryAlpha"><?= $summaryData['Alpha'] ?? 0 ?></div>
        <div class="stat-label">Alpha</div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-clipboard-check" style="color:var(--accent-cyan)"></i> Absensi — <?= date('d M Y', strtotime($tanggal)) ?></h3>
    </div>
    <div class="card-body">
        <!-- Date & Filter -->
        <form method="GET" class="toolbar" style="margin-bottom:1.5rem; gap:1rem;">
            <div style="display:flex; align-items:center; gap:0.5rem;">
                <label style="font-size:0.85rem; font-weight:700; color:var(--text-secondary);">Tanggal:</label>
                <input type="date" name="tanggal" value="<?= $tanggal ?>" class="form-control" style="width:auto;" onchange="this.form.submit()">
            </div>
            <select name="divisi" class="filter-select" onchange="this.form.submit()">
                <option value="">Semua Divisi</option>
                <option value="Mobile Legends" <?= $filterDivisi === 'Mobile Legends' ? 'selected' : '' ?>>Mobile Legends</option>
                <option value="PUBG Mobile" <?= $filterDivisi === 'PUBG Mobile' ? 'selected' : '' ?>>PUBG Mobile</option>
                <option value="Free Fire" <?= $filterDivisi === 'Free Fire' ? 'selected' : '' ?>>Free Fire</option>
                <option value="Valorant" <?= $filterDivisi === 'Valorant' ? 'selected' : '' ?>>Valorant</option>
            </select>
            <div style="display:flex; align-items:center; gap:0.5rem;">
                <span style="font-size:0.85rem; font-weight:700; color:var(--text-secondary);">Filter Status:</span>
                <?php foreach ($allowedStatuses as $status): ?>
                    <label style="display:inline-flex; align-items:center; gap:0.3rem; font-size:0.85rem; color:var(--text-secondary);">
                        <input type="checkbox" class="status-filter-checkbox" name="status_filter[]" value="<?= $status ?>" <?= in_array($status, $filterStatuses) ? 'checked' : '' ?>>
                        <?= $status ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </form>

        <!-- Attendance Form -->
        <form method="POST" action="absensi_proses.php">
            <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
            <div class="table-responsive">
                <table id="absensiTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Divisi</th>
                            <th>Status Kehadiran</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $i => $s):
                            $currentStatus = $existingAbsensi[$s['id']]['status'] ?? '';
                            $currentKet = $existingAbsensi[$s['id']]['keterangan'] ?? '';
                            $statusAttr = $currentStatus ?: '';
                        ?>
                        <tr data-status="<?= htmlspecialchars($statusAttr) ?>">
                            <td><?= $i + 1 ?></td>
                            <td style="font-family:'Orbitron',sans-serif; font-size:0.8rem; color:var(--accent-cyan)"><?= htmlspecialchars($s['nis']) ?></td>
                            <td style="font-weight:700; color:var(--text-primary)"><?= htmlspecialchars($s['nama']) ?></td>
                            <td><?= $s['kelas'] ?> <?= $s['jurusan'] ?></td>
                            <td><span style="color:var(--accent-orange); font-weight:600; font-size:0.8rem"><?= $s['game_divisi'] ?></span></td>
                            <td>
                                <input type="hidden" name="siswa_id[]" value="<?= $s['id'] ?>">
                                <div class="absen-options">
                                    <?php foreach (['Hadir','Izin','Sakit','Alpha'] as $st): ?>
                                    <input type="radio" name="status_<?= $s['id'] ?>" value="<?= $st ?>" id="s<?= $s['id'] ?>_<?= $st ?>"
                                        class="absen-radio" <?= $currentStatus === $st ? 'checked' : '' ?>>
                                    <label for="s<?= $s['id'] ?>_<?= $st ?>" class="absen-label label-<?= strtolower($st) ?>"><?= $st ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="keterangan_<?= $s['id'] ?>" value="<?= htmlspecialchars($currentKet) ?>"
                                    class="form-control" style="min-width:120px; padding:0.4rem 0.6rem; font-size:0.8rem;" placeholder="Opsional">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="margin-top:1.5rem; display:flex; justify-content:flex-end; gap:0.8rem;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Absensi</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
