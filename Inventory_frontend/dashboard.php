<?php
session_start();

require_once '../Database/Database.php';
require_once '../Inventory_backend/ReportManager.php';

$database = new Database();
$pdo = $database->getConnection();
$reportManager = new ReportManager($pdo);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../Inventory_frontend/login.php");
  exit;
}

$range = $_GET['range'] ?? 'today';
$startDate = $_GET['start'] ?? null;
$endDate = $_GET['end'] ?? null;

$metrics = $reportManager->getDashboardMetrics($range, $startDate, $endDate);

extract($metrics);

$rangeLabel = 'Today\'s';
if ($range === 'yesterday') $rangeLabel = 'Yesterday\'s';
if ($range === 'week') $rangeLabel = 'Weekly';
if ($range === 'month') $rangeLabel = 'Monthly';
if ($range === 'alltime') $rangeLabel = 'All Time';
if ($range === 'custom') {
  if ($startDate && $endDate) {
    $rangeLabel = date("M j", strtotime($startDate)) . ' - ' . date("M j", strtotime($endDate));
  } else {
    $rangeLabel = 'Custom';
  }
}

$allNotifications = [];

foreach ($productLogs as $log) {
  $allNotifications[] = [
    'type' => 'product_log',
    'time' => $log['created_at'],
    'data' => $log
  ];
}

foreach ($salesLogs as $sale) {
  $allNotifications[] = [
    'type' => 'sales_log',
    'time' => $sale['created_at'],
    'data' => $sale
  ];
}

foreach ($newUsers as $user) {
  $allNotifications[] = [
    'type' => 'new_user',
    'time' => $user['created_at'],
    'data' => $user
  ];
}

