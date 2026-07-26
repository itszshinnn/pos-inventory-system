<?php
session_start();

require_once '../src/Database.php';
require_once '../src/ReportManager.php';

$database = new Database();
$pdo = $database->getConnection();
$reportManager = new ReportManager($pdo);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

$metrics = $reportManager->getDashboardMetrics('alltime');
extract($metrics);

$orders = $reportManager->getSalesHistory();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>K's Inventory — Transaction History</title>
  <link rel="stylesheet" href="../assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
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

      .mobile-bottom-spacer {
        display: none !important;
      }

      .history-stats-grid {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 6px !important;
        margin-bottom: 10px !important;
      }

      .history-stats-grid .history-stat-card:nth-child(3) {
        grid-column: span 2 !important;
      }

      .history-stat-card {
        padding: 8px 12px !important;
      }

      .history-stat-card h3 {
        font-size: 11px !important;
        margin-bottom: 2px !important;
      }

      .history-stat-card p {
        font-size: 16px !important;
      }

      .history-toolbar {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 8px !important;
        margin-bottom: 10px !important;
      }

      .history-toolbar input {
        grid-column: span 2 !important;
        width: 100% !important;
      }

      .history-toolbar select {
        width: 100% !important;
      }

      .history-toolbar .export-btn {
        grid-column: span 2 !important;
        width: 100% !important;
      }

      .table-wrap {
        max-height: 390px !important;
        margin-bottom: 75px !important;
      }

      .table-wrap table {
        min-width: 850px;
        table-layout: auto !important;
      }

      .receipt-modal-backdrop {
        z-index: 99999 !important;
        padding: 16px !important;
      }

      .receipt-card {
        width: calc(100% - 24px) !important;
        max-width: 380px !important;
        max-height: 82vh !important;
        overflow-y: auto !important;
        padding: 18px 16px !important;
        box-sizing: border-box !important;
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
      display: flex;
      gap: 12px;
      margin-bottom: 16px;
      align-items: center;
      flex-wrap: wrap;
    }

    .history-toolbar input {
      flex: 1;
      min-width: 200px;
    }

    .history-toolbar select {
      width: 160px;
      flex-shrink: 0;
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

    .badge-more {
      display: inline-block;
      background: #eef4ff;
      color: #4d66ff;
      font-size: 11px;
      font-weight: 700;
      padding: 2px 7px;
      border-radius: 12px;
      margin-left: 4px;
      border: 1px solid #c7d7fe;
      white-space: nowrap;
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
      max-height: 150px;
      overflow-y: auto;
    }

    .receipt-item-line {
      display: flex;
      justify-content: space-between;
      font-size: 14px;
      margin-bottom: 8px;
      color: #333;
    }

    .receipt-item-line span:last-child {
      font-weight: 500;
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
      font-weight: 700;
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

    .table-wrap table {
      width: 100%;
      table-layout: fixed;
    }

    .table-wrap th,
    .table-wrap td {
      white-space: normal !important;
      word-wrap: break-word;
      overflow-wrap: break-word;
    }

    .table-wrap td:nth-child(3),
    .table-wrap th:nth-child(3) {
      max-width: 350px;
    }
  </style>
</head>

<body>

  <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

  <div class="layout">
    <nav class="sidebar no-transition" id="sidebarNav">
      <div class="sidebar-brand">
        <div class="brand-logo-icon">
          <img src="../assets/images/logo.svg" alt="Logo" style="width: 26px; height: 26px; object-fit: contain;">
        </div>
        <div class="brand-text">
          <span class="brand-name">Kinetix</span>
          <span class="brand-sub">Inventory System</span>
        </div>
      </div>

      <span class="sidebar-group-label">Menu</span>
      <a href="dashboard.php"><?php include '../assets/images/dashboard.svg'; ?> <span>Dashboard</span></a>
      <a href="categories.php"><?php include '../assets/images/categories.svg'; ?> <span>Categories</span></a>
      <a href="products.php"><?php include '../assets/images/products.svg'; ?> <span>Products</span></a>
      <a href="purchase_orders.php"><?php include '../assets/images/purchase_orders.svg'; ?> <span>Purchase Orders</span></a>
      <a href="promos.php"><?php include '../assets/images/promos.svg'; ?> <span>Promo Codes</span></a>

      <span class="sidebar-group-label">Reports</span>
      <a href="xml.php"><?php include '../assets/images/backup.svg'; ?> <span>Backup and Restore</span></a>
      <a href="#" onclick="toggleHistorySubmenu(event)" id="historyParentLink"><?php include '../assets/images/history.svg'; ?> <span>History</span></a>
      <div id="historySubmenu" style="display: <?= (in_array(basename($_SERVER['PHP_SELF']), ['history.php', 'product_history.php', 'login_history.php']) ? 'block' : 'none') ?>;">
        <a href="history.php" class="sub-tab<?= (basename($_SERVER['PHP_SELF']) == 'history.php' ? ' active' : '') ?>">Sales History</a>
        <a href="product_history.php" class="sub-tab<?= (basename($_SERVER['PHP_SELF']) == 'product_history.php' ? ' active' : '') ?>">Inventory Logs</a>
        <a href="login_history.php" class="sub-tab<?= (basename($_SERVER['PHP_SELF']) == 'login_history.php' ? ' active' : '') ?>">Log History</a>
      </div>
      <a href="users.php"><?php include '../assets/images/users.svg'; ?> <span>Users</span></a>

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

        <span class="topbar-title"> Sales History</span>

        <div class="topbar-right-group">
          <button id="topbar-ai-btn" onclick="toggleAiChat()" class="topbar-ai-btn"><img src="../assets/images/message.svg" alt="AI" style="width: 15px; height: 15px; object-fit: contain; filter: brightness(0) invert(1); flex-shrink: 0;"> AI Assistant</button>
          <div class="topbar-admin">
            <img src="../assets/images/profile.png" alt="Profile" class="profile-img">
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

        <div class="history-stats-grid">
          <div class="history-stat-card">
            <h3>Total Revenue</h3>
            <p class="green-txt">Php<?= number_format($totalRevenue, 2) ?></p>
          </div>
          <div class="history-stat-card">
            <h3>Transactions</h3>
            <p class="blue-txt"><?= $transactions ?></p>
          </div>
          <div class="history-stat-card">
            <h3>Total Items Sold</h3>
            <p class="blue-txt"><?= number_format($itemsSold ?? 0) ?></p>
          </div>
        </div>

        <div class="history-toolbar">
          <input type="text" id="historySearchInput" placeholder="Search order no, cashier, items..." oninput="filterHistoryTable()">

          <select id="paymentFilter" onchange="filterHistoryTable()">
            <option value="">All Payments</option>
            <option value="cash">Cash</option>
            <option value="gcash">GCash</option>
            <option value="maya">Maya</option>
            <option value="card">Card</option>
          </select>

          <select id="sortFilter" onchange="filterHistoryTable()">
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
            <option value="highest">Highest Amount</option>
            <option value="lowest">Lowest Amount</option>
          </select>

          <button onclick="exportHistoryExcel()" class="export-btn" title="Export current filtered data to Excel">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
            <span>Export Excel</span>
          </button>
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th style="width: 100px;">Order no.</th>
                <th style="width: 150px;">Cashier</th>
                <th>Items Summary</th>
                <th style="width: 150px;">Payment Method</th>
                <th style="width: 120px;">Discount Type</th>
                <th style="width: 110px;">Discount</th>
                <th style="width: 130px;">Total Amount</th>
                <th style="width: 110px; text-align: center;">Actions</th>
              </tr>
            </thead>
            <tbody id="historyTableBody"></tbody>
          </table>
        </div>
        <div class="mobile-bottom-spacer"></div>
      </div>
    </div>
  </div>

  <div class="receipt-modal-backdrop" id="receiptModal">
    <div class="receipt-card">
      <h2>TRANSACTION RECEIPT</h2>
      <div class="receipt-subtitle">K's Inventory System</div>

      <div class="receipt-meta-row">
        <span>Order Number:</span>
        <strong id="rcptOrderNo">#0000</strong>
      </div>
      <div class="receipt-meta-row">
        <span>Cashier:</span>
        <span id="rcptCashier">-</span>
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
        <span>Discount Type:</span>
        <span id="rcptDiscountType">-</span>
      </div>
      <div class="receipt-meta-row">
        <span>Discount applied:</span>
        <span id="rcptDiscount">-</span>
      </div>
      <div class="receipt-total-line">
        <span>TOTAL PAID:</span>
        <span id="rcptTotal">Php0.00</span>
      </div>

      <div class="receipt-meta-row" style="margin-top: 14px;">
        <span>Cash Received:</span>
        <span id="rcptCashReceived" style="font-family: 'DM Mono', monospace; font-weight: 500; color: #444;">Php0.00</span>
      </div>
      <div class="receipt-meta-row">
        <span>Change Given:</span>
        <span id="rcptChange" style="font-family: 'DM Mono', monospace; font-weight: 500; color: #444;">Php0.00</span>
      </div>

      <button class="receipt-close-btn" onclick="closeReceiptModal()">Close Receipt</button>
    </div>
  </div>

  <script>
    const allOrders = <?= json_encode($orders) ?>;

    function exportHistoryExcel() {
      const searchVal = document.getElementById('historySearchInput').value.toLowerCase().trim();
      const paymentVal = document.getElementById('paymentFilter').value;
      const sortVal = document.getElementById('sortFilter').value;

      let filtered = allOrders.filter(order => {
        const orderNo = (order.order_no || '').toLowerCase();
        const items = (order.item || '').toLowerCase();
        const payment = order.payment || '';
        return (orderNo.includes(searchVal) || items.includes(searchVal)) && (paymentVal === "" || payment === paymentVal);
      });

      if (sortVal === 'oldest') {
        filtered.sort((a, b) => parseInt(a.order_no) - parseInt(b.order_no));
      } else if (sortVal === 'highest') {
        filtered.sort((a, b) => parseFloat(b.total) - parseFloat(a.total));
      } else if (sortVal === 'lowest') {
        filtered.sort((a, b) => parseFloat(a.total) - parseFloat(b.total));
      } else {
        filtered.sort((a, b) => parseInt(b.order_no) - parseInt(a.order_no));
      }

      let html = `<html xmlns:o="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">`;
      html += `<head><meta charset="utf-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Sheet1</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>`;
      html += `<body><table><thead><tr>`;
      
      const headers = ["Order No", "Cashier", "Items Summary", "Payment Method", "Discount Type", "Discount", "Total Amount"];
      headers.forEach(h => {
        html += `<th style="background-color: #f2f2f2; border: 1px solid #dddddd; padding: 8px; font-weight: bold;">${h}</th>`;
      });
      html += `</tr></thead><tbody>`;

      filtered.forEach(order => {
        const orderNo = "#" + order.order_no;
        const cashier = order.cashier || 'Unknown';
        const items = order.item || '';
        const payment = order.payment || '';
        const discType = order.discount_type || '-';
        const disc = order.discount || '0.00';
        const total = order.total || '0.00';

        html += `<tr>`;
        html += `<td style="border: 1px solid #dddddd; padding: 8px; mso-number-format:'\\@';">${escapeExcelHtml(orderNo)}</td>`;
        html += `<td style="border: 1px solid #dddddd; padding: 8px;">${escapeExcelHtml(cashier)}</td>`;
        html += `<td style="border: 1px solid #dddddd; padding: 8px;">${escapeExcelHtml(items)}</td>`;
        html += `<td style="border: 1px solid #dddddd; padding: 8px;">${escapeExcelHtml(payment)}</td>`;
        html += `<td style="border: 1px solid #dddddd; padding: 8px;">${escapeExcelHtml(discType)}</td>`;
        html += `<td style="border: 1px solid #dddddd; padding: 8px; mso-number-format:'0\\.00';">${escapeExcelHtml(disc)}</td>`;
        html += `<td style="border: 1px solid #dddddd; padding: 8px; mso-number-format:'0\\.00';">${escapeExcelHtml(total)}</td>`;
        html += `</tr>`;
      });

      html += `</tbody></table></body></html>`;

      const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement("a");
      link.setAttribute("href", url);
      link.setAttribute("download", `sales_history_${new Date().toISOString().slice(0, 10)}.xls`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    function escapeExcelHtml(unsafe) {
      if (unsafe === null || unsafe === undefined) return '';
      return unsafe.toString().replace(/[&<>"']/g, function(m) {
        switch (m) {
          case '&': return '&amp;';
          case '<': return '&lt;';
          case '>': return '&gt;';
          case '"': return '&quot;';
          case "'": return '&#039;';
        }
      });
    }

    function getPaymentBadgeClass(method) {
      if (!method) return 'badge-payment';
      const m = method.toLowerCase();
      if (m === 'gcash') return 'badge-payment gcash';
      if (m === 'maya') return 'badge-payment maya';
      if (m === 'card') return 'badge-payment card';
      return 'badge-payment';
    }

    function formatItemsSummary(itemString) {
      if (!itemString) return 'No items tracked';
      const items = itemString.split(', ');
      if (items.length <= 2) {
        return itemString;
      }
      const preview = items.slice(0, 2).join(', ');
      const extraCount = items.length - 2;
      return `${preview} <span class="badge-more">+${extraCount} more</span>`;
    }

    function filterHistoryTable() {
      const searchVal = document.getElementById('historySearchInput').value.toLowerCase().trim();
      const paymentVal = document.getElementById('paymentFilter').value;
      const sortVal = document.getElementById('sortFilter').value;
      const tbody = document.getElementById('historyTableBody');

      let filtered = allOrders.filter(order => {
        const orderNo = (order.order_no || '').toLowerCase();
        const items = (order.item || '').toLowerCase();
        const payment = order.payment || '';
        return (orderNo.includes(searchVal) || items.includes(searchVal)) && (paymentVal === "" || payment === paymentVal);
      });

      if (sortVal === 'oldest') {
        filtered.sort((a, b) => parseInt(a.order_no) - parseInt(b.order_no));
      } else {
        filtered.sort((a, b) => parseInt(b.order_no) - parseInt(a.order_no));
      }

      if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; color: #aaa; padding: 24px; font-weight: 500;">No matching transaction histories found.</td></tr>`;
        return;
      }

      tbody.innerHTML = filtered.map(order => {
        const discountNum = parseFloat(order.discount) || 0;
        const totalNum = parseFloat(order.total) || 0;
        const discountType = order.discount_type || '-';

        return `
          <tr>
            <td style="font-weight: 700;">#${order.order_no}</td>
            <td>${order.cashier || 'Unknown'}</td>
            <td onclick="openReceiptModal('${order.order_no}')" style="cursor: pointer;" title="Click to view full receipt items">${formatItemsSummary(order.item)}</td>
            <td><span class="${getPaymentBadgeClass(order.payment)}">${order.payment}</span></td>
            <td>${discountType}</td>
            <td>${discountNum > 0 ? `Php${discountNum.toFixed(2)}` : '-'}</td>
            <td class="price-mono">Php${totalNum.toFixed(2)}</td>
            <td style="text-align: center;">
              <button class="view-receipt-btn" onclick="openReceiptModal('${order.order_no}')">View</button>
            </td>
          </tr>
        `;
      }).join('');
    }

    function openReceiptModal(orderNo) {
      const order = allOrders.find(o => o.order_no === orderNo);
      if (!order) return;

      document.getElementById('rcptOrderNo').textContent = `#${order.order_no}`;
      document.getElementById('rcptDate').textContent = order.date;
      document.getElementById('rcptPayment').textContent = order.payment;
      document.getElementById('rcptCashier').textContent = order.cashier || 'Unknown';
      const discountNum = parseFloat(order.discount) || 0;
      document.getElementById('rcptDiscountType').textContent = order.discount_type || 'None';
      document.getElementById('rcptDiscount').textContent = discountNum > 0 ? `- Php${discountNum.toFixed(2)}` : 'None';
      document.getElementById('rcptTotal').textContent = `Php${parseFloat(order.total).toFixed(2)}`;

      const cashReceivedNum = parseFloat(order.cash_received) || 0;
      const changeAmountNum = parseFloat(order.change_amount) || 0;
      document.getElementById('rcptCashReceived').textContent = `Php${cashReceivedNum.toFixed(2)}`;
      document.getElementById('rcptChange').textContent = `Php${changeAmountNum.toFixed(2)}`;

      const itemsBox = document.getElementById('rcptItemsBox');
      itemsBox.innerHTML = '';

      if (order.item) {
        const itemLines = order.item.split(', ');
        itemLines.forEach(line => {
          const row = document.createElement('div');
          row.classList.add('receipt-item-line');

          row.innerHTML = `<span>${line}</span>`;
          itemsBox.appendChild(row);
        });
      }

      document.getElementById('receiptModal').style.display = 'flex';
    }

    function closeReceiptModal() {
      document.getElementById('receiptModal').style.display = 'none';
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
      </div>
    </div>
  </div>
  <?php require_once 'ai_widget.php'; ?>
  <?php require_once 'stock_alert.php'; ?>
</body>

</html>