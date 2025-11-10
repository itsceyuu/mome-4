<?php
// ✅ Pastikan session hanya dimulai sekali
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Ambil nama pengguna dari session
$username = $_SESSION['username'] ?? 'User';

// ✅ Set default variabel halaman
if (!isset($activePage)) $activePage = 'dashboard';
if (!isset($pageTitle)) $pageTitle = 'Home';
if (!isset($pageContent)) $pageContent = '<p>Kontennya belum ada kaka</p>';

$BASE = '/mome-4';

// ✅ Deteksi BASE path secara dinamis
if (isset($_SERVER['SCRIPT_NAME'])) {
    $dir = dirname($_SERVER['SCRIPT_NAME']);
    if ($dir !== '/' && $dir !== '\\') {
        $BASE = $dir;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= htmlspecialchars($pageTitle) ?> — Mome</title>

    <!-- Iconify CDN -->
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <!-- CSS -->
    <link rel="stylesheet" href="<?= $BASE ?>/View/Dashboard.css">
</head>

<body>
    <div id="overlay" class="overlay" style="display:none" aria-hidden="true"></div>

    <div class="app" id="app">
        <!-- SIDEBAR -->
        <aside id="sidebar" class="sidebar" role="navigation" aria-label="Sidebar">
            <div style="display:flex;align-items:center;gap:8px">
                <div class="logo">
                    <img src="<?= $BASE ?>/images/LOGO.png" alt="Mome Logo" />
                </div>
                <button id="toggleBtn" class="toggle" aria-expanded="true" aria-label="Toggle sidebar">
                    <span class="iconify" data-icon="mdi:chevron-left"></span>
                </button>
            </div>

            <nav class="menu" aria-label="Main menu">
                <?php
                $menuItems = [
                    ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'mdi:home-outline'],
                    ['id' => 'expenses', 'label' => 'Track Your Expenses', 'icon' => 'mdi:wallet-outline'],
                    ['id' => 'recap', 'label' => 'MOME Recap', 'icon' => 'mdi:chart-line'],
                    ['id' => 'goals', 'label' => 'MOME Goals', 'icon' => 'mdi:target'],
                    ['id' => 'wishlist', 'label' => 'Wishlist', 'icon' => 'mdi:format-list-bulleted'],
                    ['id' => 'articles', 'label' => 'Articles Finance', 'icon' => 'mdi:file-document-outline']
                ];

                foreach ($menuItems as $item):
                    $isActive = ($activePage === $item['id']) ? 'active' : '';
                ?>
                    <a href="index.php?c=Dashboard&m=navigate&menu=<?= urlencode($item['id']) ?>" 
                       class="menu-item <?= $isActive ?>" role="menuitem">
                        <span class="iconify" data-icon="<?= $item['icon'] ?>" aria-hidden="true"></span>
                        <span class="label"><?= htmlspecialchars($item['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>

        <!-- FAB -->
        <div class="fab" title="Track Your Expenses">
            <span class="iconify" data-icon="mdi:plus"></span>
        </div>

        <!-- MAIN CONTENT -->
        <main class="content" role="main">
            <div class="header">
                <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>

                <!-- 🔹 Profil kanan atas -->
                <div style="display:flex;gap:16px;align-items:center">
                    <div class="header-profile">
                        <span class="username"><?= htmlspecialchars($username) ?></span>
                        <div class="profile-icon">
                            <span class="iconify" data-icon="mdi:account-circle"></span>
                        </div>
                    </div>

                    <!-- Tombol menu mobile -->
                    <button id="mobileMenuBtn" class="toggle" aria-label="Open sidebar on mobile" style="display:none">
                        <span class="iconify" data-icon="mdi:menu"></span>
                    </button>
                </div>
            </div>

            <section>
                <!-- ==================== DASHBOARD ==================== -->
                <?php
                if (!isset($data) || !is_array($data)) $data = [];
                $recap = $data['recap'] ?? [];
                $goal = $data['goal'] ?? [];
                $wishlist = $data['wishlist'] ?? [];
                $article = $data['article'] ?? [];
                ?>

                <div class="home-container">
                    <h2>Hi, <?= htmlspecialchars($username) ?></h2>
                    <p class="subtitle">ready to manage your money?</p>

                    <!-- ✅ MOME Recap Header -->
                    <div class="recap-header">
                        <h4>MOME Recap</h4>
                        <a href="index.php?c=Dashboard&m=navigate&menu=recap" class="show-more">Show More →</a>
                    </div>

                    <!-- MOME Recap Cards -->
                    <div class="recap-section">
                        <a href="index.php?c=Dashboard&m=navigate&menu=recap" class="recap-card outcome">
                            <span class="iconify" data-icon="mdi:wallet-minus" style="font-size:32px"></span>
                            <div class="recap-text">
                                <h3>- <?= number_format($recap['total_expense'] ?? 0, 0, ',', '.') ?></h3>
                                <p>Your Outcome This October</p>
                            </div>
                        </a>

                        <a href="index.php?c=Dashboard&m=navigate&menu=expenses" class="recap-card income">
                            <span class="iconify" data-icon="mdi:wallet-plus" style="font-size:32px"></span>
                            <div class="recap-text">
                                <h3>+ <?= number_format($recap['total_income'] ?? 0, 0, ',', '.') ?></h3>
                                <p>Your Income This October</p>
                            </div>
                        </a>
                    </div>

                    <!-- Goals + Articles (2 Kolom) -->
                    <div class="goal-article-wrapper">
                        <!-- KIRI -->
                        <div>
                            <!-- MOME Goals -->
                            <div class="goal-section" onclick="window.location.href='index.php?c=Dashboard&m=navigate&menu=goals'" style="cursor:pointer;">
                                <h4>MOME Goals</h4>
                                <a href="index.php?c=Dashboard&m=navigate&menu=goals" class="show-more">Show More →</a>
                                <div class="goal-card">
                                    <div class="goal-left">
                                        <div class="goal-icon">
                                            <span class="iconify" data-icon="mdi:home-outline"></span>
                                        </div>
                                        <div class="goal-info">
                                            <h5>Dream House</h5>
                                            <p>Progress 17%</p>
                                            <p>Current Savings</p>
                                        </div>
                                    </div>
                                    <div class="goal-right">
                                        <p style="color:#6b7280;font-weight:500;font-size:14px;margin-bottom:4px;">Target Amount</p>
                                        <div class="progress-dots">
                                            <div class="dot active"></div>
                                            <div class="dot active"></div>
                                            <div class="dot active"></div>
                                            <div class="dot"></div>
                                            <div class="dot"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Wishlist -->
                            <div class="wishlist-section" onclick="window.location.href='index.php?c=Dashboard&m=navigate&menu=wishlist'" style="cursor:pointer;">
                                <h4>Wishlist</h4>
                                <table class="wishlist-table">
                                    <thead>
                                        <tr><th>Title</th><th>Description</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?= htmlspecialchars($wishlist['item_name'] ?? 'Earphone Bluetooth') ?></td>
                                            <td><?= htmlspecialchars($wishlist['description'] ?? 'Harga Rp 950.000, warna putih, link Tokopedia') ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <a href="index.php?c=Dashboard&m=navigate&menu=wishlist" class="show-more">Show More →</a>
                            </div>
                        </div>

                        <!-- KANAN -->
                        <div class="article-section" onclick="window.location.href='index.php?c=Dashboard&m=navigate&menu=articles'" style="cursor:pointer;">
                            <h4>Articles Finance</h4>
                            <div class="article-card">
                                <img src="<?= $BASE ?>/images/article-thumb.jpg" alt="Article image" class="article-thumb">
                                <div class="article-content">
                                    <h5><?= htmlspecialchars($article['title'] ?? 'Simple Money Management Tips for Students') ?></h5>
                                    <p><?= htmlspecialchars($article['infoTambahan'] ?? 'Lorem ipsum dolor sit amet habitasse interdum dapibus cras malesuada mattis sapien quis non rhoncus.') ?></p>
                                    <a href="index.php?c=Dashboard&m=navigate&menu=articles" class="readmore">Read More →</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

<script>
(function () {
    const body = document.body;
    const toggleBtn = document.getElementById('toggleBtn');
    const overlay = document.getElementById('overlay');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.getElementById('sidebar');
    let hoverOpened = false;
    body.classList.add('collapsed');
    toggleBtn.addEventListener('click', e => { e.preventDefault(); e.stopPropagation(); });
    function isMobile() { return window.matchMedia('(max-width: 860px)').matches; }
    function updateToggleVisibility() {
        if (isMobile()) toggleBtn.style.display = ''; 
        else toggleBtn.style.display = body.classList.contains('collapsed') ? 'none' : '';
    }
    updateToggleVisibility();
    sidebar.addEventListener('mouseenter', () => {
        if (body.classList.contains('collapsed') && !isMobile()) {
            body.classList.remove('collapsed'); hoverOpened = true; updateToggleVisibility();
        }
    });
    sidebar.addEventListener('mouseleave', () => {
        if (hoverOpened && !isMobile()) {
            body.classList.add('collapsed'); hoverOpened = false; updateToggleVisibility();
        }
    });
    function updateMobileButtons() {
        if (isMobile()) mobileMenuBtn.style.display = 'inline-flex';
        else { mobileMenuBtn.style.display = 'none'; body.classList.remove('sidebar-open'); overlay.style.display = 'none'; }
        updateToggleVisibility();
    }
    updateMobileButtons();
    window.addEventListener('resize', updateMobileButtons);
    mobileMenuBtn.addEventListener('click', () => {
        body.classList.add('sidebar-open'); overlay.style.display = 'block'; overlay.setAttribute('aria-hidden', 'false');
    });
    overlay.addEventListener('click', () => {
        body.classList.remove('sidebar-open'); overlay.style.display = 'none';
    });
    window.addEventListener('keydown', e => {
        if (e.key === 'Escape' && body.classList.contains('sidebar-open')) {
            body.classList.remove('sidebar-open'); overlay.style.display = 'none';
        }
    });
})();
</script>

</body>
</html>

