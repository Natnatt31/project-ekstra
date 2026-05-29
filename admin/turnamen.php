<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

$pageTitle = 'Turnamen';
$activePage = 'turnamen';

$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
$filterStatus = $_GET['status'] ?? '';

$sql = "SELECT * FROM turnamen WHERE 1=1";
$params = [];
if ($filterStatus) {
    $sql .= " AND status = ?";
    $params[] = $filterStatus;
}
$sql .= " ORDER BY tanggal_mulai DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tournaments = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<?php if ($success): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Toolbar -->
<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:1.5rem;">
    <form method="GET" style="display:flex; gap:0.6rem; align-items:center;">
        <select name="status" class="filter-select" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="Upcoming" <?= $filterStatus === 'Upcoming' ? 'selected' : '' ?>>Upcoming</option>
            <option value="Ongoing" <?= $filterStatus === 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
            <option value="Selesai" <?= $filterStatus === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
        </select>
        <?php if ($filterStatus): ?>
            <a href="turnamen.php" class="btn btn-warning btn-sm"><i class="fas fa-times"></i> Reset</a>
        <?php endif; ?>
    </form>
    <button class="btn btn-primary" data-modal="modalTambahTurnamen"><i class="fas fa-plus"></i> Tambah Turnamen</button>
</div>

<!-- Tournament Cards -->
<?php if (empty($tournaments)): ?>
    <div class="card"><div class="card-body"><div class="empty-state"><i class="fas fa-trophy"></i><p>Belum ada data turnamen</p></div></div></div>
<?php else: ?>
<div class="tournament-grid">
    <?php foreach ($tournaments as $t):
        $statusClass = match($t['status']) {
            'Upcoming' => 'badge-upcoming',
            'Ongoing' => 'badge-ongoing',
            'Selesai' => 'badge-selesai',
            default => 'badge-selesai'
        };
    ?>
    <div class="tournament-card-admin">
        <div class="t-header">
            <span class="badge <?= $statusClass ?>"><?= $t['status'] ?></span>
            <span style="color:var(--text-muted); font-size:0.75rem">#<?= $t['id'] ?></span>
        </div>
        <div class="t-title"><?= htmlspecialchars($t['nama_turnamen']) ?></div>
        <div class="t-game"><i class="fas fa-gamepad"></i> <?= htmlspecialchars($t['game']) ?></div>
        <div class="t-meta">
            <span><i class="far fa-calendar-alt"></i> <?= date('d M Y', strtotime($t['tanggal_mulai'])) ?><?= $t['tanggal_selesai'] ? ' — ' . date('d M Y', strtotime($t['tanggal_selesai'])) : '' ?></span>
            <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($t['lokasi'] ?? 'TBA') ?></span>
            <?php if ($t['hasil']): ?>
                <span style="color:var(--warning); font-weight:700"><i class="fas fa-trophy"></i> <?= htmlspecialchars($t['hasil']) ?></span>
            <?php endif; ?>
        </div>
        <?php if ($t['deskripsi']): ?>
            <p style="color:var(--text-muted); font-size:0.8rem; margin-top:0.3rem;"><?= htmlspecialchars($t['deskripsi']) ?></p>
        <?php endif; ?>
        <div class="t-actions">
            <button class="btn btn-warning btn-sm" onclick='openEditModal("modalEditTurnamen", <?= json_encode($t) ?>)'><i class="fas fa-edit"></i> Edit</button>
            <a href="turnamen_proses.php?action=delete&id=<?= $t['id'] ?>" class="btn btn-danger btn-sm btn-delete"><i class="fas fa-trash"></i> Hapus</a>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Modal Tambah Turnamen -->
<div class="modal-overlay" id="modalTambahTurnamen">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-trophy" style="color:var(--accent-cyan)"></i> Tambah Turnamen</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form method="POST" action="turnamen_proses.php">
            <input type="hidden" name="action" value="add">
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Turnamen</label>
                    <input type="text" name="nama_turnamen" class="form-control" placeholder="Nama event" required>
                </div>
                <div class="form-group">
                    <label>Game</label>
                    <select name="game" class="form-control" required>
                        <option value="Mobile Legends">Mobile Legends</option>
                        <option value="PUBG Mobile">PUBG Mobile</option>
                        <option value="Free Fire">Free Fire</option>
                        <option value="Valorant">Valorant</option>
                    </select>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Lokasi</label>
                    <input type="text" name="lokasi" class="form-control" placeholder="Lokasi / Online">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Upcoming">Upcoming</option>
                            <option value="Ongoing">Ongoing</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Hasil</label>
                        <input type="text" name="hasil" class="form-control" placeholder="Juara 1, Top 4, dll">
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi turnamen..." style="resize:vertical;min-height:80px;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger modal-cancel">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Turnamen -->
<div class="modal-overlay" id="modalEditTurnamen">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-edit" style="color:var(--accent-orange)"></i> Edit Turnamen</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form method="POST" action="turnamen_proses.php">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="">
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Turnamen</label>
                    <input type="text" name="nama_turnamen" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Game</label>
                    <select name="game" class="form-control" required>
                        <option value="Mobile Legends">Mobile Legends</option>
                        <option value="PUBG Mobile">PUBG Mobile</option>
                        <option value="Free Fire">Free Fire</option>
                        <option value="Valorant">Valorant</option>
                    </select>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Lokasi</label>
                    <input type="text" name="lokasi" class="form-control">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Upcoming">Upcoming</option>
                            <option value="Ongoing">Ongoing</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Hasil</label>
                        <input type="text" name="hasil" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3" style="resize:vertical;min-height:80px;"></textarea>
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
