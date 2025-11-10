<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $data['pageTitle'] ?? 'Wishlist Form';
$activePage = 'wishlist';
$formAction = $data['formAction'] ?? 'create';
$item = $data['item'] ?? null;

// Build form action URL with ID for edit
$formActionUrl = 'index.php?c=Wishlist&m=' . $formAction;
if ($formAction === 'edit' && $item) {
    $formActionUrl .= '&id=' . $item['id'];
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
    <link rel="stylesheet" href="View/Wishlist.css">
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

                foreach ($menuItems as $item_menu):
                    $isActive = ($activePage === $item_menu['id']) ? 'active' : '';
                    $href = ($item_menu['id'] === 'wishlist')
                        ? 'index.php?c=Wishlist&m=index'
                        : (($item_menu['id'] === 'articles')
                            ? 'index.php?c=Articles&m=index'
                            : 'index.php?c=Dashboard&m=navigate&menu=' . urlencode($item_menu['id']));
                ?>
                    <a href="<?= $href ?>" class="menu-item <?= $isActive ?>" role="menuitem">
                        <span class="iconify" data-icon="<?= $item_menu['icon'] ?>" aria-hidden="true"></span>
                        <span class="label"><?= htmlspecialchars($item_menu['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

        </aside>

        <main class="content" role="main">
            <div class="back-nav">
                <a href="index.php?c=Wishlist&m=index" class="back-btn">
                    <i class="bi bi-arrow-left"></i> Back to Wishlist
                </a>
            </div>

            <div class="form-container">
                <div class="form-header">
                    <i class="bi bi-<?= $formAction === 'create' ? 'plus-circle' : 'pencil-square' ?>"></i>
                    <h1><?= htmlspecialchars($pageTitle) ?></h1>
                </div>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= $formActionUrl ?>" class="wishlist-form">
                    <?php if ($formAction === 'edit' && $item): ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="item_name">Item Name <span class="required">*</span></label>
                        <input
                            type="text"
                            id="item_name"
                            name="item_name"
                            class="form-control"
                            placeholder="Enter item name..."
                            value="<?= htmlspecialchars($item['item_name'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea
                            id="description"
                            name="description"
                            class="form-control"
                            rows="5"
                            placeholder="Add details about this item (optional)..."><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
                        <small class="form-hint">Tell us more about why you want this item or what it means to you</small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-<?= $formAction === 'create' ? 'check-circle' : 'save' ?>"></i>
                            <?= $formAction === 'create' ? 'Add to Wishlist' : 'Update Item' ?>
                        </button>
                        <a href="index.php?c=Wishlist&m=index" class="btn-cancel">
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