usort($allNotifications, function ($a, $b) {
  return strtotime($b['time']) <=> strtotime($a['time']);
});
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <title>K's Inventory — Dashboard</title>
  <link rel="stylesheet" href="../style.css?v=<?= filemtime(__DIR__ . '/../style.css') ?>">

  <style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=DM+Mono:wght@400;500&display=swap');

    .profile-img {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      object-fit: cover;
    }

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

    .icon-products {
      background: rgba(67, 97, 238, 0.18);
    }

    .icon-products svg {
      fill: none;
    }

    .icon-products svg path {
      stroke: #3b55cc;
    }

    .icon-units {
      background: rgba(16, 185, 129, 0.18);
    }

    .icon-units svg {
      fill: none;
    }

    .icon-units svg path,
    .icon-units svg polyline,
    .icon-units svg line {
      stroke: #059669;
    }

    .icon-cats {
      background: rgba(139, 92, 246, 0.18);
    }

    .icon-cats svg rect {
      fill: #7c3aed;
    }

    .icon-low {
      background: rgba(245, 158, 11, 0.18);
    }

    .icon-low svg {
      fill: none;
    }

    .icon-low svg polyline {
      stroke: #d97706;
    }

    .icon-out {
      background: rgba(239, 68, 68, 0.18);
    }

    .icon-out svg {
      fill: none;
    }

    .icon-out svg polyline {
      stroke: #dc2626;
    }

    .dashboard-container {
      display: grid;
      grid-template-columns: 1fr 280px;
      gap: 14px;
      align-items: start;
    }

    .dashboard-left-content {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    .dashboard-section {
      background: #ffffff;
      border-radius: 12px;
      padding: 14px 16px;
      border: 1px solid #eef0f2;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .section-title {
      font-size: 11.5px;
      font-weight: 700;
      color: #334155;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 10px;
    }

    .metrics-row-grid {
      display: grid;
      grid-template-columns: repeat(5, minmax(0, 1fr));
      gap: 10px;
    }

    .metric-sub-card {
      background: #f8fafc;
      padding: 10px 12px;
      border-radius: 10px;
      border: 1px solid #e2e8f0;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-width: 0;
      overflow: hidden;
    }

    .metric-sub-card h3 {
      font-size: 10px;
      font-weight: 700;
      color: #475569;
      margin-bottom: 3px;
      text-transform: uppercase;
      letter-spacing: 0.2px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .metric-sub-card p {
      font-size: 15px;
      font-weight: 700;
      color: #0f172a;
      margin: 0;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      letter-spacing: -0.2px;
    }

    .metric-sub-card p.green-txt {
      color: #10b981;
      font-weight: 700;
    }

    .metric-sub-card p.blue-txt {
      color: #4361ee;
    }

    .dashboard-left-content {
      display: flex;
      flex-direction: column;
      gap: 12px;
      height: 100%;
      min-height: 0;
    }

    .dashboard-right-sidebar {
      display: flex;
      flex-direction: column;
      gap: 12px;
      height: 100%;
      min-height: 0;
    }

    .wide-line-chart-card {
      background: #ffffff;
      border-radius: 12px;
      padding: 14px 16px;
      border: 1px solid #eef0f2;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
      display: flex;
      flex-direction: column;
      flex: 1;
      min-height: 280px;
      box-sizing: border-box;
    }

    .wide-line-chart-card .section-title {
      color: #334155;
      font-weight: 700;
    }

    .chart-canvas-wrapper {
      position: relative;
      flex: 1;
      width: 100%;
      height: 100%;
      min-height: 0;
    }

    .notifications-sidebar {
      background: #ffffff;
      border-radius: 12px;
      padding: 14px 16px;
      border: 1px solid #eef0f2;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
      display: flex;
      flex-direction: column;
      height: 285px;
      flex-shrink: 0;
      box-sizing: border-box;
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
    }

    .top-selling-card {
      background: #ffffff;
      border-radius: 12px;
      padding: 14px 16px;
      border: 1px solid #eef0f2;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
      display: flex;
      flex-direction: column;
      flex: 1;
      min-height: 190px;
      box-sizing: border-box;
      overflow: hidden;
    }

    .top-selling-card .section-title {
      font-size: 13px;
      font-weight: 700;
      color: #334155;
      margin-bottom: 8px;
      padding-bottom: 0px;
      border-bottom: none;
      letter-spacing: 0.5px;
    }

    .leaderboard-list {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      gap: 6px;
      flex: 1;
      margin-top: 0px;
      margin-bottom: 0px;
    }

    .leaderboard-item {
      display: flex;
      flex-direction: column;
      gap: 2px;
      padding: 2px 0;
    }

    .leaderboard-row {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 12.5px;
    }

    .rank-badge {
      font-weight: 700;
      font-size: 11px;
      padding: 2px 6px;
      border-radius: 4px;
      background: #f1f5f9;
      color: #64748b;
      min-width: 20px;
      text-align: center;
    }

    .rank-badge.rank-1 {
      background: rgba(67, 97, 238, 0.15);
      color: #4361ee;
    }

    .rank-badge.rank-2 {
      background: rgba(16, 185, 129, 0.15);
      color: #10b981;
    }

    .rank-badge.rank-3 {
      background: rgba(139, 92, 246, 0.15);
      color: #8b5cf6;
    }

    .product-name-container {
      flex: 1;
      overflow: hidden;
      white-space: nowrap;
      margin: 0 8px;
      position: relative;
      height: 18px;
      line-height: 18px;
    }

    .product-name {
      font-weight: 600;
      color: #1e293b;
      font-size: 12.5px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      display: block;
      width: 100%;
      height: 100%;
    }

    .leaderboard-item:hover .product-name {
      display: block;
      overflow: visible;
      text-overflow: clip;
      width: auto;
      position: absolute;
      left: 0;
      top: 0;
      animation: nameMarquee 10s linear infinite;
    }

    @keyframes nameMarquee {
      0% {
        transform: translateX(0);
      }

      50% {
        transform: translateX(-55%);
      }

      100% {
        transform: translateX(0);
      }
    }

    .sold-count {
      font-weight: 700;
      color: #64748b;
      font-size: 11px;
      white-space: nowrap;
    }

    .progress-bar-bg {
      height: 5px;
      background: #f1f5f9;
      border-radius: 3px;
      overflow: hidden;
      width: 100%;
    }

    .progress-bar-fill {
      height: 100%;
      border-radius: 3px;
      background: #cbd5e1;
      transition: width 0.4s ease;
    }

    .progress-bar-fill.rank-fill-1 {
      background: #4361ee;
    }

    .progress-bar-fill.rank-fill-2 {
      background: #10b981;
    }

    .progress-bar-fill.rank-fill-3 {
      background: #8b5cf6;
    }

    .notif-header {
      padding-bottom: 10px;
      margin-bottom: 10px;
      border-bottom: 1px solid #f1f5f9;
      flex-shrink: 0;
    }

    .notif-body-scroll {
      flex: 1;
      overflow-y: auto;
      padding-right: 4px;
    }

    .notif-body-scroll::-webkit-scrollbar {
      width: 4px;
    }

    .notif-body-scroll::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 4px;
    }

    .notif-item {
      background: #f8fafc;
      border-radius: 8px;
      padding: 8px 12px;
      margin-bottom: 8px;
      font-size: 12.5px;
      color: #334155;
      line-height: 1.4;
      border: 1px solid #e2e8f0;
    }

    .notif-warning {
      background: #fff4db;
      border-color: #ffd978;
      color: #8a6500;
    }

    .notif-success {
      background: #e8f9ee;
      border-color: #8ce0a6;
      color: #157347;
    }

    .notif-danger {
      background: #fff0f0;
      border-color: #ffbcbc;
      color: #ff4b4b;
    }

    .layout {
      display: flex;
    }

    .main {
      flex: 1;
      padding: 24px;
      overflow-x: hidden;
    }

    .sidebar-toggle-btn {
      display: none;
      background: transparent;
      border: none;
      color: white;
      cursor: pointer;
      padding: 6px;
      margin-right: 10px;
      border-radius: 6px;
      align-items: center;
      justify-content: center;
    }

    .sidebar-toggle-btn:hover {
      background: rgba(255, 255, 255, 0.1);
    }

    .sidebar-backdrop {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: 1999;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .sidebar-backdrop.active {
      display: block;
      opacity: 1;
    }

    @media (max-width: 768px) {
      .sidebar-toggle-btn {
        display: flex;
        padding: 4px;
        margin-right: 6px;
      }

      .topbar {
        height: 52px !important;
      }

      .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        height: 100dvh;
        width: 240px;
        z-index: 2000;
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
        padding-bottom: 24px !important;
      }

      .sidebar.active {
        transform: translateX(0);
      }

      .date-filter-wrapper {
        position: static !important;
      }

      #custom-date-inputs {
        position: absolute !important;
        top: 52px !important;
        left: 0 !important;
        right: 0 !important;
        background: #181b2a !important;
        padding: 8px 12px !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        z-index: 1000 !important;
        width: 100% !important;
        box-sizing: border-box !important;
        justify-content: space-between !important;
      }

      #custom-date-inputs input[type="date"] {
        flex: 1 !important;
        min-width: 0 !important;
        height: 32px !important;
        background: #fff !important;
        color: #000 !important;
      }

      #custom-date-inputs button {
        height: 32px !important;
        line-height: 32px !important;
        background: #1b4ef5 !important;
        color: #fff !important;
      }

      .main {
        width: 100%;
        padding: 12px 12px 70px !important;
      }

      .dashboard-container {
        grid-template-columns: 1fr;
        gap: 16px;
      }

      .stat-grid-5 {
        grid-template-columns: 1fr !important;
        gap: 10px !important;
      }

      .metrics-row-grid {
        grid-template-columns: 1fr !important;
        gap: 10px !important;
      }

      .metric-sub-card {
        padding: 12px;
      }

      .metric-sub-card p {
        font-size: 17px !important;
        word-break: break-word;
      }

      .notif-item {
        font-size: 12px;
        padding: 10px;
        word-break: break-word;
        overflow-wrap: break-word;
      }

      .top-selling-card {
        min-height: auto !important;
        height: auto !important;
        overflow: visible !important;
        margin-bottom: 0 !important;
      }

      .leaderboard-list {
        gap: 10px;
      }

    }
  </style>
