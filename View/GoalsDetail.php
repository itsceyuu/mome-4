<?php
// Data dari Controller
$goal = $data['goal'] ?? null;
$progress = $data['progress'] ?? 0;
$remaining = $data['remaining'] ?? 0;
$message = $data['message'] ?? null;
$errors = $data['errors'] ?? [];

if (!$goal) {
    die('<p style="color:#dc2626;">Goal tidak ditemukan.</p>');
}

$activePage = 'goals';
$pageTitle = htmlspecialchars($goal['target_name']);
$BASE = '/mome-4';

// Format tanggal
$createdDate = date('d M Y', strtotime($goal['created_at']));
$deadlineFormatted = !empty($goal['deadline']) ? date('d M Y', strtotime($goal['deadline'])) : '-';

// Calculate days remaining
$daysRemaining = null;
if (!empty($goal['deadline'])) {
    $today = new DateTime();
    $deadline = new DateTime($goal['deadline']);
    $interval = $today->diff($deadline);
    $daysRemaining = $interval->days;
    if ($today > $deadline) {
        $daysRemaining = -$daysRemaining; // negatif jika sudah lewat
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> — Mome</title>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <link rel="stylesheet" href="View/GoalsDetail.css">
</head>

<body>
    <div class="overlay" id="overlay" style="display:none" aria-hidden="true"></div>

    <div class="app" id="app">
        <aside id="sidebar" class="sidebar" role="navigation" aria-label="Sidebar">
            <div style="display:flex;align-items:center;gap:8px">
                <div class="logo" aria-hidden="false">
                    <img src="<?= $BASE ?>/Images/LOGO.png" alt="Mome Logo">
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
                    ['id' => 'articles', 'label' => 'Articles Finance', 'icon' => 'mdi:file-document-outline'],
                ];

                foreach ($menuItems as $item):
                    $isActive = ($activePage === $item['id']) ? 'active' : '';
                    $href = ($item['id'] === 'recap')
                        ? 'index.php?c=RecapController&m=index'
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
            <div class="detail-container">
                <a href="index.php?c=GoalsController&m=index" class="back-link">
                    <span class="iconify" data-icon="mdi:arrow-left"></span>
                    Kembali ke Goals
                </a>

                <?php if (!empty($errors)): ?>
                    <div class="errors" role="alert">
                        <ul>
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($message)): ?>
                    <div class="message success" role="status">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <div class="detail-card">
                    <div class="detail-header">
                        <div class="detail-icon">
                            <span class="iconify" data-icon="mdi:flag"></span>
                        </div>
                        <div class="detail-title">
                            <h1><?= htmlspecialchars($goal['target_name']) ?></h1>
                            <div class="detail-subtitle">
                                Dibuat pada <?= $createdDate ?>
                                <?php if ($daysRemaining !== null): ?>
                                    •
                                    <?php if ($daysRemaining > 0): ?>
                                        <span class="days-badge <?= $daysRemaining < 30 ? 'danger' : ($daysRemaining < 90 ? 'warning' : 'success') ?>">
                                            <span class="iconify" data-icon="mdi:clock-outline" style="font-size:14px;"></span>
                                            <?= $daysRemaining ?> hari lagi
                                        </span>
                                    <?php elseif ($daysRemaining === 0): ?>
                                        <span class="days-badge danger">
                                            <span class="iconify" data-icon="mdi:alert" style="font-size:14px;"></span>
                                            Deadline hari ini!
                                        </span>
                                    <?php else: ?>
                                        <span class="days-badge danger">
                                            <span class="iconify" data-icon="mdi:alert" style="font-size:14px;"></span>
                                            <?= abs($daysRemaining) ?> hari terlambat
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="progress-section">
                        <div class="progress-header">
                            <div>
                                <div class="progress-percent"><?= $progress ?>%</div>
                                <div class="progress-label">Progress Tercapai</div>
                            </div>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width:<?= $progress ?>%"></div>
                        </div>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-label">Target Amount</div>
                            <div class="stat-value">Rp <?= number_format($goal['target_amount'], 0, ',', '.') ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Tabungan Saat Ini</div>
                            <div class="stat-value">Rp <?= number_format($goal['current_amount'], 0, ',', '.') ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Kekurangan</div>
                            <div class="stat-value">Rp <?= number_format($remaining, 0, ',', '.') ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Target Deadline</div>
                            <div class="stat-value"><?= $deadlineFormatted ?></div>
                        </div>
                    </div>

                    <div class="action-section">
                        <button class="btn btn--primary" id="openAddProgress">
                            <span class="iconify" data-icon="mdi:plus-circle"></span>
                            Tambah Tabungan
                        </button>
                        <button class="btn btn--ghost" id="openEditGoal">
                            <span class="iconify" data-icon="mdi:pencil"></span>
                            Edit Goal
                        </button>
                    </div>

                    <?php if (!empty($goal['description'])): ?>
                        <div class="description-section">
                            <h3>Deskripsi</h3>
                            <p><?= nl2br(htmlspecialchars($goal['description'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal: Tambah Progress -->
    <div class="modal" id="progressModal" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="modal__panel">
            <div class="modal__title">Tambah Tabungan</div>
            <form method="post" action="index.php?c=GoalsController&m=updateProgress">
                <input type="hidden" name="goal_id" value="<?= htmlspecialchars($goal['id']) ?>">
                <div class="field">
                    <label for="amount_to_add">Jumlah yang Ditambahkan *</label>
                    <input id="amount_to_add" name="amount_to_add" type="text" placeholder="500000" required>
                    <small style="color:#64748b; display:block; margin-top:6px;">Masukkan jumlah uang yang ingin ditambahkan ke tabungan</small>
                </div>
                <div class="modal__actions">
                    <button type="button" class="btn btn--ghost" id="cancelProgress">Batal</button>
                    <button type="submit" class="btn btn--secondary">Tambahkan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Goal -->
    <div class="modal" id="editModal" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="modal__panel">
            <div class="modal__title">Edit Goal</div>
            <form method="post" action="index.php?c=GoalsController&m=update">
                <input type="hidden" name="goal_id" value="<?= htmlspecialchars($goal['id']) ?>">
                <div class="field">
                    <label for="edit_title">Judul Goal *</label>
                    <input id="edit_title" name="title" type="text" value="<?= htmlspecialchars($goal['target_name']) ?>" required>
                </div>
                <div style="display:grid; grid-template-columns:2fr 1fr; gap:12px;">
                    <div class="field">
                        <label for="edit_target">Target Amount *</label>
                        <input id="edit_target" name="target_amount" type="text" value="<?= htmlspecialchars($goal['target_amount']) ?>" required>
                    </div>
                    <div class="field">
                        <label for="edit_current">Tabungan</label>
                        <input id="edit_current" name="current_amount" type="text" value="<?= htmlspecialchars($goal['current_amount']) ?>">
                    </div>
                </div>
                <div class="field">
                    <label for="edit_deadline">Deadline</label>
                    <input id="edit_deadline" name="deadline" type="date" value="<?= htmlspecialchars($goal['deadline']) ?>">
                </div>
                <div class="field">
                    <label for="edit_description">Deskripsi</label>
                    <textarea id="edit_description" name="description" rows="3" style="width:100%; padding:12px; border:1px solid #d1d5db; border-radius:10px; resize:vertical;"><?= htmlspecialchars($goal['description']) ?></textarea>
                </div>
                <div class="modal__actions">
                    <button type="button" class="btn btn--ghost" id="cancelEdit">Batal</button>
                    <button type="submit" class="btn btn--primary">Update Goal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            const body = document.body;
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleBtn');
            const overlay = document.getElementById('overlay');
            let hoverOpened = false;

            if (!body.classList.contains('collapsed')) body.classList.add('collapsed');

            function isMobile() {
                return window.matchMedia('(max-width: 860px)').matches;
            }

            function updateToggleVisibility() {
                if (!toggleBtn) return;
                toggleBtn.style.display = isMobile() ? '' : (body.classList.contains('collapsed') ? 'none' : '');
            }
            updateToggleVisibility();

            if (sidebar) {
                sidebar.addEventListener('mouseenter', function() {
                    if (body.classList.contains('collapsed') && !isMobile()) {
                        body.classList.remove('collapsed');
                        hoverOpened = true;
                        updateToggleVisibility();
                    }
                });
                sidebar.addEventListener('mouseleave', function() {
                    if (hoverOpened && !isMobile()) {
                        body.classList.add('collapsed');
                        hoverOpened = false;
                        updateToggleVisibility();
                    }
                });
            }

            // Modal handlers
            const progressModal = document.getElementById('progressModal');
            const editModal = document.getElementById('editModal');
            const openProgress = document.getElementById('openAddProgress');
            const openEdit = document.getElementById('openEditGoal');
            const cancelProgress = document.getElementById('cancelProgress');
            const cancelEdit = document.getElementById('cancelEdit');

            function openModal(modal) {
                if (!modal) return;
                modal.setAttribute('aria-hidden', 'false');
                overlay.style.display = 'block';
            }

            function closeModal(modal) {
                if (!modal) return;
                modal.setAttribute('aria-hidden', 'true');
                overlay.style.display = 'none';
            }

            if (openProgress) openProgress.addEventListener('click', () => openModal(progressModal));
            if (openEdit) openEdit.addEventListener('click', () => openModal(editModal));
            if (cancelProgress) cancelProgress.addEventListener('click', () => closeModal(progressModal));
            if (cancelEdit) cancelEdit.addEventListener('click', () => closeModal(editModal));

            overlay.addEventListener('click', function() {
                closeModal(progressModal);
                closeModal(editModal);
            });

            window.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal(progressModal);
                    closeModal(editModal);
                }
            });
        })();
    </script>
</body>

</html>