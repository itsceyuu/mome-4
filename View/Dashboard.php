<?php
// Session sudah di-start di Controller, tidak perlu start lagi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($activePage))
    $activePage = 'dashboard';
if (!isset($pageTitle))
    $pageTitle = 'Home';
if (!isset($pageContent))
    $pageContent = '<p>Kontennya belum ada kaka</p>';

$BASE = '/mome-4';

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
    <link rel="stylesheet" href="View/Dashboard.css">
</head>

<body>
    <div id="overlay" class="overlay" style="display:none" aria-hidden="true"></div>

    <div class="app" id="app">
        <aside id="sidebar" class="sidebar" role="navigation" aria-label="Sidebar">
            <div style="display:flex;align-items:center;gap:8px">
                <div class="logo">
                    <img src="/Images/LOGO.png" alt="Mome Logo" />
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

    <div class="fab" title="Track Your Expenses">
        <span class="iconify" data-icon="mdi:plus"></span>
    </div>


        <main class="content" role="main">
            <div class="header">
                <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
                <div style="display:flex;gap:8px;align-items:center">
                    <button id="mobileMenuBtn" class="toggle" aria-label="Open sidebar on mobile" style="display:none">
                        <span class="iconify" data-icon="mdi:menu"></span>
                    </button>
                </div>
            </div>

            <section>
                <!-- ==================== MULAI DASHBOARD ==================== -->
                <?php
                if (!isset($data) || !is_array($data)) $data = [];
                $recap = $data['recap'] ?? [];
                $goal = $data['goal'] ?? [];
                $wishlist = $data['wishlist'] ?? [];
                $article = $data['article'] ?? [];
                $username = $_SESSION['username'] ?? 'User';
                ?>

                <div class="home-container">
                    <h2>Hi, <?= htmlspecialchars($username) ?></h2>
                    <p class="subtitle">ready to manage your money?</p>

                    <!-- MOME Recap -->
                    <div class="recap-section">
                        <a href="index.php?c=Dashboard&m=navigate&menu=recap" class="recap-card outcome" style="text-decoration:none;color:inherit;">
                            <span class="iconify" data-icon="mdi:wallet-minus" style="font-size:32px"></span>
                            <div class="recap-text">
                                <h3>- <?= number_format($recap['total_expense'] ?? 0, 0, ',', '.') ?></h3>
                                <p>Your Outcome This October</p>
                            </div>
                        </a>

                        <a href="index.php?c=Dashboard&m=navigate&menu=expenses" class="recap-card income" style="text-decoration:none;color:inherit;">
                            <span class="iconify" data-icon="mdi:wallet-plus" style="font-size:32px"></span>
                            <div class="recap-text">
                                <h3>+ <?= number_format($recap['total_income'] ?? 0, 0, ',', '.') ?></h3>
                                <p>Your Income This October</p>
                            </div>
                        </a>
                    </div>

                    <!-- MOME Goals -->
                    <div class="goal-section">
                        <h4>MOME Goals</h4>
                        <a href="index.php?c=Dashboard&m=navigate&menu=goals" style="text-decoration:none;color:inherit;">
                            <div class="goal-card">
                                <div class="goal-title"><?= htmlspecialchars($goal['target_name'] ?? 'Dream House') ?></div>
                                <div class="goal-progress">
                                    <span>Progress <?= isset($goal['current_amount'], $goal['target_amount'])
                                        ? round(($goal['current_amount'] / $goal['target_amount']) * 100) : 17 ?>%</span>
                                    <div class="progress-bar">
                                        <div class="progress-fill"
                                            style="width: <?= isset($goal['current_amount'], $goal['target_amount'])
                                                ? round(($goal['current_amount'] / $goal['target_amount']) * 100) : 17 ?>%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Wishlist -->
                    <a href="index.php?c=Dashboard&m=navigate&menu=wishlist" style="text-decoration:none;color:inherit;">
                        <div class="wishlist-section">
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
                        </div>
                    </a>

                    <!-- Article Finance -->
                    <a href="index.php?c=Dashboard&m=navigate&menu=articles" style="text-decoration:none;color:inherit;">
                        <div class="article-section">
                            <h4>Articles Finance</h4>
                            <div class="article-card">
                                <img src="<?= $BASE ?>/images/article-thumb.jpg" alt="Article image" class="article-thumb">
                                <div class="article-text">
                                    <h5><?= htmlspecialchars($article['title'] ?? 'Simple Money Management Tips for Students') ?></h5>
                                    <p><?= htmlspecialchars($article['infoTambahan'] ?? 'Be smart managing your money as a student!') ?></p>
                                    <span class="readmore">Read More →</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <style>
                .home-container {padding: 16px 24px; font-family: 'Poppins', sans-serif;}
                .subtitle {color:#777;margin-top:-10px;margin-bottom:24px;}
                .recap-section{display:flex;gap:16px;margin-bottom:28px;}
                .recap-card{flex:1;border-radius:12px;padding:14px 20px;display:flex;align-items:center;gap:12px;transition:.2s;}
                .recap-card.outcome{background-color:#ffebee;}
                .recap-card.income{background-color:#e8f5e9;}
                .recap-card:hover,.goal-card:hover,.wishlist-section:hover,.article-card:hover{
                    transform:translateY(-2px);box-shadow:0 3px 8px rgba(0,0,0,0.1);
                }
                .goal-section{margin-bottom:28px;}
                .goal-card{background:#f8f9fc;border-radius:10px;padding:14px;}
                .progress-bar{width:100%;background-color:#e0e0e0;height:8px;border-radius:4px;margin-top:4px;}
                .progress-fill{height:8px;background-color:#2196f3;border-radius:4px;}
                .wishlist-table{width:100%;border-collapse:collapse;background:white;border-radius:10px;overflow:hidden;}
                .wishlist-table th,.wishlist-table td{padding:10px 14px;border-bottom:1px solid #eee;}
                .wishlist-table th{background-color:#f1f3f6;text-align:left;}
                .article-card{display:flex;gap:12px;background:#fff;border-radius:12px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,0.1);}
                .article-thumb{width:120px;height:90px;border-radius:8px;object-fit:cover;}
                .readmore{display:inline-block;color:#2563eb;font-weight:500;text-decoration:none;font-size:14px;}
                </style>
                <!-- ==================== AKHIR DASHBOARD ==================== -->
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