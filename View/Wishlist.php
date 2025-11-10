<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($activePage)) $activePage = 'wishlist';
if (!isset($pageTitle)) $pageTitle = 'Wishlist';

$wishlistItems = $data['wishlistItems'] ?? [];
$username = $data['username'] ?? 'User';
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

                foreach ($menuItems as $item):
                    $isActive = ($activePage === $item['id']) ? 'active' : '';
                    $href = ($item['id'] === 'wishlist')
                        ? 'index.php?c=Wishlist&m=index'
                        : (($item['id'] === 'articles')
                            ? 'index.php?c=Articles&m=index'
                            : 'index.php?c=Dashboard&m=navigate&menu=' . urlencode($item['id']));
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
                    <p class="page-subtitle">Track your dream items and savings goals</p>
                </div>
                <div style="display:flex;gap:8px;align-items:center">
                    <button id="mobileMenuBtn" class="toggle" aria-label="Open sidebar on mobile" style="display:none">
                        <span class="iconify" data-icon="mdi:menu"></span>
                    </button>
                </div>
            </div>

            <section class="wishlist-section">
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

                <div class="action-toolbar">
                    <a href="index.php?c=Wishlist&m=create" class="btn-add">
                        <i class="bi bi-plus-circle"></i> Add New Item
                    </a>
                </div>

                <div class="table-container">
                    <?php if (empty($wishlistItems)): ?>
                        <div class="empty-state">
                            <i class="bi bi-heart"></i>
                            <h3>Your Wishlist is Empty</h3>
                            <p>Start adding items you dream of owning or goals you want to achieve!</p>
                            <a href="index.php?c=Wishlist&m=create" class="btn-primary">
                                <i class="bi bi-plus-circle"></i> Add Your First Item
                            </a>
                        </div>
                    <?php else: ?>
                        <table class="wishlist-table">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Item Name</th>
                                    <th>Description</th>
                                    <th>Date Added</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($wishlistItems as $index => $item): ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td class="font-semibold"><?= htmlspecialchars($item['item_name']) ?></td>
                                        <td class="text-description">
                                            <?php if (!empty($item['description'])): ?>
                                                <?php
                                                $description = htmlspecialchars($item['description']);
                                                $maxLength = 150; // Max characters before collapse
                                                $needsCollapse = strlen($description) > $maxLength;
                                                ?>

                                                <?php if ($needsCollapse): ?>
                                                    <span class="description-wrapper" data-full="<?= $description ?>">
                                                        <span class="description-content collapsed">
                                                            <?= $description ?>
                                                        </span>
                                                        <button type="button" class="description-toggle" onclick="toggleDescription(this)">
                                                            <span class="toggle-text">Read more</span>
                                                            <i class="bi bi-chevron-down"></i>
                                                        </button>
                                                    </span>
                                                <?php else: ?>
                                                    <?= $description ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">No description</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-date"><?= date('M d, Y', strtotime($item['date_added'])) ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="index.php?c=Wishlist&m=edit&id=<?= $item['id'] ?>" class="btn-action btn-edit" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button onclick="confirmDelete(<?= $item['id'] ?>)" class="btn-action btn-delete" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <form id="deleteForm" method="POST" action="index.php?c=Wishlist&m=delete" style="display:none;">
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

        function toggleDescription(button) {
            const wrapper = button.closest('.description-wrapper');
            const content = wrapper.querySelector('.description-content');
            const toggleText = button.querySelector('.toggle-text');
            const icon = button.querySelector('i');

            if (content.classList.contains('collapsed')) {
                content.classList.remove('collapsed');
                toggleText.textContent = 'Read less';
                icon.classList.remove('bi-chevron-down');
                icon.classList.add('bi-chevron-up');
            } else {
                content.classList.add('collapsed');
                toggleText.textContent = 'Read more';
                icon.classList.remove('bi-chevron-up');
                icon.classList.add('bi-chevron-down');
            }
        }

        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
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