<?php
session_start();
if (!isset($activePage))
    $activePage = 'dashboard';
if (!isset($activeTitle))
    $pageTitle = 'MOME Goals';
if (!isset($pageContent1))
    $pageContent1 = '<p>Your savings journey continues!</p>';
if (!isset($pageContent2))
    $pageContent2 = '<p> Review your progress toward your target this month and stay consistent — every step brings you closer to your goal</p>';
if (!isset($pageContent3))
    $pageContent3 = '<p> closer to your goal. </p>';


$BASE = '/mome-4';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — Mome</title>

    <!-- iconify CDN -->
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="<?= $BASE ?>/View/Goals.css">
</head>

<body>
    <div class="overlay" id="overlay" style="display:none" aria-hidden="true"></div>

    <div class="app" id="app">
        <aside id="sidebar" class="sidebar" role="navigation" aria-label="Sidebar">
            <div style="display:flex;align-items:center;gap:8px">
                <div class="logo" aria-hidden="false">
                    <img src="<?= $BASE ?>/Images/LOGO.png" alt="Mome Logo">
                </div>

                <!-- toggle desktop -->
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
                    ['id' => 'articles', 'label' => 'Articles Finance', 'icon' => 'mdi:file-document-outline'],
                ];

                foreach ($menuItems as $item):
                    $isActive = ($activePage == $item['id']) ? 'active' : '';
                    ?>
                    <a href="?page=<?= urlencode($item['id']) ?>" class="menu-item <?= $isActive ?>" role="menuitem">
                        <span class="iconify" data-icon="<?= $item['icon'] ?>" aria-hidden="true"></span>
                        <span class="label"><?= htmlspecialchars($item['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>

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
                <?= $pageContent1 ?>
                <?= $pageContent2 ?>
                <?= $pageContent3 ?>
            </section>


            <!-- Goals Card -->
            <section class="goals-section" aria-label="Goals cards">
                <article class="goal-card" role="region" aria-labelledby="goal-title-1">
                    <button class="goal-card__close" aria-label="Close details">
                        <span class="iconify" data-icon="mdi:close" aria-hidden="true"></span>
                    </button>

                    <div class="goal-card__left">
                        <div class="goal-card__icon" aria-hidden="true">
                            <span class="iconify" data-icon="mdi:home" data-inline="false"></span>
                        </div>

                        <div class="goal-card__meta">
                            <h2 id="goal-title-1">Dream House</h2>
                            <div class="goal-card__progress">Progres <strong>17%</strong></div>

                            <div class="goal-card__labels">
                                <div class="label">Current Savings</div>
                                <div class="dots dots--small" aria-hidden="true">
                                    <span></span><span></span><span></span><span></span><span></span><span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="goal-card__spacer" aria-hidden="true"></div>

                    <div class="goal-card__right">
                        <div class="label label--right">Target Amount</div>
                        <div class="dots" aria-hidden="true">
                            <span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                    </div>
                </article>
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

            // Pastikan sidebar default collapsed (sesuai permintaanmu)
            body.classList.add('collapsed');

            // Nonaktifkan fungsi toggle button (klik tidak akan mengubah apapun)
            toggleBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            });

            function isMobile() {
                return window.matchMedia('(max-width: 860px)').matches;
            }

            // Kontrol visibilitas tombol toggle agar tidak "keluar" saat collapsed
            function updateToggleVisibility() {
                // Jika di mobile, biarkan tombol toggle terlihat (opsional).
                // Jika bukan mobile: sembunyikan toggle saat collapsed, tampilkan bila terbuka.
                if (isMobile()) {
                    toggleBtn.style.display = ''; // reset ke CSS default (visible)
                } else {
                    if (body.classList.contains('collapsed')) {
                        toggleBtn.style.display = 'none';
                    } else {
                        toggleBtn.style.display = ''; // tampilkan kembali saat open/hover
                    }
                }
            }

            // Init visibility
            updateToggleVisibility();

            // Saat mouse masuk sidebar: jika collapsed dan bukan mobile, buka sementara + tampilkan toggle
            sidebar.addEventListener('mouseenter', function () {
                if (body.classList.contains('collapsed') && !isMobile()) {
                    body.classList.remove('collapsed');
                    hoverOpened = true;
                    updateToggleVisibility();
                }
            });

            // Saat mouse keluar sidebar: jika dibuka oleh hover, kembalikan collapsed + sembunyikan toggle
            sidebar.addEventListener('mouseleave', function () {
                if (hoverOpened && !isMobile()) {
                    body.classList.add('collapsed');
                    hoverOpened = false;
                    updateToggleVisibility();
                }
            });

            // Mobile button handling (tidak diubah)
            function updateMobileButtons() {
                if (isMobile()) {
                    mobileMenuBtn.style.display = 'inline-flex';
                } else {
                    mobileMenuBtn.style.display = 'none';
                    body.classList.remove('sidebar-open');
                    overlay.style.display = 'none';
                }
                // setiap kali kalkulasi mobile, perbarui visibilitas toggle juga
                updateToggleVisibility();
            }

            updateMobileButtons();
            window.addEventListener('resize', updateMobileButtons);

            mobileMenuBtn.addEventListener('click', function () {
                body.classList.add('sidebar-open');
                overlay.style.display = 'block';
                overlay.setAttribute('aria-hidden', 'false');
            });

            overlay.addEventListener('click', function () {
                body.classList.remove('sidebar-open');
                overlay.style.display = 'none';
            });

            window.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && body.classList.contains('sidebar-open')) {
                    body.classList.remove('sidebar-open');
                    overlay.style.display = 'none';
                }
            });
        })();
    </script>
</body>

</html>