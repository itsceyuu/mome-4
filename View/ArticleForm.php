<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $data['pageTitle'] ?? 'Article Form';
$activePage = 'articles';
$formAction = $data['formAction'] ?? 'create';
$article = $data['article'] ?? null;
$userRole = $data['userRole'] ?? 'client';

if ($userRole !== 'admin') {
    header("Location: index.php?c=Articles&m=index");
    exit;
}
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

            <div class="form-container">
                <div class="form-header">
                    <i class="bi bi-<?= $formAction === 'create' ? 'plus-circle' : 'pencil-square' ?>"></i>
                    <h1><?= htmlspecialchars($pageTitle) ?></h1>
                </div>

                <form method="POST" action="index.php?c=Articles&m=<?= $formAction ?>" class="article-form" enctype="multipart/form-data">
                    <?php if ($formAction === 'edit' && $article): ?>
                        <input type="hidden" name="id" value="<?= $article['id'] ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="title">Article Title <span class="required">*</span></label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="form-control"
                            placeholder="Enter article title..."
                            value="<?= htmlspecialchars($article['title'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="infoTambahan">Short Description / Summary</label>
                        <textarea
                            id="infoTambahan"
                            name="infoTambahan"
                            class="form-control"
                            rows="3"
                            placeholder="Brief summary that appears in the article list..."><?= htmlspecialchars($article['infoTambahan'] ?? '') ?></textarea>
                        <small class="form-hint">This will be shown as excerpt in article cards (recommended 150-200 characters)</small>
                    </div>

                    <div class="form-group">
                        <label for="content">Full Article Content</label>
                        <textarea
                            id="content"
                            name="content"
                            class="form-control"
                            rows="15"
                            placeholder="Write the full article content here..."><?= htmlspecialchars($article['content'] ?? '') ?></textarea>
                        <small class="form-hint">Write the complete article content. Line breaks will be preserved.</small>
                    </div>

                    <div class="form-group">
                        <label for="photo">Article Photo</label>
                        <input
                            type="file"
                            id="photo"
                            name="photo"
                            class="form-control file-input"
                            accept="image/jpeg,image/png,image/jpg,image/webp">
                        <small class="form-hint">Upload an image for the article (JPG, PNG, WEBP - Max 5MB)</small>

                        <?php if ($formAction === 'edit' && $article && !empty($article['photo_path']) && file_exists($article['photo_path'])): ?>
                            <div class="current-photo">
                                <strong>Current Photo:</strong>
                                <img src="<?= htmlspecialchars($article['photo_path']) ?>" alt="Current article photo">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-<?= $formAction === 'create' ? 'check-circle' : 'save' ?>"></i>
                            <?= $formAction === 'create' ? 'Create Article' : 'Update Article' ?>
                        </button>
                        <a href="index.php?c=Articles&m=index" class="btn-cancel">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
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