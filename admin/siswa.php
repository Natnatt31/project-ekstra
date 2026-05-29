<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

$pageTitle = 'Data Siswa';
$activePage = 'siswa';

// Handle flash messages
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

// Fetch students with filters
$search = $_GET['search'] ?? '';
$filterDivisi = $_GET['divisi'] ?? '';
$filterStatus = $_GET['status'] ?? '';

$sql = "SELECT s.*,
    (SELECT COUNT(*) FROM absensi a WHERE a.siswa_id = s.id AND a.status = 'Hadir') AS hadir_count,
    (SELECT COUNT(*) FROM absensi a WHERE a.siswa_id = s.id AND a.status = 'Izin') AS izin_count,
    (SELECT COUNT(*) FROM absensi a WHERE a.siswa_id = s.id AND a.status = 'Sakit') AS sakit_count,
    (SELECT COUNT(*) FROM absensi a WHERE a.siswa_id = s.id AND a.status = 'Alpha') AS alpha_count
    FROM siswa s WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (nama LIKE ? OR nis LIKE ? OR kelas LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}
if ($filterDivisi) {
    $sql .= " AND game_divisi = ?";
    $params[] = $filterDivisi;
}
if ($filterStatus) {
    $sql .= " AND status = ?";
    $params[] = $filterStatus;
}

$sql .= " ORDER BY nama ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<?php if ($success): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-users" style="color:var(--accent-cyan)"></i> Daftar Siswa Ekstrakulikuler</h3>
        <button class="btn btn-primary" data-modal="modalTambahSiswa"><i class="fas fa-plus"></i> Tambah Siswa</button>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="toolbar" style="margin-bottom:1.2rem;">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input id="searchInput" type="text" name="search" placeholder="Cari nama, NIS, kelas..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <select name="divisi" class="filter-select">
                <option value="">Semua Divisi</option>
                <option value="Mobile Legends" <?= $filterDivisi === 'Mobile Legends' ? 'selected' : '' ?>>Mobile Legends</option>
                <option value="PUBG Mobile" <?= $filterDivisi === 'PUBG Mobile' ? 'selected' : '' ?>>PUBG Mobile</option>
                <option value="Free Fire" <?= $filterDivisi === 'Free Fire' ? 'selected' : '' ?>>Free Fire</option>
                <option value="Valorant" <?= $filterDivisi === 'Valorant' ? 'selected' : '' ?>>Valorant</option>
            </select>
            <select name="status" class="filter-select">
                <option value="">Semua Status</option>
                <option value="Aktif" <?= $filterStatus === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                <option value="Nonaktif" <?= $filterStatus === 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
            <?php if ($search || $filterDivisi || $filterStatus): ?>
                <a href="siswa.php" class="btn btn-warning btn-sm"><i class="fas fa-times"></i> Reset</a>
            <?php endif; ?>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Divisi Game</th>
                        <th>Hadir</th>
                        <th>Izin</th>
                        <th>Sakit</th>
                        <th>Alpha</th>
                        <th>No. HP</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr><td colspan="13"><div class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada data siswa</p></div></td></tr>
                    <?php else: ?>
                        <?php foreach ($students as $i => $s): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td style="font-family:'Orbitron',sans-serif; font-size:0.8rem; color:var(--accent-cyan)"><?= htmlspecialchars($s['nis']) ?></td>
                            <td style="font-weight:700; color:var(--text-primary)"><?= htmlspecialchars($s['nama']) ?></td>
                            <td><?= htmlspecialchars($s['kelas']) ?></td>
                            <td><?= htmlspecialchars($s['jurusan']) ?></td>
                            <td><span style="color:var(--accent-orange); font-weight:600"><?= htmlspecialchars($s['game_divisi']) ?></span></td>
                            <td><span style="color:var(--success); font-weight:700"><?= $s['hadir_count'] ?></span></td>
                            <td><span style="color:var(--info); font-weight:700"><?= $s['izin_count'] ?></span></td>
                            <td><span style="color:var(--warning); font-weight:700"><?= $s['sakit_count'] ?></span></td>
                            <td><span style="color:var(--danger); font-weight:700"><?= $s['alpha_count'] ?></span></td>
                            <td><?= htmlspecialchars($s['no_hp'] ?? '-') ?></td>
                            <td><span class="badge badge-<?= strtolower($s['status']) ?>"><?= $s['status'] ?></span></td>
                            <td>
                                <div style="display:flex; gap:0.4rem;">
                                    <button class="btn btn-warning btn-sm" onclick='openEditModal("modalEditSiswa", <?= json_encode($s) ?>)'><i class="fas fa-edit"></i></button>
                                    <a href="siswa_proses.php?action=delete&id=<?= $s['id'] ?>" class="btn btn-danger btn-sm btn-delete"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem; color:var(--text-muted); font-size:0.8rem;">
            Total: <?= count($students) ?> siswa
        </div>
    </div>
</div>

<!-- Modal Tambah Siswa -->
<div class="modal-overlay" id="modalTambahSiswa">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus" style="color:var(--accent-cyan)"></i> Tambah Siswa</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form method="POST" action="siswa_proses.php">
            <input type="hidden" name="action" value="add">
            <div class="modal-body">
                <div class="form-group">
                    <label>NIS</label>
                    <input type="text" name="nis" class="form-control" placeholder="Contoh: 2024016" required>
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama siswa" required>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="form-group">
                        <label>Kelas</label>
                        <select name="kelas" class="form-control" required>
                            <option value="X">X</option>
                            <option value="XI">XI</option>
                            <option value="XII">XII</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jurusan</label>
                        <select name="jurusan" class="form-control" required>
                            <option value="RPL">RPL</option>
                            <option value="TKJ">TKJ</option>
                            <option value="MM">MM</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-group">
                    <label>Divisi Game</label>
                    <select name="game_divisi" class="form-control" required>
                        <option value="Mobile Legends">Mobile Legends</option>
                        <option value="PUBG Mobile">PUBG Mobile</option>
                        <option value="Free Fire">Free Fire</option>
                        <option value="Valorant">Valorant</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger modal-cancel">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Siswa -->
<div class="modal-overlay" id="modalEditSiswa">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-user-edit" style="color:var(--accent-orange)"></i> Edit Siswa</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form method="POST" action="siswa_proses.php">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="">
            <div class="modal-body">
                <div class="form-group">
                    <label>NIS</label>
                    <input type="text" name="nis" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="form-group">
                        <label>Kelas</label>
                        <select name="kelas" class="form-control" required>
                            <option value="X">X</option>
                            <option value="XI">XI</option>
                            <option value="XII">XII</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jurusan</label>
                        <select name="jurusan" class="form-control" required>
                            <option value="RPL">RPL</option>
                            <option value="TKJ">TKJ</option>
                            <option value="MM">MM</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="no_hp" class="form-control">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="form-group">
                        <label>Divisi Game</label>
                        <select name="game_divisi" class="form-control" required>
                            <option value="Mobile Legends">Mobile Legends</option>
                            <option value="PUBG Mobile">PUBG Mobile</option>
                            <option value="Free Fire">Free Fire</option>
                            <option value="Valorant">Valorant</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger modal-cancel">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
