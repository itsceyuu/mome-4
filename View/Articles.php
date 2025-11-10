<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($activePage)) $activePage = 'articles';
if (!isset($pageTitle)) $pageTitle = 'Articles Finance';

$articles = $data['articles'] ?? [];
$search = $data['search'] ?? '';
$username = $data['username'] ?? 'User';
$userRole = $data['userRole'] ?? 'client';

$defaultImage = 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&auto=format&fit=crop';
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
        </aside>

        <main class="content" role="main">
            <div class="header">
                <div>
                    <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
                    <p class="page-subtitle">Learn simple, smart financial tips — updated every week.</p>
                </div>
                <div style="display:flex;gap:8px;align-items:center">
                    <button id="mobileMenuBtn" class="toggle" aria-label="Open sidebar on mobile" style="display:none">
                        <span class="iconify" data-icon="mdi:menu"></span>
                    </button>
                </div>
            </div>

            <section class="articles-section">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i>
                        <?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <i class="bi bi-exclamation-circle"></i>
                        <?= htmlspecialchars($_SESSION['error']) ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if ($userRole === 'admin'): ?>
                    <div class="admin-toolbar">
                        <a href="index.php?c=Articles&m=create" class="btn-admin-create">
                            <i class="bi bi-plus-circle"></i> Create New Article
                        </a>
                    </div>
                <?php endif; ?>

                <div class="search-container">
                    <form method="GET" action="index.php" class="search-form">
                        <input type="hidden" name="c" value="Articles">
                        <input type="hidden" name="m" value="index">
                        <div class="search-input-wrapper">
                            <i class="bi bi-search search-icon"></i>
                            <input
                                type="text"
                                name="search"
                                class="search-input"
                                placeholder="Search articles..."
                                value="<?= htmlspecialchars($search) ?>">
                            <?php if (!empty($search)): ?>
                                <a href="index.php?c=Articles&m=index" class="clear-search" title="Clear search">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="search-btn">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </form>
                </div>

                <?php if (!empty($search)): ?>
                    <p class="search-result-text">
                        Showing results for: <strong>"<?= htmlspecialchars($search) ?>"</strong>
                    </p>
                <?php endif; ?>

                <div class="articles-list">
                    <?php if (empty($articles)): ?>
                        <div class="empty-state">
                            <i class="bi bi-journal-x"></i>
                            <h3>No Articles Found</h3>
                            <p>
                                <?php if (!empty($search)): ?>
                                    No articles match your search. Try different keywords.
                                <?php else: ?>
                                    There are no articles available at the moment. Check back later!
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($search)): ?>
                                <a href="index.php?c=Articles&m=index" class="btn-primary">View All Articles</a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php foreach ($articles as $article):
                            $imageUrl = !empty($article['photo_path']) && file_exists($article['photo_path'])
                                ? $article['photo_path']
                                : $defaultImage;
                        ?>
                            <article class="article-card-horizontal">
                                <a href="index.php?c=Articles&m=detail&id=<?= $article['id'] ?>" class="article-thumbnail">
                                    <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
                                </a>

                                <div class="article-content-horizontal">
                                    <div class="article-body">
                                        <h2 class="article-title-horizontal">
                                            <?= htmlspecialchars($article['title']) ?>
                                        </h2>
                                        <p class="article-excerpt-horizontal">
                                            <?= htmlspecialchars(
                                                strlen($article['infoTambahan'] ?? '') > 180
                                                    ? substr($article['infoTambahan'], 0, 180) . '...'
                                                    : ($article['infoTambahan'] ?? 'Click to read more about this article.')
                                            ) ?>
                                        </p>
                                    </div>

                                    <div class="article-footer-horizontal">
                                        <div class="article-date-horizontal">
                                            <i class="bi bi-calendar3"></i>
                                            <?= date('M d, Y', strtotime($article['published_date'])) ?>
                                        </div>

                                        <div class="article-actions">
                                            <a href="index.php?c=Articles&m=detail&id=<?= $article['id'] ?>" class="btn-read-more">
                                                Read More <i class="bi bi-arrow-right"></i>
                                            </a>

                                            <?php if ($userRole === 'admin'): ?>
                                                <a href="index.php?c=Articles&m=edit&id=<?= $article['id'] ?>" class="btn-admin-edit" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <button onclick="confirmDelete(<?= $article['id'] ?>)" class="btn-admin-delete" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <form id="deleteForm" method="POST" action="index.php?c=Articles&m=delete" style="display:none;">
        <input type="hidden" name="id" id="deleteId">
    </form>

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
                if (isMobile()) {
                    mobileMenuBtn.style.display = 'inline-flex';
                } else {
                    mobileMenuBtn.style.display = 'none';
                }
            }

            updateUI();
            window.addEventListener('resize', updateUI);

            mobileMenuBtn.addEventListener('click', () => {
                body.classList.add('sidebar-open');
                overlay.style.display = 'block';
            });

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

        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this article? This action cannot be undone.')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }

        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>

</html>