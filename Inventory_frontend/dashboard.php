<?php
require '../Database/config.php';

session_start();

// Block users who aren't logged in, OR who are logged in but aren't an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Inventory_frontend/login_signup.php");
    exit;
}

// Fetch stats directly from DB
$totalProducts   = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$totalUnits      = $pdo->query('SELECT COALESCE(SUM(stock), 0) FROM products')->fetchColumn();
$totalCategories = $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$lowStock        = $pdo->query('SELECT COUNT(*) FROM products WHERE stock > 0 AND stock <= 3')->fetchColumn();
$outOfStock      = $pdo->query('SELECT COUNT(*) FROM products WHERE stock = 0')->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>K's Inventory — Dashboard</title>
  <link rel="stylesheet" href="../style.css">

  <style>
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
    .icon-products { background: #e05c5c; }
    .icon-units    { background: #e8a020; }
    .icon-cats     { background: #27ae60; }
    .icon-low      { background: #9b59b6; }
    .icon-out      { background: #1abc9c; }
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
      <div class="stat-grid">
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