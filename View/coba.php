<?php
// contoh satu file langsung
$progress = 17; // nilai progres
$BASE = '';     // ubah jika pakai subfolder
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MOME Goals</title>
  <!-- Iconify untuk ikon -->
  <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
  <style>
    /* --- Reset sederhana --- */
    body {
      font-family: "Poppins", sans-serif;
      margin: 0;
      background: #fafafa;
      color: #111827;
    }
    h1, h2, h3, p { margin: 0; }
    a { text-decoration: none; color: inherit; }

    /* --- Layout dasar --- */
    .sidebar {
      position: fixed;
      top: 0; left: 0;
      height: 100vh;
      width: 200px;
      background: #1e293b;
      padding: 20px;
      color: #fff;
    }
    .sidebar h2 { margin-bottom: 20px; }
    .sidebar ul { list-style: none; padding: 0; }
    .sidebar li { margin-bottom: 12px; }
    .sidebar a {
      color: #cbd5e1;
      display: block;
      padding: 8px 10px;
      border-radius: 8px;
    }
    .sidebar a.active, .sidebar a:hover {
      background: #2563eb;
      color: #fff;
    }

    .main-content {
      margin-left: 220px;
      padding: 40px;
    }

    /* --- Goal Card Component --- */
    .goal-card {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #ffffff;
      border: 2px solid #e5e7eb;
      border-radius: 28px;
      padding: 22px 28px;
      margin-top: 20px;
      gap: 20px;
    }

    /* left section: icon + text */
    .goal-left {
      display: flex;
      gap: 20px;
      align-items: center;
    }

    .goal-icon {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: #9bb9ff;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .goal-icon img {
      width: 44px;
      height: 44px;
      object-fit: contain;
    }

    .goal-meta { }
    .goal-title {
      font-size: 22px;
      font-weight: 700;
      margin-bottom: 6px;
    }
    .goal-sub {
      color: #9aa0a6;
      font-weight: 600;
      font-size: 14px;
      margin-bottom: 12px;
    }

    /* dots */
    .dots-row {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .dots-label {
      margin-right: 10px;
      color: #9aa0a6;
      font-weight: 600;
      font-size: 14px;
    }
    .dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: #2563eb;
    }

    /* right section */
    .goal-right {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    .target-area {
      text-align: right;
    }
    .target-area .dots-label {
      display: block;
      margin-bottom: 6px;
    }

    /* close button */
    .goal-close {
      background: transparent;
      border: none;
      cursor: pointer;
      padding: 4px;
    }
    .goal-close svg {
      width: 22px;
      height: 22px;
      color: #2563eb;
    }

    /* responsive */
    @media (max-width: 860px) {
      .main-content { margin-left: 0; padding: 20px; }
      .sidebar { display: none; }
      .goal-card { flex-direction: column; align-items: flex-start; }
      .goal-right { align-self: flex-end; }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <aside class="sidebar">
    <h2>MOME</h2>
    <ul>
      <li><a href="#" class="active">Dashboard</a></li>
      <li><a href="#">Goals</a></li>
    </ul>
  </aside>

  <!-- Main Content -->
  <div class="main-content">
    <div class="goal-card" role="group" aria-label="Goal: Dream House">
      <div class="goal-left">
        <div class="goal-icon" aria-hidden="true">
          <span class="iconify" data-icon="mdi:home" style="font-size:36px;color:#1e62d7"></span>
        </div>

        <div class="goal-meta">
          <h3 class="goal-title">Dream House</h3>
          <p class="goal-sub">Progress <?= $progress ?>%</p>

          <div class="dots-row" aria-hidden="true">
            <span class="dots-label">Current Savings</span>
            <?php for ($i = 0; $i < 6; $i++): ?>
              <span class="dot"></span>
            <?php endfor; ?>
          </div>
        </div>
      </div>

      <div class="goal-right">
        <div class="target-area" aria-hidden="true">
          <div class="dots-label">Target Amount</div>
          <div class="dots-row">
            <?php for ($i = 0; $i < 6; $i++): ?>
              <span class="dot"></span>
            <?php endfor; ?>
          </div>
        </div>

        <button class="goal-close" aria-label="Close goal">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
               stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
    </div>
    </div>
</body>
</html>
