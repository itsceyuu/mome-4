<?php
// Session sudah di-start di Controller
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Data dari controller (dikirim via method view())
$transactions = $data['transaksiList'] ?? [];
$summary = $data['summary'] ?? ['total_income_today' => 0];
$totalIncomeToday = number_format($summary['total_income_today'], 2, ',', '.');

// Filter dari controller
$filterSort = $data['filterSort'] ?? 'newest';
$filterType = $data['filterType'] ?? ''; // kosong = tampilkan semua


if (!isset($activePage))
    $activePage = 'expenses';
if (!isset($activeTitle))
    $pageTitle = 'Track Your Expenses';
if (!isset($pageContent1))
    $pageContent1 = '
<section class="expense-section">
    <p class="expense-desc">
        In this page, you can now easily track where your money goes!
        You can also change the information you\'ve put later and add some description into it
    </p>

    <!-- Balance Card -->
    <div class="balance-card">
        <div class="balance-left">
            <span class="iconify" data-icon="mdi:wallet-outline"></span>
            <div>
                <h2>Rp. ' . $totalIncomeToday . '</h2>
                <p>Your Income Today</p>
            </div>
        </div>
        <div class="balance-right">
            <select id="sortFilter" onchange="filterTransactions()">
                <option value="newest"' . ($filterSort === 'newest' ? ' selected' : '') . '>Newest</option>
                <option value="oldest"' . ($filterSort === 'oldest' ? ' selected' : '') . '>Oldest</option>
            </select>
            <select id="typeFilter" onchange="filterTransactions()">
                <option value="all"' . ($filterType === 'all' ? ' selected' : '') . '>All</option>
                <option value="income"' . ($filterType === 'income' ? ' selected' : '') . '>Income</option>
                <option value="expense"' . ($filterType === 'expense' ? ' selected' : '') . '>Expense</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <table class="expense-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>';

// Loop transaksi dari database
if (empty($transactions)) {
    $pageContent1 .= '
            <tr>
                <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">
                    <span class="iconify" data-icon="mdi:inbox" style="font-size: 48px; display: block; margin-bottom: 12px;"></span>
                    No transactions yet. Start tracking your expenses!
                </td>
            </tr>';
} else {
    foreach ($transactions as $transaction) {
        $amountClass = $transaction['type'] === 'income' ? 'amount-income' : 'amount-expense';
        $amountSign = $transaction['type'] === 'income' ? '+' : '-';
        $formattedAmount = number_format($transaction['amount'], 2, ',', '.');
        $formattedDate = date('d/m/Y', strtotime($transaction['date']));
        $description = $transaction['description'] ?: '-';
        
        // Escape untuk JavaScript
        $titleJS = htmlspecialchars($transaction['title'], ENT_QUOTES);
        $descJS = htmlspecialchars($transaction['description'] ?? '', ENT_QUOTES);
        
        $pageContent1 .= '
            <tr>
                <td>' . htmlspecialchars($transaction['title']) . '</td>
                <td>' . $formattedDate . '</td>
                <td class="' . $amountClass . '">' . $amountSign . $formattedAmount . '</td>
                <td>' . htmlspecialchars($description) . '</td>
                <td>
                    <button class="edit-btn" onclick="openEditModal(' . $transaction['id'] . ', \'' . $titleJS . '\', \'' . $transaction['date'] . '\', ' . $transaction['amount'] . ', \'' . $transaction['type'] . '\', \'' . $descJS . '\')">
                        Edit
                    </button>
                    <button class="delete-btn" onclick="openDeleteModal(' . $transaction['id'] . ', \'' . $titleJS . '\')">
                        Delete
                    </button>
                </td>
            </tr>';
    }
}

$pageContent1 .= '
        </tbody>
    </table>
</section>';

if (!isset($pageContent2))
    $pageContent2 = '
<!-- Info Section -->
<div class="info-section">
    <div class="info-box">
        <span class="iconify" data-icon="mdi:information-outline"></span>
        <div>
            <strong>Important</strong>
            <p>In order to give you detailed about your monthly financial expenses,
            you can also now look at your monthly recaps! Go seek it now!
            <a href="?page=recap">Take me There!</a></p>
        </div>
    </div>
</div>
';
if (!isset($pageContent3))
    $pageContent3 = '
<!-- FAB Button -->
<button class="add-btn" aria-label="Add new expense" onclick="openAddModal()">
    <span class="iconify" data-icon="mdi:plus"></span>
</button>