</head>

<body>
  <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

  <div class="layout">
    <nav class="sidebar no-transition" id="sidebarNav">
      <div class="sidebar-brand">
        <div class="brand-logo-icon">
          <img src="../Images/logo.svg" alt="Logo" style="width: 26px; height: 26px; object-fit: contain;">
        </div>
        <div class="brand-text">
          <span class="brand-name">Kinetix</span>
          <span class="brand-sub">Inventory System</span>
        </div>
      </div>

      <span class="sidebar-group-label">Menu</span>
      <a href="dashboard.php" class="active"><?php include '../Images/dashboard.svg'; ?> <span>Dashboard</span></a>
      <a href="categories.php"><?php include '../Images/categories.svg'; ?> <span>Categories</span></a>
      <a href="products.php"><?php include '../Images/products.svg'; ?> <span>Products</span></a>
      <a href="purchase_orders.php"><?php include '../Images/purchase_orders.svg'; ?> <span>Purchase Orders</span></a>

      <span class="sidebar-group-label">Reports</span>
      <a href="xml.php"><?php include '../Images/backup.svg'; ?> <span>Backup and Restore</span></a>
      <a href="#" onclick="toggleHistorySubmenu(event)" id="historyParentLink"><?php include '../Images/history.svg'; ?> <span>History</span></a>
      <div id="historySubmenu" style="display: <?= (in_array(basename($_SERVER['PHP_SELF']), ['history.php', 'product_history.php', 'login_history.php']) ? 'block' : 'none') ?>;">
        <a href="history.php" class="sub-tab<?= (basename($_SERVER['PHP_SELF']) == 'history.php' ? ' active' : '') ?>">Sales History</a>
        <a href="product_history.php" class="sub-tab<?= (basename($_SERVER['PHP_SELF']) == 'product_history.php' ? ' active' : '') ?>">Inventory Logs</a>
        <a href="login_history.php" class="sub-tab<?= (basename($_SERVER['PHP_SELF']) == 'login_history.php' ? ' active' : '') ?>">Log History</a>
      </div>
      <a href="users.php"><?php include '../Images/users.svg'; ?> <span>Users</span></a>

      <div class="sidebar-logout">
        <a href="../Inventory_frontend/logout.php">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" x2="9" y1="12" y2="12" />
          </svg>
          <span>Logout</span>
        </a>
      </div>
    </nav>
    <script>
      (function() {
        const state = sessionStorage.getItem("sidebarState");
        if (state === "active") {
          const sb = document.getElementById("sidebarNav");
          const bd = document.getElementById("sidebarBackdrop");
          if (sb) sb.classList.add("active");
          if (bd) bd.classList.add("active");
        }
      })();
    </script>

    <div class="main-wrapper">
      <div class="topbar">
        <button class="sidebar-toggle-btn" onclick="toggleSidebar(event)" aria-label="Toggle Navigation Sidebar">
          <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round">
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>

        <span class="topbar-title">Dashboard</span>

        <div class="topbar-right-group">
          <div class="date-filter-wrapper" style="position: relative; display: flex; align-items: center; gap: 8px;">
            <select id="dashboard-date-filter" onchange="handleDateFilterChange(this.value)" style="padding: 6px 30px 6px 12px; border-radius: 8px; border: 1px solid #dde1e9; font-family: inherit; font-size: 0.85rem; font-weight: 600; cursor: pointer; outline: none; background: #ffffff; color: #1a1c20; transition: border-color 0.2s; height: 32px; appearance: none; -webkit-appearance: none; background-image: url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%228%22 viewBox=%220 0 12 8%22><path d=%22M1 1l5 5 5-5%22 stroke=%22%237a7f8a%22 stroke-width=%221.5%22 fill=%22none%22 stroke-linecap=%22round%22/></svg>'); background-repeat: no-repeat; background-position: right 10px center;">
              <option value="today" <?= $range === 'today' ? 'selected' : '' ?>>Today</option>
              <option value="yesterday" <?= $range === 'yesterday' ? 'selected' : '' ?>>Yesterday</option>
              <option value="week" <?= $range === 'week' ? 'selected' : '' ?>>Last 7 Days</option>
              <option value="month" <?= $range === 'month' ? 'selected' : '' ?>>Last 30 Days</option>
              <option value="alltime" <?= $range === 'alltime' ? 'selected' : '' ?>>All Time</option>
              <option value="custom" <?= $range === 'custom' ? 'selected' : '' ?>>Custom Range...</option>
            </select>
            <div id="custom-date-inputs" style="display: <?= $range === 'custom' ? 'flex' : 'none' ?>; align-items: center; gap: 6px;">
              <input type="date" id="filter-start-date" value="<?= htmlspecialchars($startDate ?? '') ?>" style="padding: 4px 8px; border-radius: 6px; border: 1px solid #dde1e9; font-size: 0.8rem; height: 30px; font-family: inherit;" />
              <span style="font-size: 0.8rem; color: #64748b; font-weight: 600;">to</span>
              <input type="date" id="filter-end-date" value="<?= htmlspecialchars($endDate ?? '') ?>" style="padding: 4px 8px; border-radius: 6px; border: 1px solid #dde1e9; font-size: 0.8rem; height: 30px; font-family: inherit;" />
              <button onclick="applyCustomDateFilter()" class="btn" style="padding: 0 12px; font-size: 0.8rem; border-radius: 6px; font-weight: 600; cursor: pointer; border: none; height: 30px; line-height: 30px; display: inline-flex; align-items: center; justify-content: center;">Apply</button>
            </div>
          </div>

          <button id="topbar-ai-btn" onclick="toggleAiChat()" class="topbar-ai-btn"><img src="../Images/message.svg" alt="AI" style="width: 15px; height: 15px; object-fit: contain; filter: brightness(0) invert(1); flex-shrink: 0;"> AI Assistant</button>
          <div class="topbar-admin">
            <img src="../Images/profile.png" alt="Profile" class="profile-img">
            <span class="topbar-admin-text"><span class="welcome-prefix">Welcome back, </span><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>!</span>
          </div>
        </div>
      </div>

      <div class="main">
        <?php if (isset($errorMsg)): ?>
          <div style="background: #fff0f0; border: 1px solid #ffbcbc; color: #ff4b4b; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            <strong>Database Notice:</strong> <?= htmlspecialchars($errorMsg) ?>
          </div>
        <?php endif; ?>

        <div class="dashboard-container">
          <div class="dashboard-left-content">
            <div class="stat-grid-5">
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
                <div class="stat-icon icon-low">
                  <svg viewBox="0 0 24 24">
                    <polyline points="23 18 13.5 8.5 8.5 13.5 1 6" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round" />
                    <polyline points="17 18 23 18 23 12" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round" />
                  </svg>
                </div>
                <div class="stat-body">
                  <div class="num"><?= $lowStock ?></div>
                  <div class="label">Low on stock</div>
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

            <div class="dashboard-section todays-performance-section" style="margin-bottom: 0;">
              <div class="section-title"><?= $rangeLabel ?> Performance</div>
              <div class="metrics-row-grid">
                <div class="metric-sub-card">
                  <h3><?= $rangeLabel ?> Revenue</h3>
                  <p class="green-txt">Php<?= number_format($todayRevenue, 2) ?></p>
                </div>
                <div class="metric-sub-card">
                  <h3><?= $rangeLabel ?> COGS</h3>
                  <p style="color:#ef4444;">Php<?= number_format($todayCOGS, 2) ?></p>
                </div>
                <div class="metric-sub-card">
                  <h3><?= $rangeLabel ?> Purchases</h3>
                  <p style="color:#f59e0b;">Php<?= number_format($todayPurchases ?? 0, 2) ?></p>
                </div>
                <div class="metric-sub-card">
                  <h3><?= $rangeLabel ?> Profit</h3>
                  <p style="color:#22c55e;">Php<?= number_format($todayProfit, 2) ?></p>
                </div>
                <div class="metric-sub-card">
                  <h3><?= $rangeLabel ?> Sales</h3>
                  <p class="blue-txt"><?= $todayTransactions ?></p>
                </div>
              </div>
            </div>

            <div class="graph-card wide-line-chart-card">
              <div class="section-title" style="margin-bottom: 6px;">Revenue, COGS &amp; Net Profit Overview</div>
              <div class="chart-canvas-wrapper">
                <canvas id="profitChart"></canvas>
              </div>
            </div>
          </div>

          <div class="dashboard-right-sidebar">
            <div class="notifications-sidebar">
              <div class="notif-header">
                <div class="section-title" style="margin-bottom: 0;">Stock Alerts &amp; Activity</div>
              </div>
              <div class="notif-body-scroll">
                <?php foreach ($lowStocks as $stock): ?>
                  <?php if ($stock['stock'] == 0): ?>
                    <div class="notif-item notif-danger">
                      🚨 <strong><?= htmlspecialchars($stock['name']) ?></strong> is out of stock!
                    </div>
                  <?php else: ?>
                    <div class="notif-item notif-warning">
                      ⚠️ <strong><?= htmlspecialchars($stock['name']) ?></strong> is low on stock (<?= $stock['stock'] ?> left)
                    </div>
                  <?php endif; ?>
                <?php endforeach; ?>

                <?php foreach ($allNotifications as $notif): ?>
                  <?php $data = $notif['data']; ?>
                  <?php if ($notif['type'] === 'product_log'): ?>
                    <div class="notif-item">
                      📦 <strong><?= htmlspecialchars($data['changed_by']) ?></strong> <?= strtolower($data['action_type']) ?> <?= htmlspecialchars($data['product_name']) ?>
                    </div>
                  <?php elseif ($notif['type'] === 'sales_log'): ?>
                    <div class="notif-item notif-success">
                      🛒 Order #<?= $data['order_no'] ?> completed — Php<?= number_format($data['total_amount'], 2) ?>
                    </div>
                  <?php elseif ($notif['type'] === 'new_user'): ?>
                    <div class="notif-item">
                      👤 New <?= htmlspecialchars($data['role']) ?>: <strong><?= htmlspecialchars($data['username']) ?></strong>
                    </div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="graph-card top-selling-card">
              <div class="section-title">Top 5 Selling Products</div>
              <div class="leaderboard-list">
                <?php
                $maxSold = 1;
                if (!empty($topProducts)) {
                  foreach ($topProducts as $p) {
                    if ($p['sold'] > $maxSold) $maxSold = $p['sold'];
                  }
                }
                ?>
                <?php if (!empty($topProducts)): ?>
                  <?php $rank = 1; ?>
                  <?php foreach (array_slice($topProducts, 0, 5) as $product): ?>
                    <?php $pct = round(($product['sold'] / $maxSold) * 100); ?>
                    <div class="leaderboard-item">
                      <div class="leaderboard-row">
                        <span class="rank-badge rank-<?= $rank ?>">#<?= $rank ?></span>
                        <div class="product-name-container">
                          <span class="product-name" title="<?= htmlspecialchars($product['name']) ?>"><?= htmlspecialchars($product['name']) ?></span>
                        </div>
                        <span class="sold-count"><?= $product['sold'] ?> sold</span>
                      </div>
                      <div class="progress-bar-bg">
                        <div class="progress-bar-fill rank-fill-<?= $rank ?>" style="width: <?= $pct ?>%;"></div>
                      </div>
                    </div>
                    <?php $rank++; ?>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div style="font-size: 12px; color: #94a3b8; padding: 10px 0;">No sales data available</div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    function handleDateFilterChange(val) {
      const customInputs = document.getElementById("custom-date-inputs");
      if (val === 'custom') {
        customInputs.style.display = 'flex';
      } else {
        customInputs.style.display = 'none';
        window.location.href = `dashboard.php?range=${val}`;
      }
    }

    function applyCustomDateFilter() {
      const start = document.getElementById("filter-start-date").value;
      const end = document.getElementById("filter-end-date").value;
      if (!start || !end) {
        alert("Please select both start and end dates.");
        return;
      }
      if (start > end) {
        alert("Start date cannot be after end date.");
        return;
      }
      window.location.href = `dashboard.php?range=custom&start=${start}&end=${end}`;
    }

    function toggleUserDropdown(event) {
      event.stopPropagation();
      const dropdown = document.getElementById("userDropdownMenu");
      dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
    }

    function toggleSidebar(event) {
      if (event) event.stopPropagation();
      const sidebar = document.getElementById("sidebarNav");
      const backdrop = document.getElementById("sidebarBackdrop");

      if (sidebar && backdrop) {
        sidebar.classList.toggle("active");
        backdrop.classList.toggle("active");
        if (sidebar.classList.contains("active")) {
          sessionStorage.setItem("sidebarState", "active");
        } else {
          sessionStorage.setItem("sidebarState", "inactive");
        }
      }
    }

    function closeSidebar() {
      const sidebar = document.getElementById("sidebarNav");
      const backdrop = document.getElementById("sidebarBackdrop");
      if (sidebar) sidebar.classList.remove("active");
      if (backdrop) backdrop.classList.remove("active");
      sessionStorage.setItem("sidebarState", "inactive");
    }

    function toggleHistorySubmenu(event) {
      if (event) event.preventDefault();
      const submenu = document.getElementById("historySubmenu");
      if (submenu) {
        submenu.style.display = (submenu.style.display === "block") ? "none" : "block";
      }
    }

    document.addEventListener("DOMContentLoaded", function() {
      const sidebar = document.getElementById("sidebarNav");
      setTimeout(() => {
        if (sidebar) sidebar.classList.remove("no-transition");
      }, 50);
    });

    window.onclick = function(event) {
      const dropdown = document.getElementById("userDropdownMenu");
      if (dropdown && dropdown.style.display === "block") {
        dropdown.style.display = "none";
      }
    }
    let profitChart;

    async function loadDashboardGraph() {
      const urlParams = new URLSearchParams(window.location.search);
      const range = urlParams.get('range') || 'today';
      const start = urlParams.get('start') || '';
      const end = urlParams.get('end') || '';
      const response = await fetch(`../Inventory_backend/api_dashboard_graph.php?range=${range}&start_date=${start}&end_date=${end}`);
      const result = await response.json();

      if (!result.success) return;

      const labels = result.profitGraph.map(r => r.day);
      const revenue = result.profitGraph.map(r => r.revenue);
      const profit = result.profitGraph.map(r => r.profit);
      const purchases = result.profitGraph.map(r => r.purchases);

      const canvasElem = document.getElementById("profitChart");
      const chartCtx = canvasElem.getContext('2d');

      const revenueGradient = chartCtx.createLinearGradient(0, 0, 0, 300);
      revenueGradient.addColorStop(0, 'rgba(67, 98, 238, 0.86)');
      revenueGradient.addColorStop(0.7, 'rgba(67, 98, 238, 0.2)');
      revenueGradient.addColorStop(1, 'rgba(67, 97, 238, 0.02)');

      const profitGradient = chartCtx.createLinearGradient(0, 0, 0, 300);
      profitGradient.addColorStop(0, 'rgba(16, 185, 129, 0.86)');
      profitGradient.addColorStop(0.7, 'rgba(16, 185, 129, 0.2)');
      profitGradient.addColorStop(1, 'rgba(16, 185, 129, 0.02)');

      if (profitChart) {
        profitChart.destroy();
      }
      profitChart = new Chart(canvasElem, {
        type: "line",
        data: {
          labels: labels,
          datasets: [{
              label: "Revenue",
              data: revenue,
              borderColor: "#4361ee",
              backgroundColor: revenueGradient,
              fill: true,
              tension: 0.38,
              borderWidth: 2.5,
              pointRadius: 4,
              pointHoverRadius: 6,
              pointBackgroundColor: "#4361ee",
              pointBorderColor: "#4361ee",
              pointBorderWidth: 0
            },
            {
              label: "Net Profit",
              data: profit,
              borderColor: "#10b981",
              backgroundColor: profitGradient,
              fill: true,
              tension: 0.38,
              borderWidth: 2.5,
              pointRadius: 4,
              pointHoverRadius: 6,
              pointBackgroundColor: "#10b981",
              pointBorderColor: "#10b981",
              pointBorderWidth: 0
            },
            {
              label: "Inventory Purchases",
              data: purchases,
              borderColor: "#f59e0b",
              backgroundColor: "rgba(245, 158, 11, 0.12)",
              fill: false,
              tension: 0.38,
              borderWidth: 2.5,
              pointRadius: 4,
              pointHoverRadius: 6,
              pointBackgroundColor: "#f59e0b",
              pointBorderColor: "#f59e0b",
              pointBorderWidth: 0
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          layout: {
            padding: {
              left: 2,
              right: 8,
              top: 4,
              bottom: 8
            }
          },
          plugins: {
            legend: {
              position: "top",
              labels: {
                boxWidth: 10,
                font: {
                  size: 11,
                  weight: '600'
                },
                padding: 12
              }
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  return context.dataset.label + ": Php" + context.raw.toLocaleString();
                }
              }
            }
          },
          scales: {
            x: {
              grid: {
                display: false
              },
              ticks: {
                font: {
                  size: 10
                },
                color: '#64748b',
                maxRotation: 0
              }
            },
            y: {
              beginAtZero: true,
              grid: {
                color: '#cbd5e1',
                lineWidth: 1.2
              },
              ticks: {
                font: {
                  size: 10
                },
                color: '#64748b',
                callback: function(value) {
                  return "Php" + (value >= 1000 ? (value / 1000) + 'k' : value);
                }
              }
            }
          }
        }
      });
    }
    loadDashboardGraph();
  </script>
  <?php require_once 'ai_widget.php'; ?>
  <?php require_once 'stock_alert.php'; ?>
</body>

</html>