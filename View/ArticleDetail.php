<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$article = $data['article'] ?? null;
$pageTitle = $article['title'] ?? 'Article Detail';
$activePage = 'articles';

if (!$article) {
    header("Location: index.php?c=Articles&m=index");
    exit;
}

$defaultImage = 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=1200&auto=format&fit=crop';
$imageUrl = !empty($article['photo_path']) && file_exists($article['photo_path'])
    ? $article['photo_path']
    : $defaultImage;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — Mome</title>

    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="View/Articles.css">
</head>

<body>
    <div id="overlay" class="overlay" style="display:none" aria-hidden="true"></div>

    <div class="app" id="app">
        <aside id="sidebar" class="sidebar" role="navigation" aria-label="Sidebar">
            <div style="display:flex;align-items:center;gap:8px">
                <div class="logo">
                    <img src="images/LOGO.png" alt="Mome Logo" />
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
                    $href = ($item['id'] === 'articles')
                        ? 'index.php?c=Articles&m=index'
                        : 'index.php?c=Dashboard&m=navigate&menu=' . urlencode($item['id']);
                ?>
                    <a href="<?= $href ?>" class="menu-item <?= $isActive ?>" role="menuitem">
                        <span class="iconify" data-icon="<?= $item['icon'] ?>" aria-hidden="true"></span>
                        <span class="label"><?= htmlspecialchars($item['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div style="margin-top: auto; padding-top: 12px; border-top: 1px solid #e6edf5;">
                <a href="index.php?c=Login&m=logout" class="menu-item logout-item" role="menuitem"
                    onclick="return confirm('Are you sure you want to logout?')">
                    <span class="iconify" data-icon="mdi:logout" aria-hidden="true"></span>
                    <span class="label">Logout</span>
                </a>
            </div>
        </aside>

        <main class="content" role="main">
            <div class="back-nav">
                <a href="index.php?c=Articles&m=index" class="back-btn">
                    <i class="bi bi-arrow-left"></i> Back to Articles
                </a>
            </div>

            <article class="article-detail">
                <div class="article-featured-image">
                    <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
                </div>

                <div class="article-meta">
                    <span class="article-date-detail">
                        <i class="bi bi-calendar3"></i>
                        Published on <?= date('F d, Y', strtotime($article['published_date'])) ?>
                    </span>
                </div>

                <h1 class="article-title-detail"><?= htmlspecialchars($article['title']) ?></h1>

                <?php if (!empty($article['infoTambahan'])): ?>
                    <p class="article-summary"><?= htmlspecialchars($article['infoTambahan']) ?></p>
                <?php endif; ?>

                <div class="article-divider"></div>

                <div class="article-content">
                    <?php if (!empty($article['content'])): ?>
                        <?= nl2br(htmlspecialchars($article['content'])) ?>
                    <?php else: ?>
                        <p class="text-muted">This article doesn't have detailed content yet.</p>
                    <?php endif; ?>
                </div>

                <div class="article-footer">
                    <a href="index.php?c=Articles&m=index" class="btn-primary">
                        <i class="bi bi-grid"></i> View More Articles
                    </a>
                </div>
            </article>
        </main>
    </div>

    <script>
        (function() {
            const body = document.body;
            const overlay = document.getElementById('overlay');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebar = document.getElementById('sidebar');

            function isMobile() {
                return window.matchMedia('(max-width: 860px)').matches;
            }

            function updateUI() {
                if (isMobile() && mobileMenuBtn) {
                    mobileMenuBtn.style.display = 'inline-flex';
                } else if (mobileMenuBtn) {
                    mobileMenuBtn.style.display = 'none';
                }
            }

            updateUI();
            window.addEventListener('resize', updateUI);

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', () => {
                    body.classList.add('sidebar-open');
                    overlay.style.display = 'block';
                });
            }

            overlay.addEventListener('click', () => {
                body.classList.remove('sidebar-open');
                overlay.style.display = 'none';
            });

            window.addEventListener('keydown', e => {
                if (e.key === 'Escape' && body.classList.contains('sidebar-open')) {
                    body.classList.remove('sidebar-open');
                    overlay.style.display = 'none';
                }
            });
        })();
    </script>
</body>

</html>