<!-- ========== MODAL ADD/EDIT ========== -->
<div class="modal-overlay" id="modalForm">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add Expense</h3>
            <button class="modal-close" onclick="closeModal(\'modalForm\')">
                <span class="iconify" data-icon="mdi:close"></span>
            </button>
        </div>
        <form id="transactionForm" action="" method="POST">
            <input type="hidden" name="transaction_id" id="transaction_id">
            <div class="modal-body">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" placeholder="MyTitle" required>
                    <div class="supporting-text">Supporting text</div>
                </div>

                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" required>
                    <div class="supporting-text">Supporting date/date</div>
                </div>

                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" id="amount" name="amount" placeholder="ex : 200.000,00" step="0.01" required>
                    <div class="supporting-text">Supporting number</div>
                </div>

                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Enter description here"></textarea>
                    <div class="supporting-text">Supporting text</div>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeModal(\'modalForm\')">Cancel</button>
                <button type="submit" class="btn-confirm">Confirm</button>
            </div>
        </form>
    </div>
</div>

<!-- ========== MODAL DELETE ========== -->
<div class="modal-overlay" id="modalDelete">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3>Delete Transaction</h3>
            <button class="modal-close" onclick="closeModal(\'modalDelete\')">
                <span class="iconify" data-icon="mdi:close"></span>
            </button>
        </div>
        <div class="modal-body">
            <p style="color: #475569; margin: 0;">Are you sure you want to delete "<strong id="deleteTransactionName"></strong>"? This action cannot be undone.</p>
        </div>
        <form action="index.php?c=ExpenseController&m=hapus" method="POST">
            <input type="hidden" name="transaction_id" id="delete_transaction_id">
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeModal(\'modalDelete\')">Cancel</button>
                <button type="submit" class="btn-confirm delete">Delete</button>
            </div>
        </form>
    </div>
</div>
<script>
// Filter transactions
function filterTransactions() {
    const sort = document.getElementById(\'sortFilter\').value;
    const type = document.getElementById(\'typeFilter\').value;
    const typeParam = type === \'all\' ? \'\' : `&type=${type}`;
    window.location.href = `index.php?c=ExpenseController&m=index&sort=${sort}${typeParam}`;
}

// Open Add Modal
function openAddModal() {
    document.getElementById(\'modalTitle\').textContent = \'Add Expense\';
    document.getElementById(\'transaction_id\').value = \'\';
    document.getElementById(\'title\').value = \'\';
    document.getElementById(\'date\').value = \'\';
    document.getElementById(\'amount\').value = \'\';
    document.getElementById(\'type\').value = \'income\';
    document.getElementById(\'description\').value = \'\';
    // Set form action untuk add
    document.getElementById(\'transactionForm\').action = \'index.php?c=ExpenseController&m=tambah\';
    document.getElementById(\'modalForm\').classList.add(\'active\');
}

// Open Edit Modal
function openEditModal(id, title, date, amount, type, description) {
    document.getElementById(\'modalTitle\').textContent = \'Edit Transaction\';
    document.getElementById(\'transaction_id\').value = id;
    document.getElementById(\'title\').value = title;
    document.getElementById(\'date\').value = date;
    document.getElementById(\'amount\').value = amount;
    document.getElementById(\'type\').value = type;
    document.getElementById(\'description\').value = description;
    // Set form action untuk edit
    document.getElementById(\'transactionForm\').action = \'index.php?c=ExpenseController&m=edit\';
    document.getElementById(\'modalForm\').classList.add(\'active\');
}

// Open Delete Modal
function openDeleteModal(id, title) {
    document.getElementById(\'delete_transaction_id\').value = id;
    document.getElementById(\'deleteTransactionName\').textContent = title;
    document.getElementById(\'modalDelete\').classList.add(\'active\');
}

// Close Modal
function closeModal(modalId) {
    document.getElementById(modalId).classList.remove(\'active\');
}

// Close modal when clicking outside
document.querySelectorAll(\'.modal-overlay\').forEach(overlay => {
    overlay.addEventListener(\'click\', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
});

// Close modal with ESC key
document.addEventListener(\'keydown\', function(e) {
    if (e.key === \'Escape\') {
        closeModal(\'modalForm\');
        closeModal(\'modalDelete\');
    }
});
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
    <link rel="stylesheet" href="View/Trackurexpense.css">
</head>

<body>
    <div class="overlay" id="overlay" style="display:none" aria-hidden="true"></div>

    <div class="app" id="app">
        <aside id="sidebar" class="sidebar" role="navigation" aria-label="Sidebar">
            <div style="display:flex;align-items:center;gap:8px">
                <div class="logo" aria-hidden="false">
                    <img src="<?= $BASE ?>/images/LOGO.png" alt="Mome Logo">
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
                    // Routing sesuai dengan MVC pattern
                    $menuRoutes = [
                        'dashboard' => 'index.php?c=Dashboard&m=index',
                        'expenses' => 'index.php?c=ExpenseController&m=index',
                        'recap' => 'index.php?c=Recap&m=index',
                        'goals' => 'index.php?c=Goals&m=index',
                        'wishlist' => 'index.php?c=Wishlist&m=index',
                        'articles' => 'index.php?c=Articles&m=index'
                    ];
                    $menuUrl = $menuRoutes[$item['id']] ?? 'index.php?c=Dashboard&m=index';
                    ?>
                    <a href="<?= $menuUrl ?>" class="menu-item <?= $isActive ?>" role="menuitem">
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
                <?php
                // Tampilkan pesan error atau success
                if (isset($_SESSION['error'])) {
                    echo '<div style="background-color: #fee; color: #c33; padding: 12px; margin-bottom: 16px; border-radius: 8px; border: 1px solid #fcc;">' . htmlspecialchars($_SESSION['error']) . '</div>';
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['success'])) {
                    echo '<div style="background-color: #efe; color: #3c3; padding: 12px; margin-bottom: 16px; border-radius: 8px; border: 1px solid #cfc;">' . htmlspecialchars($_SESSION['success']) . '</div>';
                    unset($_SESSION['success']);
                }
                ?>
                <?= $pageContent1 ?>
                <?= $pageContent2 ?>
                <?= $pageContent3 ?>
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