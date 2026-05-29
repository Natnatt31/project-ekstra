<?php
session_start();
$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $login_error = 'Username dan password wajib diisi!';
    } else {
        try {
            require_once 'admin/config/database.php';
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_nama'] = $admin['nama_lengkap'];
                header('Location: admin/index.php');
                exit;
            } else {
                $login_error = 'Username atau password salah!';
            }
        } catch (Exception $e) {
            $login_error = 'Database belum terkonfigurasi! Import file database/globaliti_esport.sql terlebih dahulu.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Globaliti Esport Badung — Organisasi Esport Bali</title>
  <meta name="description" content="Website resmi Globaliti Esport Badung. Organisasi esport berbasis di Kabupaten Badung, Bali. Mobile Legends, Free Fire.">
  <meta name="keywords" content="esport, badung, bali, globaliti, mobile legends, free fire, gaming">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { display: ['Orbitron', 'sans-serif'], body: ['Nunito', 'sans-serif'] },
          colors: {
            dark: { DEFAULT: '#0A0A0F', card: '#12121A', hover: '#1A1A28' },
            cyan: { DEFAULT: '#00D4FF', glow: 'rgba(0,212,255,0.3)' },
            orange: { DEFAULT: '#FF6B00', glow: 'rgba(255,107,0,0.3)' }
          }
        }
      }
    }
  </script>

  <!-- Libraries CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="font-body">

  <!-- Loader -->
  <div class="loader" id="loader">
    <div class="loader-spinner"></div>
  </div>

  <!-- ========== NAVBAR ========== -->
  <nav class="navbar" id="navbar">
    <div class="nav-inner">
      <!-- Logo -->
      <a href="#hero" class="nav-logo">
        <img src="assets/images/logo/logo-removebg-preview.png" alt="Globaliti Logo" class="nav-logo-img">
        <span class="nav-logo-text">GLOBALITI</span>
      </a>

      <!-- Desktop menu -->
      <div class="nav-links">
        <a href="#hero" class="nav-link active">Beranda</a>
        <a href="#about" class="nav-link">Tentang</a>
        <a href="#games" class="nav-link">Game</a>
        <a href="#roster" class="nav-link">Tim</a>

        <!-- Dropdown "Lainnya" -->
        <div class="nav-dropdown">
          <button class="nav-link nav-dropdown-btn">
            Lainnya <i class="fas fa-chevron-down nav-chevron"></i>
          </button>
          <div class="nav-dropdown-menu">
            <a href="#achievements" class="nav-dropdown-item"><i class="fas fa-trophy"></i> Prestasi</a>
            <a href="#tournaments" class="nav-dropdown-item"><i class="fas fa-gamepad"></i> Turnamen</a>
            <a href="#gallery" class="nav-dropdown-item"><i class="fas fa-images"></i> Galeri</a>
            <a href="#contact" class="nav-dropdown-item"><i class="fas fa-envelope"></i> Kontak</a>
            <div class="nav-dropdown-divider"></div>
            <?php if(isset($_SESSION['admin_id'])): ?>
              <a href="admin/index.php" class="nav-dropdown-item nav-dropdown-admin"><i class="fas fa-th-large"></i> Admin Panel</a>
            <?php else: ?>
              <a href="#login" class="nav-dropdown-item nav-dropdown-admin"><i class="fas fa-sign-in-alt"></i> Login Admin</a>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <a href="#contact" class="btn-primary nav-cta">Bergabung</a>

      <button class="hamburger" id="hamburger" aria-label="Buka menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </nav>

  <!-- Mobile Menu -->
  <div class="mobile-overlay" id="mobileOverlay"></div>
  <div class="mobile-menu" id="mobileMenu">
    <!-- tombol close -->
    <button class="mobile-close" id="mobileClose" aria-label="Tutup menu">
      <i class="fas fa-times"></i>
    </button>
    <div class="mobile-menu-inner">
      <a href="#hero" class="nav-link mobile-nav-link">Beranda</a>
      <a href="#about" class="nav-link mobile-nav-link">Tentang</a>
      <a href="#games" class="nav-link mobile-nav-link">Game</a>
      <a href="#roster" class="nav-link mobile-nav-link">Tim</a>
      <a href="#achievements" class="nav-link mobile-nav-link">Prestasi</a>
      <a href="#tournaments" class="nav-link mobile-nav-link">Turnamen</a>
      <a href="#gallery" class="nav-link mobile-nav-link">Galeri</a>
      <a href="#contact" class="nav-link mobile-nav-link">Kontak</a>
      <?php if(isset($_SESSION['admin_id'])): ?>
        <a href="admin/index.php" class="nav-link mobile-nav-link" style="color: var(--accent-cyan)"><i class="fas fa-th-large mr-1"></i> Admin Panel</a>
      <?php else: ?>
        <a href="#login" class="nav-link mobile-nav-link" style="color: var(--accent-cyan)"><i class="fas fa-sign-in-alt mr-1"></i> Login Admin</a>
      <?php endif; ?>
      <a href="#contact" class="btn-primary" style="text-align:center; margin-top: 1rem;">Bergabung Sekarang</a>
    </div>
  </div>

  <!-- ========== HERO ========== -->
  <section id="hero">
    <div id="particles-js"></div>
    <!-- background foto dari assets yang sudah ada -->
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>

    <div class="hero-content">
      <!-- badge lokasi -->
      <div class="hero-badge animate__animated animate__fadeInDown">
        <span class="hero-badge-dot"></span>
        Esport Organization · Badung, Bali
      </div>

      <!-- logo -->
      <img
        src="assets/images/logo/logo-removebg-preview.png"
        alt="Globaliti Logo"
        class="hero-logo animate__animated animate__zoomIn"
      >

      <!-- judul -->
      <h1 class="hero-title animate__animated animate__fadeInUp">
        <span class="typewriter-text"></span>
      </h1>

      <!-- sub -->
      <p class="hero-sub animate__animated animate__fadeIn animate__delay-1s">
        Membangun talenta esport lokal Badung untuk bersaing<br class="hidden md:block"> di kancah nasional dan internasional.
      </p>

      <!-- CTA -->
      <div class="hero-cta animate__animated animate__fadeInUp animate__delay-1s">
        <a href="#roster" class="btn-primary"><i class="fas fa-gamepad"></i> Lihat Tim Kami</a>
        <a href="#contact" class="btn-secondary"><i class="fas fa-envelope"></i> Hubungi Kami</a>
      </div>

      <!-- stats kecil -->
      <div class="hero-stats animate__animated animate__fadeIn animate__delay-2s">
        <div class="hero-stat">
          <span class="hero-stat-num">25+</span>
          <span class="hero-stat-label">Pemain</span>
        </div>
        <div class="hero-stat-divider"></div>
        <div class="hero-stat">
          <span class="hero-stat-num">30+</span>
          <span class="hero-stat-label">Turnamen</span>
        </div>
        <div class="hero-stat-divider"></div>
        <div class="hero-stat">
          <span class="hero-stat-num">2023</span>
          <span class="hero-stat-label">Berdiri</span>
        </div>
      </div>
    </div>

    <!-- scroll indicator -->
    <a href="#about" class="scroll-indicator" aria-label="Scroll ke bawah">
      <span class="scroll-indicator-text">Scroll</span>
      <div class="scroll-indicator-icon">
        <i class="fas fa-chevron-down"></i>
      </div>
    </a>
  </section>

  <!-- ========== ABOUT ========== -->
  <section id="about" class="section-padding">
    <div class="max-w-6xl mx-auto">
      <div class="grid md:grid-cols-2 gap-12 items-center">
        <div data-aos="fade-right">
          <div class="rounded-2xl overflow-hidden border border-white/10">
            <img src="assets/images/gallery/HOK.JPG" alt="Esport Team" class="w-full h-80 object-cover" loading="lazy">
          </div>
        </div>
        <div data-aos="fade-left">
          <p class="text-cyan font-display text-sm tracking-[3px] uppercase mb-2">Tentang Kami</p>
          <h2 class="font-display text-3xl font-bold mb-4">Globaliti Esport <span class="gradient-text">Badung</span></h2>
          <p class="text-gray-400 mb-4">Didirikan pada tahun 2023, Globaliti Esport Badung adalah organisasi esport berbasis di Kabupaten Badung, Bali. Kami berkomitmen untuk mengembangkan talenta gamer lokal dan membawa nama Badung ke panggung kompetisi esport nasional.</p>
          <p class="text-gray-400 mb-6">Dengan roster profesional di berbagai cabang game, kami telah berkompetisi di lebih dari 30 turnamen dan meraih berbagai prestasi membanggakan.</p>
          <div class="grid grid-cols-3 gap-4">
            <div class="text-center p-4 glass-card">
              <div class="stat-number" data-target="25" data-suffix="+">0</div>
              <div class="stat-label">Pemain</div>
            </div>
            <div class="text-center p-4 glass-card">
              <div class="stat-number" data-target="30" data-suffix="+">0</div>
              <div class="stat-label">Turnamen</div>
            </div>
            <div class="text-center p-4 glass-card">
              <div class="stat-number" data-target="2023">0</div>
              <div class="stat-label">Berdiri</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ========== CABANG GAME ========== -->
  <section id="games" class="section-padding bg-dark-card/30">
    <div class="max-w-6xl mx-auto">
      <div class="section-line"></div>
      <h2 class="section-title gradient-text">Cabang Game</h2>
      <p class="section-subtitle">Divisi kompetitif kami yang siap bertanding di berbagai arena</p>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 max-w-2xl mx-auto">
        <div class="game-card" data-aos="zoom-in" data-aos-delay="0">
          <div class="game-card-icon"><img src="assets/images/games/ml.jpg" alt="Mobile Legends"></div>
          <div class="game-card-name">Mobile Legends</div>
          <p class="text-gray-500 text-xs mt-2 relative z-10">Bang Bang • MOBA 5v5</p>
        </div>
        <div class="game-card" data-aos="zoom-in" data-aos-delay="150">
          <div class="game-card-icon"><img src="assets/images/games/ff.jpg" alt="Free Fire"></div>
          <div class="game-card-name">Free Fire</div>
          <p class="text-gray-500 text-xs mt-2 relative z-10">Battle Royale • Squad</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ========== ROSTER ========== -->
  <section id="roster" class="section-padding">
    <div class="max-w-6xl mx-auto">
      <div class="section-line"></div>
      <h2 class="section-title gradient-text">Roster Pemain</h2>
      <p class="section-subtitle">Para pejuang yang membawa nama Globaliti di setiap pertandingan</p>

      <div class="roster-tabs" data-aos="fade-up">
        <button class="roster-tab active" data-game="ml">Mobile Legends</button>
        <button class="roster-tab" data-game="ff">Free Fire</button>
      </div>

      <!-- ML Panel -->
      <div class="roster-panel" data-game="ml">
        <div class="swiper roster-swiper">
          <div class="swiper-wrapper">
            <div class="swiper-slide"><div class="player-card"><img src="assets/images/players/jg.jpg" class="player-card-img" alt="Player" loading="lazy"><div class="player-card-info"><div class="player-card-nick">PhantomX</div><div class="player-card-name">Kadek Ari Wijaya</div><div class="player-card-role">Jungler</div></div></div></div>
            <div class="swiper-slide"><div class="player-card"><img src="assets/images/players/mm.jpg" class="player-card-img" alt="Player" loading="lazy"><div class="player-card-info"><div class="player-card-nick">ShadowKing</div><div class="player-card-name">I Made Surya</div><div class="player-card-role">Goldlaner</div></div></div></div>
            <div class="swiper-slide"><div class="player-card"><img src="assets/images/players/mg.jpg" class="player-card-img" alt="Player" loading="lazy"><div class="player-card-info"><div class="player-card-nick">IceBreaker</div><div class="player-card-name">Wayan Dharma</div><div class="player-card-role">Midlaner</div></div></div></div>
            <div class="swiper-slide"><div class="player-card"><img src="assets/images/players/xp.jpg" class="player-card-img" alt="Player" loading="lazy"><div class="player-card-info"><div class="player-card-nick">TitanRush</div><div class="player-card-name">Nyoman Agus</div><div class="player-card-role">EXP Laner</div></div></div></div>
            <div class="swiper-slide"><div class="player-card"><img src="assets/images/players/rm.jpg" class="player-card-img" alt="Player" loading="lazy"><div class="player-card-info"><div class="player-card-nick">NovaBlade</div><div class="player-card-name">Ketut Bayu</div><div class="player-card-role">Roamer</div></div></div></div>
          </div>
          <div class="swiper-pagination"></div>
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
        </div>  
      </div>

      <!-- FF Panel -->
      <div class="roster-panel" data-game="ff" style="display:none">
        <div class="swiper roster-swiper">
          <div class="swiper-wrapper">
            <div class="swiper-slide"><div class="player-card"><img src="assets/images/players/ff1.jpg" class="player-card-img" alt="Player" loading="lazy"><div class="player-card-info"><div class="player-card-nick">InfernoX</div><div class="player-card-name">Wayan Adi</div><div class="player-card-role">Rusher</div></div></div></div>
            <div class="swiper-slide"><div class="player-card"><img src="assets/images/players/ff2.jpg" class="player-card-img" alt="Player" loading="lazy"><div class="player-card-info"><div class="player-card-nick">FrostBite</div><div class="player-card-name">Nyoman Pande</div><div class="player-card-role">Sniper</div></div></div></div>
            <div class="swiper-slide"><div class="player-card"><img src="assets/images/players/ff3.jpg" class="player-card-img" alt="Player" loading="lazy"><div class="player-card-info"><div class="player-card-nick">DarkWolf</div><div class="player-card-name">Ketut Aris</div><div class="player-card-role">Support</div></div></div></div>
            <div class="swiper-slide"><div class="player-card"><img src="assets/images/players/ff4.jpg" class="player-card-img" alt="Player" loading="lazy"><div class="player-card-info"><div class="player-card-nick">ZeroGrav</div><div class="player-card-name">Gede Mahendra</div><div class="player-card-role">IGL</ div></ div></ div></ div>
          </ div>
          < div class="swiper-pagination"></ div>
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ========== PRESTASI ========== -->
  <section id="achievements" class="section-padding bg-dark-card/30">
    <div class="max-w-6xl mx-auto">
      <div class="section-line"></div>
      <h2 class="section-title gradient-text">Prestasi</h2>
      <p class="section-subtitle">Jejak kemenangan kami di berbagai kompetisi esport</p>
      <div class="timeline">
        <div class="timeline-item" data-aos="fade-up">
          <div class="timeline-dot"></div>
          <div class="timeline-card">
            <div class="flex items-center gap-2 mb-1"><i class="fas fa-trophy trophy-gold text-xl"></i><span class="font-display font-bold text-sm uppercase">Juara 1</span></div>
            <h3 class="font-bold text-lg">Bali Esport Championship 2026</h3>
            <p class="text-gray-400 text-sm">Mobile Legends • Mei 2026</p>
          </div>
        </div>
        <div class="timeline-item" data-aos="fade-up" data-aos-delay="100">
          <div class="timeline-dot"></div>
          <div class="timeline-card">
            <div class="flex items-center gap-2 mb-1"><i class="fas fa-trophy trophy-silver text-xl"></i><span class="font-display font-bold text-sm uppercase">Juara 2</span></div>
            <h3 class="font-bold text-lg">Nusantara FF Championship 2025</h3>
            <p class="text-gray-400 text-sm">Free Fire • Desember 2025</p>
          </div>
        </div>
        <div class="timeline-item" data-aos="fade-up" data-aos-delay="200">
          <div class="timeline-dot"></div>
          <div class="timeline-card">
            <div class="flex items-center gap-2 mb-1"><i class="fas fa-trophy trophy-gold text-xl"></i><span class="font-display font-bold text-sm uppercase">Juara 1</span></div>
            <h3 class="font-bold text-lg">Badung Gaming Festival</h3>
            <p class="text-gray-400 text-sm">Free Fire • Oktober 2025</p>
          </div>
        </div>
        <div class="timeline-item" data-aos="fade-up" data-aos-delay="300">
          <div class="timeline-dot"></div>
          <div class="timeline-card">
            <div class="flex items-center gap-2 mb-1"><i class="fas fa-trophy trophy-bronze text-xl"></i><span class="font-display font-bold text-sm uppercase">Juara 3</span></div>
            <h3 class="font-bold text-lg">Indonesia Mobile Legends League S2</h3>
            <p class="text-gray-400 text-sm">Mobile Legends • Agustus 2025</p>
          </div>
        </div>
        <div class="timeline-item" data-aos="fade-up" data-aos-delay="400">
          <div class="timeline-dot"></div>
          <div class="timeline-card">
            <div class="flex items-center gap-2 mb-1"><i class="fas fa-trophy trophy-gold text-xl"></i><span class="font-display font-bold text-sm uppercase">Juara 1</span></div>
            <h3 class="font-bold text-lg">Denpasar Mobile Legends Cup</h3>
            <p class="text-gray-400 text-sm">Mobile Legends • Juni 2025</p>
          </div>
        </div>
        <div class="timeline-item" data-aos="fade-up" data-aos-delay="500">
          <div class="timeline-dot"></div>
          <div class="timeline-card">
            <div class="flex items-center gap-2 mb-1"><i class="fas fa-trophy trophy-silver text-xl"></i><span class="font-display font-bold text-sm uppercase">Juara 2</span></div>
            <h3 class="font-bold text-lg">Southeast Asia FF Invitational</h3>
            <p class="text-gray-400 text-sm">Free Fire • Maret 2025</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ========== TURNAMEN ========== -->
  <section id="tournaments" class="section-padding">
    <div class="max-w-6xl mx-auto">
      <div class="section-line"></div>
      <h2 class="section-title gradient-text">Jadwal Turnamen</h2>
      <p class="section-subtitle">Event dan kompetisi yang kami ikuti</p>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="tournament-card" data-aos="fade-up">
          <div class="flex justify-between items-start">
            <span class="badge badge-upcoming">Upcoming</span>
            <span class="text-gray-500 text-sm"><i class="far fa-calendar-alt mr-1"></i>20 Jun 2026</span>
          </div>
          <h3 class="font-display font-bold text-lg">Bali Esport Open 2026</h3>
          <p class="text-gray-400 text-sm">Mobile Legends — 32 Tim</p>
          <div class="text-cyan text-sm font-semibold"><i class="fas fa-map-marker-alt mr-1"></i>Denpasar, Bali</div>
        </div>
        <div class="tournament-card" data-aos="fade-up" data-aos-delay="100">
          <div class="flex justify-between items-start">
            <span class="badge badge-ongoing">Ongoing</span>
            <span class="text-gray-500 text-sm"><i class="far fa-calendar-alt mr-1"></i>10-15 Mei 2026</span>
          </div>
          <h3 class="font-display font-bold text-lg">Free Fire Pro League ID S8</h3>
          <p class="text-gray-400 text-sm">Free Fire — Grup Stage</p>
          <div class="text-orange text-sm font-semibold"><i class="fas fa-signal mr-1"></i>Live — Babak Grup</div>
        </div>
        <div class="tournament-card" data-aos="fade-up" data-aos-delay="200">
          <div class="flex justify-between items-start">
            <span class="badge badge-upcoming">Upcoming</span>
            <span class="text-gray-500 text-sm"><i class="far fa-calendar-alt mr-1"></i>5 Jul 2026</span>
          </div>
          <h3 class="font-display font-bold text-lg">MLBB Challengers Cup</h3>
          <p class="text-gray-400 text-sm">Mobile Legends — Open Qualifier</p>
          <div class="text-cyan text-sm font-semibold"><i class="fas fa-globe mr-1"></i>Online</div>
        </div>
        <div class="tournament-card" data-aos="fade-up" data-aos-delay="300">
          <div class="flex justify-between items-start">
            <span class="badge badge-done">Selesai</span>
            <span class="text-gray-500 text-sm"><i class="far fa-calendar-alt mr-1"></i>28 Apr 2026</span>
          </div>
          <h3 class="font-display font-bold text-lg">Free Fire Master League</h3>
          <p class="text-gray-400 text-sm">Free Fire — Playoff</p>
          <div class="text-gray-500 text-sm font-semibold"><i class="fas fa-trophy mr-1"></i>Top 4 Finish</div>
        </div>
        <div class="tournament-card" data-aos="fade-up" data-aos-delay="400">
          <div class="flex justify-between items-start">
            <span class="badge badge-done">Selesai</span>
            <span class="text-gray-500 text-sm"><i class="far fa-calendar-alt mr-1"></i>15 Mar 2026</span>
          </div>
          <h3 class="font-display font-bold text-lg">Badung Gaming Festival S3</h3>
          <p class="text-gray-400 text-sm">Multi-game — LAN Event</p>
          <div class="text-gray-500 text-sm font-semibold"><i class="fas fa-trophy mr-1 trophy-gold"></i>Juara 1 (MLBB)</div>
        </div>
        <div class="tournament-card" data-aos="fade-up" data-aos-delay="500">
          <div class="flex justify-between items-start">
            <span class="badge badge-upcoming">Upcoming</span>
            <span class="text-gray-500 text-sm"><i class="far fa-calendar-alt mr-1"></i>Aug 2026</span>
          </div>
          <h3 class="font-display font-bold text-lg">Nusantara Esport Summit</h3>
          <p class="text-gray-400 text-sm">Mobile Legends &amp; Free Fire</p>
          <div class="text-cyan text-sm font-semibold"><i class="fas fa-map-marker-alt mr-1"></i>Jakarta</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ========== GALERI ========== -->
  <section id="gallery" class="section-padding bg-dark-card/30">
    <div class="max-w-6xl mx-auto">
      <div class="section-line"></div>
      <h2 class="section-title gradient-text">Galeri</h2>
      <p class="section-subtitle">Momen terbaik dari kegiatan dan kompetisi kami</p>
      <div class="gallery-grid">
        <div class="gallery-item" data-aos="fade-in" data-aos-delay="0">
          <a href="" class="glightbox" data-gallery="gallery1">
            <img src="assets/images/gallery/HOK.JPG" alt="Bootcamp Tim" loading="lazy">
          </a>
          <div class="gallery-item-overlay"><p class="text-sm font-semibold">hok j3 ea</p></div>
        </div>
        <div class="gallery-item" data-aos="fade-in" data-aos-delay="100">
          <a href="assets/images/gallery/ml.jpeg" class="glightbox" data-gallery="gallery1">
            <img src="assets/images/gallery/ml.jpeg" alt="Turnamen LAN" loading="lazy">
          </a>
          <div class="gallery-item-overlay"><p class="text-sm font-semibold">tur ciki</p></div>
        </div>
        <div class="gallery-item" data-aos="fade-in" data-aos-delay="200">
          <a href="assets/images/gallery/imo.jpeg" class="glightbox" data-gallery="gallery1">
            <img src="assets/images/gallery/imo.jpeg" alt="Gaming Setup" loading="lazy">
          </a>
          <div class="gallery-item-overlay"><p class="text-sm font-semibold">yang penting imo</p></div>
        </div>
        <div class="gallery-item" data-aos="fade-in" data-aos-delay="300">
          <a href="assets/images/gallery/jawa.jpeg" class="glightbox" data-gallery="gallery1">
            <img src="assets/images/gallery/jawa.jpeg" alt="Practice Session" loading="lazy">
          </a>
          <div class="gallery-item-overlay"><p class="text-sm font-semibold">jawa favorit</p></div>
        </div>
        <div class="gallery-item" data-aos="fade-in" data-aos-delay="400">
          <a href="assets/images/gallery/tim.jpg" class="glightbox" data-gallery="gallery1">
            <img src="assets/images/gallery/tim.jpg" alt="Team Photo" loading="lazy">
          </a>
          <div class="gallery-item-overlay"><p class="text-sm font-semibold">Foto Tim</p></div>
        </div>
        <div class="gallery-item" data-aos="fade-in" data-aos-delay="500">
          <a href="assets/images/gallery/cina.jpeg" class="glightbox" data-gallery="gallery1">
            <img src="assets/images/gallery/cina.jpeg" alt="Award Ceremony" loading="lazy">
          </a>
          <div class="gallery-item-overlay"><p class="text-sm font-semibold">filo cina</p></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ========== SPONSOR ========== -->
  <section id="sponsors" class="section-padding">
    <div class="max-w-6xl mx-auto">
      <div class="section-line"></div>
      <h2 class="section-title gradient-text">Sponsor &amp; Mitra</h2>
      <p class="section-subtitle">Partner yang mendukung perjalanan kami</p>
      <div class="swiper sponsor-swiper mb-10" data-aos="fade-up">
        <div class="swiper-wrapper items-center">
          <div class="swiper-slide flex justify-center"><div class="sponsor-logo bg-dark-card rounded-xl p-6 flex items-center justify-center h-28 w-full border border-white/5"><span class="font-display text-xl font-bold text-gray-500">SPONSOR A</span></div></div>
          <div class="swiper-slide flex justify-center"><div class="sponsor-logo bg-dark-card rounded-xl p-6 flex items-center justify-center h-28 w-full border border-white/5"><span class="font-display text-xl font-bold text-gray-500">SPONSOR B</span></div></div>
          <div class="swiper-slide flex justify-center"><div class="sponsor-logo bg-dark-card rounded-xl p-6 flex items-center justify-center h-28 w-full border border-white/5"><span class="font-display text-xl font-bold text-gray-500">SPONSOR C</span></div></div>
          <div class="swiper-slide flex justify-center"><div class="sponsor-logo bg-dark-card rounded-xl p-6 flex items-center justify-center h-28 w-full border border-white/5"><span class="font-display text-xl font-bold text-gray-500">SPONSOR D</span></div></div>
          <div class="swiper-slide flex justify-center"><div class="sponsor-logo bg-dark-card rounded-xl p-6 flex items-center justify-center h-28 w-full border border-white/5"><span class="font-display text-xl font-bold text-gray-500">SPONSOR E</span></div></div>
        </div>
      </div>
      <div class="text-center" data-aos="fade-up" data-aos-delay="200">
        <a href="#contact" class="btn-secondary"><i class="fas fa-handshake"></i> Jadi Sponsor Kami</a>
      </div>
    </div>
  </section>

  <!-- ========== KONTAK ========== -->
  <section id="contact" class="section-padding bg-dark-card/30">
    <div class="max-w-6xl mx-auto">
      <div class="section-line"></div>
      <h2 class="section-title gradient-text">Hubungi Kami</h2>
      <p class="section-subtitle">Tertarik bergabung atau ingin berkolaborasi? Jangan ragu menghubungi kami</p>
      <div class="grid md:grid-cols-2 gap-10">
        <!-- Contact Info -->
        <div data-aos="fade-right">
          <div class="space-y-6 mb-8">
            <div class="flex items-start gap-4">
              <div class="social-icon shrink-0"><i class="fas fa-envelope"></i></div>
              <div><h4 class="font-bold mb-1">Email</h4><p class="text-gray-400">globaliti.esport@gmail.com</p></div>
            </div>
            <div class="flex items-start gap-4">
              <div class="social-icon shrink-0"><i class="fab fa-whatsapp"></i></div>
              <div><h4 class="font-bold mb-1">WhatsApp</h4><p class="text-gray-400">+62 812-3456-7890</p></div>
            </div>
            <div class="flex items-start gap-4">
              <div class="social-icon shrink-0"><i class="fas fa-map-marker-alt"></i></div>
              <div><h4 class="font-bold mb-1">Lokasi</h4><p class="text-gray-400">Kabupaten Badung, Bali, Indonesia</p></div>
            </div>
          </div>
          <h4 class="font-display font-bold text-sm uppercase tracking-widest mb-4">Follow Kami</h4>
          <div class="flex gap-3">
            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-tiktok"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-discord"></i></a>
          </div>
        </div>
        <!-- Contact Form -->
        <div data-aos="fade-left">
          <form class="contact-form space-y-4" action="mailto:globaliti.esport@gmail.com" method="POST" enctype="text/plain">
            <input type="text" name="name" placeholder="Nama Lengkap" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="subject" placeholder="Subjek">
            <textarea name="message" placeholder="Tulis pesan Anda..." required></textarea>
            <button type="submit" class="btn-primary w-full justify-center"><i class="fas fa-paper-plane"></i> Kirim Pesan</button>
          </form>
        </div>
      </div>
    </div>
  </section>


  <!-- ========== LOGIN ADMIN ========== -->
  <?php if(!isset($_SESSION['admin_id'])): ?>
  <section id="login" class="section-padding">
    <div class="max-w-md mx-auto">
      <div class="section-line"></div>
      <div class="text-center mb-6">
        <img src="assets/images/logo/logo-removebg-preview.png" alt="Globaliti Logo" class="w-24 h-24 mx-auto mb-4 drop-shadow-[0_0_15px_rgba(255,107,0,0.4)]">
        <h2 class="section-title gradient-text !text-3xl">Login Admin</h2>
        <p class="section-subtitle">Akses khusus pengurus Globaliti Esport</p>
      </div>
      
      <div class="glass-card p-8 rounded-2xl border border-white/10">
        <?php if ($login_error): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-500 p-3 rounded-xl text-sm mb-6 flex items-center gap-2 animate__animated animate__shakeX">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($login_error) ?>
            </div>
            <script>
                // Scroll ke section login saat ada error
                document.addEventListener('DOMContentLoaded', () => {
                    const loginSection = document.getElementById('login');
                    if (loginSection) {
                        loginSection.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            </script>
        <?php endif; ?>
        
        <form method="POST" action="#login" class="space-y-5">
            <input type="hidden" name="login_submit" value="1">
            <div class="space-y-2">
                <label for="username" class="block text-xs font-display font-bold text-gray-400 uppercase tracking-widest">Username</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                    <input type="text" id="username" name="username" placeholder="Masukkan username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required class="w-full bg-dark/60 border border-white/10 rounded-xl py-3 pl-11 pr-4 text-white focus:border-cyan focus:ring-1 focus:ring-cyan outline-none transition-all placeholder:text-gray-600">
                </div>
            </div>
            <div class="space-y-2">
                <label for="password" class="block text-xs font-display font-bold text-gray-400 uppercase tracking-widest">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required class="w-full bg-dark/60 border border-white/10 rounded-xl py-3 pl-11 pr-12 text-white focus:border-cyan focus:ring-1 focus:ring-cyan outline-none transition-all placeholder:text-gray-600">
                    <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-cyan transition-colors" onclick="togglePassword()">
                        <i class="fas fa-eye" id="eye-icon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-primary w-full justify-center mt-2 py-3 !text-sm">
                <i class="fas fa-sign-in-alt"></i> Masuk Dashboard
            </button>
        </form>
      </div>
    </div>
  </section>
  <script>
      function togglePassword() {
          const pw = document.getElementById('password');
          const icon = document.getElementById('eye-icon');
          if (pw.type === 'password') {
              pw.type = 'text';
              icon.classList.replace('fa-eye', 'fa-eye-slash');
          } else {
              pw.type = 'password';
              icon.classList.replace('fa-eye-slash', 'fa-eye');
          }
      }
  </script>
  <?php endif; ?>

  <!-- ========== FOOTER ========== -->
  <footer class="footer py-12 px-4">
    <div class="max-w-6xl mx-auto">
      <div class="grid md:grid-cols-3 gap-10 mb-10">
        <div>
          <div class="flex items-center gap-3 mb-3">
            <img src="assets/images/logo/logo-removebg-preview.png" alt="Globaliti Logo" class="w-10 h-10 object-contain">
            <span class="font-display text-2xl font-bold gradient-text">GLOBALITI</span>
          </div>
          <p class="text-gray-500 text-sm">Organisasi esport berbasis di Kabupaten Badung, Bali. Membangun talenta gamer lokal untuk bersaing di kancah nasional.</p>
        </div>
        <div>
          <h4 class="font-display font-bold text-sm uppercase tracking-widest mb-4">Menu</h4>
          <div class="grid grid-cols-2 gap-2">
            <a href="#hero" class="footer-link">Beranda</a>
            <a href="#about" class="footer-link">Tentang</a>
            <a href="#games" class="footer-link">Game</a>
            <a href="#roster" class="footer-link">Tim</a>
            <a href="#achievements" class="footer-link">Prestasi</a>
            <a href="#tournaments" class="footer-link">Turnamen</a>
            <a href="#gallery" class="footer-link">Galeri</a>
            <a href="#contact" class="footer-link">Kontak</a>
          </div>
        </div>
        <div>
          <h4 class="font-display font-bold text-sm uppercase tracking-widest mb-4">Sosial Media</h4>
          <div class="flex gap-3">
            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-tiktok"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-discord"></i></a>
          </div>
        </div>
      </div>
      <div class="border-t border-white/5 pt-6 text-center">
        <p class="text-gray-600 text-sm">&copy; 2026 Globaliti Esport Badung. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Back to Top -->
  <button class="back-to-top" id="backToTop" aria-label="Back to top"><i class="fas fa-chevron-up"></i></button>

  <!-- ========== SCRIPTS ========== -->
  <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="assets/js/particles.config.js"></script>
  <script src="assets/js/swiper.init.js"></script>
</body>
</html>