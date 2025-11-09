    <?php

if (session_status() === PHP_SESSION_NONE) {
session_start();
}

if (!isset($activePage))
    $activePage = 'recap';
if (!isset($pageTitle))
        $pageTitle = 'Mome Recap';

// Pastikan data dari controller ada
$transactions = $data['transactions'] ?? [];
$totalIncome = $data['totalIncome'] ?? 0;
$totalOutcome = $data['totalOutcome'] ?? 0;
$finalBalance = $data['finalBalance'] ?? 0;
$currentMonth = $data['currentMonth'] ?? date('n');
$monthName = $data['monthName'] ?? date('F');

$months = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];

// Konten utama
if (!isset($pageContent))
    $pageContent = '
        <section class="recap-section">
        <p class="recap-desc"> 
            Here\'s your monthly recap based on the transactions you\'ve made. 
            Take a look at your spending and see how well you\'ve managed your finances this month
        </p>
        <div class="content flex-grow-1 p-5">
        <div class="d-flex justify-content-end mb-3">
        <select class="form-select shadow-sm month-select" id="monthDropdown" onchange="filterByMonth()">';

    foreach ($months as $num => $name) {
        $selected = ($num == $currentMonth) ? 'selected' : '';
        $pageContent .= '<option value="' . $num . '" ' . $selected . '>' . $name . '</option>';
    }

    $pageContent .= '
        </select>
        </div>
       </div>';

if (empty($transactions)) {
    $pageContent .= '
        <!-- TABLE dengan empty state -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-header text-white">
                    <tr>
                        <th>DATE</th>
                        <th>TITLE</th>
                        <th>INCOME</th>
                        <th>OUTCOME</th>
                        <th>AMOUNT</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="text-muted py-4" style="font-style: italic;">No transactions available for this month.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- TOTAL BOXES dengan nilai 0 -->
        <div class="summary-cards-container mt-4">
            <div class="summary-card-new income-card">
                <div class="summary-icon-new">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="summary-content-new">
                    <h3 class="summary-amount-new">+ 0,00</h3>
                    <p class="summary-label-new">Your Income this ' . htmlspecialchars($monthName) . '</p>
                </div>
            </div>
            <div class="summary-card-new outcome-card">
                <div class="summary-icon-new">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="summary-content-new">
                    <h3 class="summary-amount-new">- 0,00</h3>
                    <p class="summary-label-new">Your Outcome This ' . htmlspecialchars($monthName) . '</p>
                </div>
            </div>
        </div>';

} else {
    // Ada transaksi, tampilkan tabel
    $pageContent .= '
        <!-- TABLE -->
        <div class="table-recap">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-header text-white">
                    <tr>
                        <th>DATE</th>
                        <th>TITLE</th>
                        <th>INCOME</th>
                        <th>OUTCOME</th>
                        <th>AMOUNT</th>
                    </tr>
                </thead>
                <tbody>';
    
    // Loop transaksi
    foreach ($transactions as $t) {
        $pageContent .= '
            <tr>
                <td>' . htmlspecialchars(date("j/n/Y", strtotime($t['date']))) . '</td>
                <td>' . htmlspecialchars($t['title']) . '</td>
                    <td class="text-success">
                        ' . ($t['income'] > 0 ? '+' . number_format($t['income'], 2, ',', '.') : '-') . '
                    </td>
                    <td class="text-danger">
                            ' . ($t['outcome'] > 0 ? '-' . number_format($t['outcome'], 2, ',', '.') : '-') . '
                    </td>
                    <td>Rp ' . number_format($t['balance'], 2, ',', '.') . '</td>
                </tr>';
    }

    // Final Balance Row
    $pageContent .= '
        <tr class="fw-bold text-end final-balance">
                            <td colspan="4" class="text-start ps-5 text-warning">Final Balance</td>
                            <td>Rp ' . number_format($finalBalance, 2, ',', '.') . '</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- TOTAL BOXES -->
            <div class="summary-cards-container mt-4">
                <div class="summary-card-new income-card">
                    <div class="summary-icon-new">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="summary-content-new">
                        <h3 class="summary-amount-new">+ ' . number_format($totalIncome, 2, ',', '.') . '</h3>
                        <p class="summary-label-new">Your Income This ' . htmlspecialchars($monthName) . '</p>
                    </div>
                </div>
                <div class="summary-card-new outcome-card">
                    <div class="summary-icon-new">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div class="summary-content-new">
                        <h3 class="summary-amount-new">- ' . number_format($totalOutcome, 2, ',', '.') . '</h3>
                        <p class="summary-label-new">Your Outcome This ' . htmlspecialchars($monthName) . '</p>
                    </div>
                </div>
            </div>';
    }

$pageContent .= '
    </section>

    <script>
    function filterByMonth() {
        const month = document.getElementById("monthDropdown").value;
        window.location.href = "index.php?c=RecapController&m=index&month=" + month;
    }
    </script>';

$BASE = '/mome-4';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — Mome</title>

    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="View/Recap.css">
</head>

<body>
    <!-- Optional overlay for mobile (inserted/controlled via JS) -->
    <div id="overlay" class="overlay" style="display:none" aria-hidden="true"></div>

    <div class="app" id="app">
        <aside id="sidebar" class="sidebar" role="navigation" aria-label="Sidebar">
            <div style="display:flex;align-items:center;gap:8px">
                <div class="logo" aria-hidden="false">
                    <img src="/Images/LOGO.png" alt="Mome Logo" />
                </div>

                <!-- Toggle (desktop) -->
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
                    // Gunakan routing yang sama seperti Dashboard.php
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
            <div class="header">
                <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>

                <!-- Mobile menu button -->
                <div style="display:flex;gap:8px;align-items:center">
                    <button id="mobileMenuBtn" class="toggle" aria-label="Open sidebar on mobile" style="display:none">
                        <span class="iconify" data-icon="mdi:menu"></span>
                    </button>
                </div>
            </div>

            <section>
                <?= $pageContent ?>
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