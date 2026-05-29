<?php
// Header include — Sidebar + Topbar
// $pageTitle and $activePage should be set before including this file
$pageTitle = $pageTitle ?? 'Dashboard';
$activePage = $activePage ?? 'dashboard';
$adminNama = $_SESSION['admin_nama'] ?? 'Admin';
$adminInitial = strtoupper(substr($adminNama, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — Globaliti Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../assets/images/logo/logo-removebg-preview.png" alt="Logo">
            <h2>GLOBALITI</h2>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-title">Menu Utama</div>
            <a href="index.php" class="nav-item <?= $activePage === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="siswa.php" class="nav-item <?= $activePage === 'siswa' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Data Siswa
            </a>
            <a href="absensi.php" class="nav-item <?= $activePage === 'absensi' ? 'active' : '' ?>">
                <i class="fas fa-clipboard-check"></i> Absensi
            </a>
            <a href="turnamen.php" class="nav-item <?= $activePage === 'turnamen' ? 'active' : '' ?>">
                <i class="fas fa-trophy"></i> Turnamen
            </a>
            <div class="nav-section-title" style="margin-top:1rem;">Lainnya</div>
            <a href="../index.php" class="nav-item">
                <i class="fas fa-globe"></i> Lihat Website
            </a>
            <a href="logout.php" class="nav-item" style="color:var(--danger);">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="admin-avatar"><?= $adminInitial ?></div>
            <div class="admin-info">
                <div class="admin-name"><?= htmlspecialchars($adminNama) ?></div>
                <div class="admin-role">Administrator</div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <button class="hamburger-btn" id="hamburgerBtn"><i class="fas fa-bars"></i></button>
                <h1><?= htmlspecialchars($pageTitle) ?></h1>
            </div>
            <div class="topbar-right">
                <span style="color:var(--text-muted); font-size:0.8rem;">
                    <i class="far fa-calendar-alt"></i> <?= date('d M Y') ?>
                </span>
                <a href="logout.php" class="btn-logout"><i class="fas fa-power-off"></i> Logout</a>
            </div>
        </header>
        <div class="page-content">
