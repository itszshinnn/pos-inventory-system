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

$metrics = $reportManager->getDashboardMetrics();
extract($metrics);

$logs = $reportManager->getFullInventoryLogs();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>K's Inventory — Transaction History</title>
  <link rel="stylesheet" href="../style.css?v=<?= filemtime(__DIR__ . '/../style.css') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=DM+Mono:wght@400;500&display=swap');

    * {
      font-family: 'DM Sans', 'Segoe UI', sans-serif;
    }

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
      background: rgba(0, 0, 0, 0.5);
      z-index: 1999;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .sidebar-backdrop.active {
      display: block;
      opacity: 1;
    }

    .main {
      overflow-y: hidden !important;
    }

    .table-wrap {
      flex: 1;
      overflow-y: auto !important;
      overflow-x: auto !important;
      min-height: 0;
      border: 1px solid var(--border, #dde1e9);
      border-radius: 8px;
    }

    .table-wrap thead th {
      position: sticky;
      top: 0;
      z-index: 10;
      background: #f5f6f9;
      border-bottom: 2px solid var(--border, #dde1e9);
    }

    @media (max-width: 768px) {
      .sidebar-toggle-btn {
        display: flex;
        padding: 4px;
        margin-right: 2px;
      }

      .topbar {
        padding: 0 10px;
        height: 52px;
        gap: 6px;
      }

      .topbar-admin {
        padding: 4px 8px;
        font-size: 0.82rem;
        margin-right: 4px;
        gap: 6px;
      }

      .profile-img {
        width: 24px;
        height: 24px;
      }

      .topbar-title {
        font-size: 0.85rem;
        font-weight: 700;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
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

      .main {
        width: 100%;
        padding: 12px !important;
        padding-bottom: 0px !important;
      }

      .history-stats-grid {
        display: flex !important;
        flex-direction: column !important;
        gap: 10px !important;
      }

      .history-stat-card {
        padding: 12px !important;
      }

      .history-stat-card h3 {
        font-size: 12px !important;
      }

      .history-stat-card p {
        font-size: 18px !important;
      }

      .history-toolbar {
        display: grid !important;
        grid-template-columns: 1fr auto !important;
        gap: 8px !important;
      }

      .history-toolbar input {
        width: 100% !important;
      }

      .table-wrap {
        max-height: 240px !important;
        margin-bottom: 75px !important;
        padding-bottom: 0px !important;
      }

      .table-wrap table {
        min-width: 650px;
        table-layout: auto !important;
      }
    }



    .history-stats-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
      margin-bottom: 20px;
    }

    .history-stat-card {
      background: white;
      padding: 16px;
      border-radius: 12px;
      border: 1px solid var(--border, #cfcfcf);
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .history-stat-card h3 {
      font-size: 13px;
      font-weight: 700;
      color: #666;
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .history-stat-card p {
      font-size: 22px;
      font-weight: 700;
      color: #1a1a1a;
    }

    .history-stat-card p.green-txt {
      color: #2db84d;
      font-weight: 500;
    }

    .history-stat-card p.blue-txt {
      color: #4d66ff;
    }

    .history-toolbar {
      display: grid;
      grid-template-columns: 1fr 180px 150px;
      gap: 12px;
      margin-bottom: 16px;
    }

    .history-toolbar input,
    .history-toolbar select {
      height: 40px;
      border: 1.5px solid var(--border, #bcbcbc);
      outline: none;
      background: white;
      border-radius: 8px;
      padding: 0 12px;
      font-size: 14px;
      color: #333;
      transition: 0.2s;
    }

    .history-toolbar input:focus,
    .history-toolbar select:focus {
      border-color: #4d66ff;
    }

    .price-mono {
      font-weight: 500;
      color: #000;
    }

    .badge-payment {
      display: inline-block;
      background: #4d66ff;
      color: white;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
    }

    .badge-payment.gcash {
      background: #1a73e8;
    }

    .badge-payment.maya {
      background: #00c483;
    }

    .badge-payment.card {
      background: #ff9f00;
    }

    .view-receipt-btn {
      border: 1.5px solid #4d66ff;
      background: transparent;
      color: #4d66ff;
      padding: 4px 14px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      transition: 0.2s;
    }

    .view-receipt-btn:hover {
      background: #4d66ff;
      color: white;
    }

    .receipt-modal-backdrop {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.4);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 2000;
    }

    .receipt-card {
      background: white;
      width: 380px;
      border-radius: 20px;
      padding: 24px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .receipt-card h2 {
      font-size: 20px;
      font-weight: 700;
      margin-bottom: 4px;
      text-align: center;
    }

    .receipt-subtitle {
      font-size: 13px;
      color: #666;
      text-align: center;
      margin-bottom: 20px;
    }

    .receipt-meta-row {
      display: flex;
      justify-content: space-between;
      font-size: 13px;
      color: #444;
      margin-bottom: 6px;
    }

    .receipt-divider {
      border: none;
      border-top: 1px dashed #bbb;
      margin: 14px 0;
    }

    .receipt-items-box {
      margin: 10px 0;
    }

    .receipt-item-line {
      display: flex;
      justify-content: space-between;
      font-size: 14px;
      margin-bottom: 8px;
      color: #333;
    }

    .receipt-item-line span:last-child {
      font-family: 'DM Mono', monospace;
    }

    .receipt-total-line {
      display: flex;
      justify-content: space-between;
      font-size: 18px;
      font-weight: 700;
      color: #000;
      margin-top: 10px;
    }

    .receipt-total-line span:last-child {
      font-family: 'DM Mono', monospace;
    }

    .receipt-close-btn {
      width: 100%;
      height: 42px;
      background: #333538;
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 20px;
      transition: 0.2s;
    }

    .receipt-close-btn:hover {
      background: #1a1a1a;
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
      <a href="dashboard.php"><?php include '../Images/dashboard.svg'; ?> <span>Dashboard</span></a>
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
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
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

        <span class="topbar-title">Inventory Logs</span>

        <div class="topbar-right-group">
          <button id="topbar-ai-btn" onclick="toggleAiChat()" class="topbar-ai-btn"><img src="../Images/message.svg" alt="AI" style="width: 15px; height: 15px; object-fit: contain; filter: brightness(0) invert(1); flex-shrink: 0;"> AI Assistant</button>
          <div class="topbar-admin">
            <img src="../Images/profile.png" alt="Profile" class="profile-img">
            Welcome back, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>!
          </div>
        </div>
      </div>

      <div class="main">

        <?php if (isset($errorMsg)): ?>
          <div style="background: #fff0f0; border: 1px solid #ffbcbc; color: #ff4b4b; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            <strong>Database Notice:</strong> <?= htmlspecialchars($errorMsg) ?>
          </div>
        <?php endif; ?>

        <div class="history-stats-grid">

          <div class="history-stat-card">
            <h3>Total Logs</h3>
            <p class="blue-txt">
              <?= $totalLogs ?>
            </p>
          </div>

          <div class="history-stat-card">
            <h3>Products Added</h3>
            <p class="green-txt">
              <?= $totalAdded ?>
            </p>
          </div>

          <div class="history-stat-card">
            <h3>Products Deleted</h3>
            <p>
              <?= $totalDeleted ?>
            </p>
          </div>

        </div>
        <div class="history-toolbar">
          <input type="text" id="historySearchInput" placeholder="Search inventory logs..." oninput="filterHistoryTable()">
          <select id="sortFilter" onchange="filterHistoryTable()">
            <option value="newest">Newest first</option>
            <option value="oldest">Oldest first</option>
          </select>
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Log ID</th>
                <th>Product</th>
                <th>Action</th>
                <th>Old Stock</th>
                <th>New Stock</th>
                <th>Admin</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody id="historyTableBody"></tbody>
          </table>
        </div>

      </div>
    </div>
  </div>

  <div class="receipt-modal-backdrop" id="receiptModal">
    <div class="receipt-card">
      <h2>TRANSACTION RECEIPT</h2>
      <div class="receipt-subtitle">K's Inventory System Ledger</div>

      <div class="receipt-meta-row">
        <span>Order Number:</span>
        <strong id="rcptOrderNo">#0000</strong>
      </div>
      <div class="receipt-meta-row">
        <span>Date/Time:</span>
        <span id="rcptDate">-</span>
      </div>
      <div class="receipt-meta-row">
        <span>Payment Method:</span>
        <span id="rcptPayment">-</span>
      </div>

      <hr class="receipt-divider">

      <div class="receipt-items-box" id="rcptItemsBox"></div>

      <hr class="receipt-divider">

      <div class="receipt-meta-row">
        <span>Discount applied:</span>
        <span id="rcptDiscount">-</span>
      </div>
      <div class="receipt-total-line">
        <span>TOTAL PAID:</span>
        <span id="rcptTotal">Php0.00</span>
      </div>

      <button class="receipt-close-btn" onclick="closeReceiptModal()">Close Receipt</button>
    </div>
  </div>

  <script>
    const allLogs = <?= json_encode($logs) ?>;

    function groupLogs(logsArray) {
      const groups = {};

      logsArray.forEach(log => {
        const key = `${log.created_at}_${log.action_type}_${log.changed_by ?? 'Admin'}`;

        if (!groups[key]) {
          groups[key] = {
            id: log.id,
            action_type: log.action_type,
            changed_by: log.changed_by,
            created_at: log.created_at,
            items: []
          };
        }

        groups[key].items.push({
          product_name: log.product_name,
          old_stock: log.old_stock,
          new_stock: log.new_stock
        });
      });

      return Object.values(groups);
    }

    function filterHistoryTable() {
      const searchVal = document
        .getElementById('historySearchInput')
        .value
        .toLowerCase()
        .trim();

      const sortVal = document
        .getElementById('sortFilter')
        .value;

      const tbody = document.getElementById('historyTableBody');

      let filtered = allLogs.filter(log => {
        const product = (log.product_name || '').toLowerCase();
        const action = (log.action_type || '').toLowerCase();
        const admin = (log.changed_by || '').toLowerCase();

        return product.includes(searchVal) ||
          action.includes(searchVal) ||
          admin.includes(searchVal);
      });

      let grouped = groupLogs(filtered);

      grouped.sort((a, b) => {
        const timeA = new Date(a.created_at).getTime();
        const timeB = new Date(b.created_at).getTime();

        if (sortVal === 'oldest') {
          return timeA - timeB;
        } else {
          return timeB - timeA;
        }
      });

      if (grouped.length === 0) {
        tbody.innerHTML = `
                    <tr>
                        <td colspan="7"
                            style="text-align:center;
                                padding:24px;
                                color:#999;">
                            No inventory logs found.
                        </td>
                    </tr>
                `;
        return;
      }

      tbody.innerHTML = grouped.map(group => {
        let productMarkup = '';
        let oldStockMarkup = '';
        let newStockMarkup = '';

        if (group.items.length === 1) {
          const item = group.items[0];
          productMarkup = item.product_name;
          oldStockMarkup = item.old_stock ?? '-';
          newStockMarkup = item.new_stock ?? '-';
        } else {
          productMarkup = `<ul style="list-style-type: none; margin: 0; text-align: center;">` +
            group.items.map(item => `<li>${item.product_name}</li>`).join('') + `</ul>`;

          oldStockMarkup = `<ul style="list-style-type: none; margin: 0; padding: 0;">` +
            group.items.map(item => `<li>${item.old_stock ?? '-'}</li>`).join('') + `</ul>`;

          newStockMarkup = `<ul style="list-style-type: none; margin: 0; padding: 0;">` +
            group.items.map(item => `<li>${item.new_stock ?? '-'}</li>`).join('') + `</ul>`;
        }

        return `
                <tr>
                    <td>#${group.id}</td>
                    <td>${productMarkup}</td>
                    <td><span style="font-weight: 600;">${group.action_type}</span></td>
                    <td>${oldStockMarkup}</td>
                    <td>${newStockMarkup}</td>
                    <td>${group.changed_by ?? 'Admin'}</td>
                    <td>${group.created_at}</td>
                </tr>
            `;
      }).join('');
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
      const sidebarState = sessionStorage.getItem("sidebarState");
      const sidebar = document.getElementById("sidebarNav");
      const backdrop = document.getElementById("sidebarBackdrop");
      if (sidebarState === "active") {
        if (sidebar) sidebar.classList.add("active");
        if (backdrop) backdrop.classList.add("active");
      }
      setTimeout(() => {
        if (sidebar) sidebar.classList.remove("no-transition");
      }, 50);
    });

    window.onclick = function() {
      const dropdown = document.getElementById("userDropdownMenu");
      if (dropdown && dropdown.style.display === "block") {
        dropdown.style.display = "none";
      }
    }

    filterHistoryTable();
  </script>
  <?php require_once 'ai_widget.php'; ?>
  <?php require_once 'stock_alert.php'; ?>
</body>

</html>