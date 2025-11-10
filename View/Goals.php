<?php
// Data sudah dikirim dari Controller via $data
$goals = $data['goals'] ?? [];
$goalMessage = $data['message'] ?? null;
$goalErrors = $data['errors'] ?? [];

// Default page info
if (!isset($activePage)) $activePage = 'goals';
if (!isset($pageTitle)) $pageTitle = 'MOME Goals';
if (!isset($pageContent1)) $pageContent1 = '<p>Your savings journey continues!</p>';
if (!isset($pageContent2)) $pageContent2 = '<p>Review your progress toward your target this month and stay consistent — every step brings you closer to your goal.</p>';

$BASE = '/mome-4';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — Mome</title>

    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <link rel="stylesheet" href="View/Goals.css">

    <style>
        .goals-section {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 20px;
        }

        .goal-card {
            width: 100%;
            background: #fff;
            border-radius: 20px;
            border: 1px solid rgba(15, 23, 42, 0.06);
            padding: 18px 22px;
            display: flex;
            align-items: center;
            gap: 18px;
            box-shadow: 0 4px 10px rgba(16, 24, 40, 0.03);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .goal-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 24, 40, 0.06);
        }

        .goal-card:focus {
            outline: 3px solid rgba(37, 99, 235, 0.12);
            outline-offset: 2px;
        }

        .goal-card__left {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
            min-width: 0;
        }

        .goal-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, rgba(37, 99, 235, 0.12), rgba(37, 99, 235, 0.08));
            flex-shrink: 0;
        }

        .goal-icon .iconify {
            font-size: 24px;
            color: #2563eb;
        }

        .goal-meta {
            min-width: 0;
            flex: 1 1 auto;
            overflow: hidden;
        }

        .goal-meta h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        .goal-progress {
            margin-top: 6px;
            color: #64748b;
            font-size: 13px;
        }

        .goal-current {
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .goal-current .label {
            color: #94a3b8;
            font-size: 13px;
            min-width: 120px;
        }

        .dots {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .dots span {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #e6eefb;
        }

        .dots span.filled {
            background: #2563eb;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.18);
        }

        .goal-target {
            text-align: right;
            min-width: 160px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            align-items: flex-end;
            margin-top: 30px;
        }

        .goal-target .label {
            color: #94a3b8;
            font-size: 13px;
        }

        .goal-target .amount {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }

        .goal-card__close {
            position: absolute;
            top: 12px;
            right: 12px;
            background: transparent;
            border: none;
            padding: 4px;
            cursor: pointer;
            transition: all .12s ease;
            z-index: 10;
        }

        .goal-card__close .iconify {
            font-size: 20px;
            color: #94a3b8;
        }

        .goal-card__close:hover .iconify {
            color: #ef4444;
            transform: scale(1.1);
        }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.4);
        }

        .modal[aria-hidden="false"] {
            display: flex;
        }

        .modal__panel {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 12px 40px rgba(2, 6, 23, 0.15);
            max-width: 500px;
            width: 92%;
        }

        .modal__title {
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 16px;
            color: #0f172a;
        }

        .modal__actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 18px;
        }

        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            border: 0;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.15s ease;
        }

        .btn--ghost {
            background: transparent;
            border: 1px solid #e5e7eb;
            color: #374151;
        }

        .btn--ghost:hover {
            background: #f9fafb;
        }

        .btn--primary {
            background: #2563eb;
            color: #fff;
        }

        .btn--primary:hover {
            background: #1d4ed8;
        }

        .btn--danger {
            background: #ef4444;
            color: #fff;
        }

        .btn--danger:hover {
            background: #dc2626;
        }

        .field {
            margin-bottom: 14px;
        }

        .field label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .field input,
        .field textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
        }

        .field input:focus,
        .field textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .message.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .message.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .errors {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 16px;
        }

        .errors ul {
            margin: 0;
            padding-left: 20px;
            color: #991b1b;
            font-size: 14px;
        }

        .floating-btn {
            position: fixed;
            bottom: 32px;
            right: 32px;
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .floating-btn:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 12px 32px rgba(37, 99, 235, 0.5);
        }

        .floating-btn .iconify {
            font-size: 32px;
            color: #fff;
        }

        @media (max-width: 860px) {
            .goal-card {
                flex-direction: column;
                align-items: flex-start;
                padding: 16px;
            }

            .goal-card__left {
                width: 100%;
            }

            .goal-target {
                align-items: flex-start;
                margin-top: 12px;
            }

            .floating-btn {
                width: 56px;
                height: 56px;
                bottom: 24px;
                right: 24px;
            }

            .floating-btn .iconify {
                font-size: 28px;
            }
        }
    </style>
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

                    // Tentukan href untuk setiap item berdasarkan ID
                    if ($item['id'] === 'recap') {
                        $href = 'index.php?c=RecapController&m=index';
                    } elseif ($item['id'] === 'goals') {
                        $href = 'index.php?c=GoalsController&m=index';
                    } else {
                        $href = 'index.php?c=Dashboard&m=navigate&menu=' . urlencode($item['id']);
                    }
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
            </section>

            <?php if (!empty($goalErrors)): ?>
                <div class="errors" role="alert">
                    <ul>
                        <?php foreach ($goalErrors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($goalMessage)): ?>
                <div class="message success" role="status">
                    <?= htmlspecialchars($goalMessage) ?>
                </div>
            <?php endif; ?>

            <!-- Goals Card List -->
            <section class="goals-section" aria-label="Goals cards">
                <?php if (empty($goals)): ?>
                    <div style="text-align:center; padding:40px; color:#94a3b8;">
                        <span class="iconify" data-icon="mdi:target" style="font-size:48px; margin-bottom:12px; display:block;"></span>
                        <p style="font-size:16px; font-weight:600;">Belum ada goals yang dibuat</p>
                        <p style="font-size:14px;">Klik tombol + di bawah untuk membuat goal pertama Anda!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($goals as $g):
                        $percent = 0;
                        if (!empty($g['target_amount']) && $g['target_amount'] > 0) {
                            $percent = round(($g['current_amount'] / $g['target_amount']) * 100);
                            if ($percent > 100) $percent = 100;
                        }

                        $filledDots = min(6, max(0, (int) round($percent / (100 / 6))));
                        $emptyDots = 6 - $filledDots;
                        $safeTitle = htmlspecialchars($g['target_name']);
                        $detailUrl = 'index.php?c=GoalsController&m=detail&id=' . urlencode($g['id']);
                    ?>
                        <article class="goal-card" role="region" aria-labelledby="goal-<?= $g['id'] ?>" tabindex="0"
                            data-detail-url="<?= htmlspecialchars($detailUrl) ?>">

                            <form method="post" action="index.php?c=GoalsController&m=destroy" style="display:inline;">
                                <input type="hidden" name="goal_id" value="<?= htmlspecialchars($g['id']) ?>">
                                <button type="submit" class="goal-card__close" aria-label="Delete goal"
                                    onclick="return confirm('Yakin ingin menghapus goal ini?')">
                                    <span class="iconify" data-icon="mdi:close" aria-hidden="true"></span>
                                </button>
                            </form>

                            <div class="goal-card__left" aria-hidden="false">
                                <div class="goal-icon" aria-hidden="true">
                                    <span class="iconify" data-icon="mdi:flag" data-inline="false"></span>
                                </div>

                                <div class="goal-meta">
                                    <h3 id="goal-<?= $g['id'] ?>"><?= $safeTitle ?></h3>
                                    <div class="goal-progress">Progress <?= $percent ?>%</div>

                                    <div class="goal-current">
                                        <div class="label">Current Savings</div>
                                        <div class="dots" aria-hidden="true">
                                            <?php for ($i = 0; $i < $filledDots; $i++): ?><span class="filled"></span><?php endfor; ?>
                                            <?php for ($i = 0; $i < $emptyDots; $i++): ?><span></span><?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="goal-target">
                                <div class="label">Target Amount</div>
                                <div class="amount">Rp <?= number_format($g['target_amount'], 0, ',', '.') ?></div>
                                <div class="dots" aria-hidden="true" style="margin-top:6px;">
                                    <?php for ($i = 0; $i < $filledDots; $i++): ?><span class="filled"></span><?php endfor; ?>
                                    <?php for ($i = 0; $i < $emptyDots; $i++): ?><span></span><?php endfor; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <!-- Floating Add Button -->
    <button class="floating-btn" id="openAddGoal" aria-label="Add new goal">
        <span class="iconify" data-icon="mdi:plus"></span>
    </button>

    <!-- Modal: Add Goal -->
    <div class="modal" id="goalModal" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="modal__panel" role="document">
            <div class="modal__title">Buat Goal Baru</div>
            <form method="post" action="index.php?c=GoalsController&m=store" id="addGoalForm">
                <div class="field">
                    <label for="title">Judul Goal *</label>
                    <input id="title" name="title" type="text" placeholder="e.g. Rumah Impian" required>
                </div>

                <div style="display:grid; grid-template-columns:2fr 1fr; gap:12px;">
                    <div class="field">
                        <label for="target_amount">Target Amount *</label>
                        <input id="target_amount" name="target_amount" type="text" placeholder="100000000" required>
                    </div>
                    <div class="field">
                        <label for="current_amount">Tabungan Awal</label>
                        <input id="current_amount" name="current_amount" type="text" placeholder="0">
                    </div>
                </div>

                <div class="field">
                    <label for="deadline">Target Deadline (opsional)</label>
                    <input id="deadline" name="deadline" type="date">
                </div>

                <div class="field">
                    <label for="description">Deskripsi (opsional)</label>
                    <textarea id="description" name="description" rows="3" placeholder="Catatan tentang goal ini..."></textarea>
                </div>

                <div class="modal__actions">
                    <button type="button" class="btn btn--ghost" id="cancelModal">Batal</button>
                    <button type="submit" class="btn btn--primary">Simpan Goal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            const body = document.body;
            const toggleBtn = document.getElementById('toggleBtn');
            const overlay = document.getElementById('overlay');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebar = document.getElementById('sidebar');
            let hoverOpened = false;

            if (!body.classList.contains('collapsed')) {
                body.classList.add('collapsed');
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
            }

            function isMobile() {
                return window.matchMedia('(max-width: 860px)').matches;
            }

            function updateToggleVisibility() {
                if (!toggleBtn) return;
                if (isMobile()) {
                    toggleBtn.style.display = '';
                } else {
                    toggleBtn.style.display = body.classList.contains('collapsed') ? 'none' : '';
                }
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

            function updateMobileButtons() {
                if (!mobileMenuBtn) return;
                if (isMobile()) {
                    mobileMenuBtn.style.display = 'inline-flex';
                } else {
                    mobileMenuBtn.style.display = 'none';
                    body.classList.remove('sidebar-open');
                    overlay.style.display = 'none';
                }
                updateToggleVisibility();
            }

            updateMobileButtons();
            window.addEventListener('resize', updateMobileButtons);

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    body.classList.add('sidebar-open');
                    overlay.style.display = 'block';
                    overlay.setAttribute('aria-hidden', 'false');
                });
            }

            overlay.addEventListener('click', function() {
                body.classList.remove('sidebar-open');
                overlay.style.display = 'none';
                closeAddModal();
            });

            window.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (body.classList.contains('sidebar-open')) {
                        body.classList.remove('sidebar-open');
                        overlay.style.display = 'none';
                    }
                    closeAddModal();
                }
            });

            // Modal handlers
            const openBtn = document.getElementById('openAddGoal');
            const goalModal = document.getElementById('goalModal');
            const cancelModal = document.getElementById('cancelModal');

            function openAddModal() {
                if (!goalModal) return;
                goalModal.setAttribute('aria-hidden', 'false');
                overlay.style.display = 'block';
                const first = goalModal.querySelector('input');
                if (first) first.focus();
            }

            function closeAddModal() {
                if (!goalModal) return;
                goalModal.setAttribute('aria-hidden', 'true');
                overlay.style.display = 'none';
            }

            if (openBtn) openBtn.addEventListener('click', function(e) {
                e.preventDefault();
                openAddModal();
            });

            if (cancelModal) cancelModal.addEventListener('click', closeAddModal);

            // Card click navigation (kecuali tombol delete)
            document.addEventListener('click', function(e) {
                if (e.target.closest && e.target.closest('.goal-card__close')) return;

                const card = e.target.closest && e.target.closest('.goal-card');
                if (card) {
                    const url = card.getAttribute('data-detail-url');
                    if (url) window.location.href = url;
                }
            });

            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    const active = document.activeElement;
                    if (active && active.classList && active.classList.contains('goal-card')) {
                        const url = active.getAttribute('data-detail-url');
                        if (url) window.location.href = url;
                    }
                }
            });
        })();
    </script>
</body>

</html>