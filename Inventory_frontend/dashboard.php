<?php
require '../Database/config.php';

session_start();

// Block users who aren't logged in, OR who are logged in but aren't an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../Inventory_frontend/login_signup.php");
  exit;
}

try {
  // 1. Fetch Stats for Top Grid
  $totalProducts   = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
  $totalUnits      = $pdo->query('SELECT COALESCE(SUM(stock), 0) FROM products')->fetchColumn();
  $totalCategories = $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
  $lowStock        = $pdo->query('SELECT COUNT(*) FROM products WHERE stock > 0 AND stock <= 3')->fetchColumn();
  $outOfStock      = $pdo->query('SELECT COUNT(*) FROM products WHERE stock = 0')->fetchColumn();

  // 2. Fetch History Metrics (Image 3)
  $totalRevenue    = $pdo->query('SELECT COALESCE(SUM(total_amount), 0) FROM orders')->fetchColumn();
  $transactions    = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
  $itemsSold       = $pdo->query('SELECT COALESCE(SUM(quantity), 0) FROM order_items')->fetchColumn();

  // 3. Fetch Log Metrics (Image 2)
  $totalLogs       = $pdo->query('SELECT COUNT(*) FROM product_logs')->fetchColumn();
  $totalAdded      = $pdo->query('SELECT COUNT(*) FROM product_logs WHERE action_type = "Added"')->fetchColumn();
  $totalDeleted    = $pdo->query('SELECT COUNT(*) FROM product_logs WHERE action_type = "Deleted"')->fetchColumn();
} catch (Exception $e) {
  $errorMsg = $e->getMessage();
  // Fallback values if tables don't exist yet
  $totalProducts = $totalUnits = $totalCategories = $lowStock = $outOfStock = 0;
  $totalRevenue = 0.00;
  $transactions = $itemsSold = $totalLogs = $totalAdded = $totalDeleted = 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>K's Inventory — Dashboard</title>
  <link rel="stylesheet" href="../style.css">

  <style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=DM+Mono:wght@400;500&display=swap');

    /* User Dropdown Profile Container */
    .topbar-admin {
      position: relative;
      cursor: pointer;
    }

    .profile-img {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      object-fit: cover;
    }

    /* Floating Menu Drawer */
    .dropdown-menu {
      display: none;
      position: absolute;
      left: 0;
      top: 110%;
      background-color: #ffffff;
      min-width: 140px;
      box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
      border-radius: 8px;
      z-index: 1050;
      overflow: hidden;
      border: 1px solid #ddd;
    }

    .dropdown-menu a {
      color: #ff4b4b;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
      font-size: 14px;
      font-weight: 600;
      transition: 0.2s;
    }

    .dropdown-menu a:hover {
      background-color: #fff0f0;
    }

    /* Dynamic Grid Icon Background Colors */
    .icon-products {
      background: #e05c5c;
    }

    .icon-units {
      background: #e8a020;
    }

    .icon-cats {
      background: #27ae60;
    }

    .icon-low {
      background: #9b59b6;
    }

    .icon-out {
      background: #1abc9c;
    }

    /* Split Dashboard Layout: Left Content & Right Sidebar */
    .dashboard-container {
      display: grid;
      grid-template-columns: 1fr 380px;
      gap: 24px;
      align-items: start;
    }

    .dashboard-left-content {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    /* ── SCALING DOWN THE ORIGINAL STAT CARDS ── */
    .dashboard-left-content .stat-card {
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
    }

    .dashboard-left-content .stat-icon {
      width: 65px;
      /* Shrunk down from 90px */
    }

    .dashboard-left-content .stat-icon svg {
      width: 28px;
      /* Shrunk down from 38px */
      height: 28px;
    }

    .dashboard-left-content .stat-body {
      padding: 12px 16px;
    }

    .dashboard-left-content .stat-body .num {
      font-size: 1.6rem;
    }

    .dashboard-left-content .stat-body .label {
      font-size: 0.82rem;
      margin-top: 2px;
      color: #474747;
      font-weight: 300;
    }

    .dashboard-section {
      background: #ffffff;
      border-radius: 16px;
      padding: 20px;
      border: 1px solid #eef0f2;
    }

    .section-title {
      font-size: 14px;
      font-weight: 700;
      color: #414141;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 16px;
    }

    .metrics-row-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
    }

    .metric-sub-card {
      background: #fafafa;
      padding: 16px;
      border-radius: 12px;
      border: 1px solid #cfcfcf;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .metric-sub-card h3 {
      font-size: 12px;
      font-weight: 700;
      color: #000000;
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .metric-sub-card p {
      font-size: 22px;
      font-weight: 700;
      color: #1a1a1a;
      margin: 0;
    }

    .metric-sub-card p.green-txt {
      color: #0ed73d;
      font-family: 'DM Mono', monospace;
      font-weight: 500;
    }

    .metric-sub-card p.blue-txt {
      color: #4d66ff;
    }

    /* Notifications Sidebar Box styling */
    .notifications-sidebar {
      background: #ffffff;
      border-radius: 16px;
      padding: 20px;
      border: 1px solid #eef0f2;
      min-height: 400px;
    }
  </style>
</head>

<body>

  <div class="topbar">
    <div class="topbar-admin" onclick="toggleUserDropdown(event)">
      <img src="../Images/profile.png" alt="Profile" class="profile-img">
      <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?> ▼

      <div id="userDropdownMenu" class="dropdown-menu">
        <a href="../Inventory_frontend/logout.php">Logout</a>
      </div>
    </div>

    <span class="topbar-title">
      K's Inventory System
    </span>
  </div>

  <div class="layout">
    <nav class="sidebar">
      <a href="dashboard.php" class="active">Dashboard</a>
      <a href="categories.php">Categories</a>
      <a href="products.php">Products</a>
      <a href="history.php">History</a>
    </nav>

    <div class="main">
      <?php if (isset($errorMsg)): ?>
        <div style="background: #fff0f0; border: 1px solid #ffbcbc; color: #ff4b4b; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
          <strong>Database Notice:</strong> <?= htmlspecialchars($errorMsg) ?>
        </div>
      <?php endif; ?>

      <div class="dashboard-container">

        <div class="dashboard-left-content">

          <div class="stat-grid" style="margin-bottom: 0;">
            <div class="stat-card">
              <div class="stat-icon icon-products">
                <svg viewBox="0 0 24 24">
                  <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round" />
                </svg>
              </div>
              <div class="stat-body">
                <div class="num"><?= $totalProducts ?></div>
                <div class="label">Total Products</div>
              </div>
            </div>

            <div class="stat-card">
              <div class="stat-icon icon-units">
                <svg viewBox="0 0 24 24">
                  <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z" stroke="#fff" stroke-width="2" fill="none" />
                  <polyline points="3.27 6.96 12 12.01 20.73 6.96" stroke="#fff" stroke-width="2" fill="none" />
                  <line x1="12" y1="22.08" x2="12" y2="12" stroke="#fff" stroke-width="2" />
                </svg>
              </div>
              <div class="stat-body">
                <div class="num"><?= $totalUnits ?></div>
                <div class="label">Total Units</div>
              </div>
            </div>

            <div class="stat-card">
              <div class="stat-icon icon-cats">
                <svg viewBox="0 0 24 24">
                  <rect x="3" y="3" width="7" height="7" fill="#fff" />
                  <rect x="14" y="3" width="7" height="7" fill="#fff" />
                  <rect x="3" y="14" width="7" height="7" fill="#fff" />
                  <rect x="14" y="14" width="7" height="7" fill="#fff" />
                </svg>
              </div>
              <div class="stat-body">
                <div class="num"><?= $totalCategories ?></div>
                <div class="label">Categories</div>
              </div>
            </div>
          </div>

          <div class="stat-grid-2">
            <div class="stat-card">
              <div class="stat-icon icon-low">
                <svg viewBox="0 0 24 24">
                  <polyline points="23 18 13.5 8.5 8.5 13.5 1 6" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round" />
                  <polyline points="17 18 23 18 23 12" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round" />
                </svg>
              </div>
              <div class="stat-body">
                <div class="num"><?= $lowStock ?></div>
                <div class="label">Low stock</div>
              </div>
            </div>

            <div class="stat-card">
              <div class="stat-icon icon-out">
                <svg viewBox="0 0 24 24">
                  <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round" />
                  <polyline points="17 6 23 6 23 12" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round" />
                </svg>
              </div>
              <div class="stat-body">
                <div class="num"><?= $outOfStock ?></div>
                <div class="label">Out of stock</div>
              </div>
            </div>
          </div>

          <div class="dashboard-section">
            <div class="section-title">Recent Transactions</div>
            <div class="metrics-row-grid">
              <div class="metric-sub-card">
                <h3>Total Revenue</h3>
                <p class="green-txt">₱<?= number_format($totalRevenue, 2) ?></p>
              </div>
              <div class="metric-sub-card">
                <h3>Transactions</h3>
                <p class="blue-txt"><?= $transactions ?></p>
              </div>
              <div class="metric-sub-card">
                <h3>Items Sold</h3>
                <p><?= $itemsSold ?></p>
              </div>
            </div>
          </div>

          <div class="dashboard-section">
            <div class="section-title">Total Revenue and Stuff na nasa History</div>
            <div class="metrics-row-grid">
              <div class="metric-sub-card">
                <h3>Total Logs</h3>
                <p class="blue-txt"><?= $totalLogs ?></p>
              </div>
              <div class="metric-sub-card">
                <h3>Products Added</h3>
                <p class="green-txt"><?= $totalAdded ?></p>
              </div>
              <div class="metric-sub-card">
                <h3>Products Deleted</h3>
                <p><?= $totalDeleted ?></p>
              </div>
            </div>
          </div>

        </div>

        <div class="notifications-sidebar">
          <div class="section-title">Notifications</div>
          <p style="color: #686868; font-size: 14px;">No new update alerts.</p>
        </div>

      </div>
    </div>
  </div>

  <script>
    function toggleUserDropdown(event) {
      event.stopPropagation();
      const dropdown = document.getElementById("userDropdownMenu");
      dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
    }

    window.onclick = function() {
      const dropdown = document.getElementById("userDropdownMenu");
      if (dropdown && dropdown.style.display === "block") {
        dropdown.style.display = "none";
      }
    }
  </script>
</body>

</html>