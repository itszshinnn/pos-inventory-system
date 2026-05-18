<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>K's Inventory — Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="topbar">
  <div class="topbar-admin">
    <svg viewBox="0 0 32 32" fill="none"><circle cx="16" cy="16" r="16" fill="rgba(255,255,255,0.2)"/><circle cx="16" cy="13" r="5" fill="#fff"/><path d="M6 26c0-5 4.5-8 10-8s10 3 10 8" fill="#fff"/></svg>
    Admin ▾
  </div>
  <span class="topbar-title">K's Inventory System</span>
</div>

<div class="layout">
  <nav class="sidebar">
    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="categories.php">Categories</a>
    <a href="products.php">Products</a>
  </nav>

  <div class="main">
    <?php
      // Fetch stats directly from DB
      $totalProducts   = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
      $totalUnits      = $pdo->query('SELECT COALESCE(SUM(stock),0) FROM products')->fetchColumn();
      $totalCategories = $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
      $lowStock        = $pdo->query('SELECT COUNT(*) FROM products WHERE stock > 0 AND stock <= 5')->fetchColumn();
      $outOfStock      = $pdo->query('SELECT COUNT(*) FROM products WHERE stock = 0')->fetchColumn();
    ?>

    <div class="stat-grid">
      <div class="stat-card">
        <div class="stat-icon" style="background:#e05c5c;">
          <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round"/></svg>
        </div>
        <div class="stat-body">
          <div class="num"><?= $totalProducts ?></div>
          <div class="label">Total Products</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon" style="background:#e8a020;">
          <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z" stroke="#fff" stroke-width="2" fill="none"/><polyline points="3.27 6.96 12 12.01 20.73 6.96" stroke="#fff" stroke-width="2" fill="none"/><line x1="12" y1="22.08" x2="12" y2="12" stroke="#fff" stroke-width="2"/></svg>
        </div>
        <div class="stat-body">
          <div class="num"><?= $totalUnits ?></div>
          <div class="label">Total Units</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon" style="background:#27ae60;">
          <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" fill="#fff"/><rect x="14" y="3" width="7" height="7" fill="#fff"/><rect x="3" y="14" width="7" height="7" fill="#fff"/><rect x="14" y="14" width="7" height="7" fill="#fff"/></svg>
        </div>
        <div class="stat-body">
          <div class="num"><?= $totalCategories ?></div>
          <div class="label">Categories</div>
        </div>
      </div>
    </div>

    <div class="stat-grid-2">
      <div class="stat-card">
        <div class="stat-icon" style="background:#9b59b6;">
          <svg viewBox="0 0 24 24"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round"/><polyline points="17 18 23 18 23 12" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round"/></svg>
        </div>
        <div class="stat-body">
          <div class="num"><?= $lowStock ?></div>
          <div class="label">Low stock</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon" style="background:#1abc9c;">
          <svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round"/><polyline points="17 6 23 6 23 12" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round"/></svg>
        </div>
        <div class="stat-body">
          <div class="num"><?= $outOfStock ?></div>
          <div class="label">Out of stock</div>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
