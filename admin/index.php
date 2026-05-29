<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

// Stats
$totalSiswa = $pdo->query("SELECT COUNT(*) FROM siswa WHERE status='Aktif'")->fetchColumn();
$hadirHariIni = $pdo->query("SELECT COUNT(*) FROM absensi WHERE tanggal=CURDATE() AND status='Hadir'")->fetchColumn();
$totalTurnamen = $pdo->query("SELECT COUNT(*) FROM turnamen")->fetchColumn();
$turnamenAktif = $pdo->query("SELECT COUNT(*) FROM turnamen WHERE status IN ('Upcoming','Ongoing')")->fetchColumn();

// Absensi chart data (last 7 days)
$chartData = $pdo->query("
    SELECT tanggal,
        SUM(CASE WHEN status='Hadir' THEN 1 ELSE 0 END) as hadir,
        SUM(CASE WHEN status='Izin' THEN 1 ELSE 0 END) as izin,
        SUM(CASE WHEN status='Sakit' THEN 1 ELSE 0 END) as sakit,
        SUM(CASE WHEN status='Alpha' THEN 1 ELSE 0 END) as alpha
    FROM absensi
    WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY tanggal ORDER BY tanggal ASC
")->fetchAll();

$chartLabels = []; $chartHadir = []; $chartIzin = []; $chartSakit = []; $chartAlpha = [];
foreach ($chartData as $row) {
    $chartLabels[] = date('d/m', strtotime($row['tanggal']));
    $chartHadir[] = (int)$row['hadir'];
    $chartIzin[] = (int)$row['izin'];
    $chartSakit[] = (int)$row['sakit'];
    $chartAlpha[] = (int)$row['alpha'];
}

// Recent tournaments
$recentTournaments = $pdo->query("SELECT * FROM turnamen ORDER BY tanggal_mulai DESC LIMIT 5")->fetchAll();

// Divisi distribution
$divisiData = $pdo->query("SELECT game_divisi, COUNT(*) as total FROM siswa WHERE status='Aktif' GROUP BY game_divisi")->fetchAll();

require_once 'includes/header.php';
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-value"><?= $totalSiswa ?></div>
        <div class="stat-label">Total Siswa Aktif</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-clipboard-check"></i></div>
        <div class="stat-value"><?= $hadirHariIni ?></div>
        <div class="stat-label">Hadir Hari Ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-trophy"></i></div>
        <div class="stat-value"><?= $totalTurnamen ?></div>
        <div class="stat-label">Total Turnamen</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-fire"></i></div>
        <div class="stat-value"><?= $turnamenAktif ?></div>
        <div class="stat-label">Turnamen Aktif</div>
    </div>
</div>

<!-- Charts & Tables Row -->
<div class="grid-2">
    <!-- Absensi Chart -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-bar" style="color:var(--accent-cyan)"></i> Statistik Absensi (7 Hari)</h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="absensiChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Divisi Pie Chart -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-gamepad" style="color:var(--accent-orange)"></i> Distribusi Divisi Game</h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="divisiChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Tournaments -->
<div class="card" style="margin-top:1.5rem;">
    <div class="card-header">
        <h3><i class="fas fa-trophy" style="color:var(--warning)"></i> Turnamen Terbaru</h3>
        <a href="turnamen.php" class="btn btn-primary btn-sm"><i class="fas fa-arrow-right"></i> Lihat Semua</a>
    </div>
    <div class="card-body no-padding">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Turnamen</th>
                        <th>Game</th>
                        <th>Tanggal</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Hasil</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentTournaments as $t): ?>
                    <tr>
                        <td style="font-weight:700; color:var(--text-primary);"><?= htmlspecialchars($t['nama_turnamen']) ?></td>
                        <td><span style="color:var(--accent-cyan)"><?= htmlspecialchars($t['game']) ?></span></td>
                        <td><?= date('d M Y', strtotime($t['tanggal_mulai'])) ?></td>
                        <td><?= htmlspecialchars($t['lokasi'] ?? '-') ?></td>
                        <td>
                            <?php
                            $statusClass = match($t['status']) {
                                'Upcoming' => 'badge-upcoming',
                                'Ongoing' => 'badge-ongoing',
                                'Selesai' => 'badge-selesai',
                                default => 'badge-selesai'
                            };
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= $t['status'] ?></span>
                        </td>
                        <td><?= htmlspecialchars($t['hasil'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
// Absensi Bar Chart
const absensiCtx = document.getElementById('absensiChart').getContext('2d');
new Chart(absensiCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [
            { label:'Hadir', data:<?= json_encode($chartHadir) ?>, backgroundColor:'rgba(46,213,115,0.7)', borderRadius:6 },
            { label:'Izin', data:<?= json_encode($chartIzin) ?>, backgroundColor:'rgba(0,212,255,0.7)', borderRadius:6 },
            { label:'Sakit', data:<?= json_encode($chartSakit) ?>, backgroundColor:'rgba(255,165,2,0.7)', borderRadius:6 },
            { label:'Alpha', data:<?= json_encode($chartAlpha) ?>, backgroundColor:'rgba(255,71,87,0.7)', borderRadius:6 }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { labels: { color:'#C0C0C0', font:{family:'Nunito'} } } },
        scales: {
            x: { ticks:{color:'#666680'}, grid:{color:'rgba(255,255,255,0.05)'} },
            y: { ticks:{color:'#666680'}, grid:{color:'rgba(255,255,255,0.05)'}, beginAtZero:true }
        }
    }
});

// Divisi Doughnut Chart
const divisiCtx = document.getElementById('divisiChart').getContext('2d');
new Chart(divisiCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($divisiData, 'game_divisi')) ?>,
        datasets: [{
            data: <?= json_encode(array_map('intval', array_column($divisiData, 'total'))) ?>,
            backgroundColor: ['#00D4FF','#FF6B00','#2ED573','#FFA502'],
            borderColor: '#12121A', borderWidth: 3
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        cutout: '65%',
        plugins: { legend: { position:'bottom', labels: { color:'#C0C0C0', font:{family:'Nunito'}, padding:15 } } }
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
