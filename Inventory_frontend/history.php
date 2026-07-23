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

$orders = $reportManager->getSalesHistory();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>K's Inventory — Transaction History</title>
  <link rel="stylesheet" href="../style.css">
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

    .sidebar a.sub-tab {
      padding-left: 28px;
      font-size: 0.88rem;
      color: #bcbcbc;
    }

    .sidebar a.sub-tab::before {
      content: "• ";
      color: #666;
      margin-right: 4px;
    }

    .sidebar a.sub-tab.active::before {
      color: #4d66ff;
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
      font-family: 'DM Mono', monospace;
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
      font-family: 'DM Mono', monospace;
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

  <div class="topbar">
    <div class="topbar-admin" onclick="toggleUserDropdown(event)">
      <img src="../Images/profile.png" alt="Profile" class="profile-img">
      <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?> ▼

      <div id="userDropdownMenu" class="dropdown-menu">
        <a href="../Inventory_frontend/logout.php">Logout</a>
      </div>
    </div>
    <span class="topbar-title">K's Inventory System</span>
  </div>

  <div class="layout">
    <nav class="sidebar">
      <a href="dashboard.php">Dashboard</a>
      <a href="categories.php">Categories</a>
      <a href="products.php">Products</a>
      <a href="purchase_orders.php">Purchase Orders</a>
      <a href="xml.php">Backup & Restore</a>
      <a href="history.php" class="active">History</a>
      <a href="history.php" class="sub-tab active">Sales History</a>
      <a href="product_history.php" class="sub-tab">Inventory Logs</a>
      <a href="login_history.php" class="sub-tab">Log History</a>
      <a href="users.php">Users</a>
    </nav>

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
          <h3>Items Sold</h3>
          <p><?= $itemsSold ?></p>
        </div>
      </div>

      <div class="history-toolbar">
        <input type="text" id="historySearchInput" placeholder="Search order records..." oninput="filterHistoryTable()">
        <select id="paymentFilter" onchange="filterHistoryTable()">
          <option value="">All Payments</option>
          <option value="Cash">Cash</option>
          <option value="GCash">GCash</option>
          <option value="Maya">Maya</option>
          <option value="Card">Card</option>
        </select>
        <select id="sortFilter" onchange="filterHistoryTable()">
          <option value="newest">Newest first</option>
          <option value="oldest">Oldest first</option>
        </select>
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

    function getPaymentBadgeClass(method) {
      if (!method) return 'badge-payment';
      const m = method.toLowerCase();
      if (m === 'gcash') return 'badge-payment gcash';
      if (m === 'maya') return 'badge-payment maya';
      if (m === 'card') return 'badge-payment card';
      return 'badge-payment';
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
            <td>${order.item ? order.item : 'No items tracked'}</td>
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