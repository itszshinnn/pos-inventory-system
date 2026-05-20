<?php
require '../Database/config.php';

session_start();

// Block users who aren't logged in, OR who are logged in but aren't an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Inventory_frontend/login_signup.php");
    exit;
}

try {
    // 1. Fetch Dynamic Dashboard Metric Summaries
    $totalRevenue = $pdo->query('SELECT COALESCE(SUM(total_amount), 0) FROM orders')->fetchColumn();
    $transactions = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
    $itemsSold    = $pdo->query('SELECT COALESCE(SUM(quantity), 0) FROM order_items')->fetchColumn();

    // 2. Query the Orders Ledger with Aggregated Item Summaries
    $query = 'SELECT o.order_no, 
                     o.payment_method AS payment, 
                     o.discount_amount AS discount, 
                     o.total_amount AS total,
                     GROUP_CONCAT(CONCAT(p.name, " x", oi.quantity) SEPARATOR ", ") AS item
              FROM orders o
              LEFT JOIN order_items oi ON o.id = oi.order_id
              LEFT JOIN products p ON oi.product_id = p.id
              GROUP BY o.id
              ORDER BY o.id DESC';
              
    $stmt = $pdo->query($query);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    // Graceful error fallbacks if database tables haven't been migrated yet
    $totalRevenue = 0.00;
    $transactions = 0;
    $itemsSold = 0;
    $orders = [];
    $errorMsg = $e->getMessage();
}
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

    /* Unify application font mappings explicitly */
    * {
      font-family: 'DM Sans', 'Segoe UI', sans-serif;
    }

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

    /* CONTROL TOOLBAR FILTER PANEL */
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

    /* TABLE LAYOUT ADJUSTMENTS */
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

    /* Dynamic styling colors for distinct payment types */
    .badge-payment.gcash { background: #1a73e8; }
    .badge-payment.maya { background: #00c483; }
    .badge-payment.card { background: #ff9f00; }

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
      <a href="history.php" class="active">History</a> 
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
          <p class="green-txt">₱<?= number_format($totalRevenue, 2) ?></p>
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
              <th>Items Summary</th>
              <th style="width: 150px;">Payment Method</th>
              <th style="width: 110px;">Discount</th>
              <th style="width: 130px;">Total Amount</th>
              <th style="width: 110px; text-align: center;">Actions</th>
            </tr>
          </thead>
          <tbody id="historyTableBody">
            </tbody>
        </table>
      </div>

    </div>
  </div>

  <script>
    // Inject the database PHP data directly into a JavaScript array safely
    const allOrders = <?= json_encode($orders) ?>;

    // Helper utility to style payment badges based on method string
    function getPaymentBadgeClass(method) {
      if (!method) return 'badge-payment';
      const m = method.toLowerCase();
      if (m === 'gcash') return 'badge-payment ghtml gcash';
      if (m === 'maya') return 'badge-payment maya';
      if (m === 'card') return 'badge-payment card';
      return 'badge-payment';
    }

    function filterHistoryTable() {
      const searchVal  = document.getElementById('historySearchInput').value.toLowerCase().trim();
      const paymentVal = document.getElementById('paymentFilter').value;
      const sortVal    = document.getElementById('sortFilter').value;
      const tbody      = document.getElementById('historyTableBody');

      // 1. Core Evaluation Loop Filtering Logic
      let filtered = allOrders.filter(order => {
        const orderNo = (order.order_no || '').toLowerCase();
        const items   = (order.item || '').toLowerCase();
        const payment = order.payment || '';

        const matchesSearch  = orderNo.includes(searchVal) || items.includes(searchVal);
        const matchesPayment = paymentVal === "" || payment === paymentVal;

        return matchesSearch && matchesPayment;
      });

      // 2. Sorting Arrangement Operations
      if (sortVal === 'oldest') {
        // Arranges array incrementally from #0001 upwards
        filtered.sort((a, b) => parseInt(a.order_no) - parseInt(b.order_no));
      } else {
        // Default: Newest first (descending order layout tracking)
        filtered.sort((a, b) => parseInt(b.order_no) - parseInt(a.order_no));
      }

      // 3. Render HTML Rows to Table Document Body Container Partition
      if (filtered.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="6" style="text-align: center; color: #aaa; padding: 24px; font-weight: 500;">
              No matching transaction histories found.
            </td>
          </tr>`;
        return;
      }

      tbody.innerHTML = filtered.map(order => {
        const discountNum = parseFloat(order.discount) || 0;
        const totalNum    = parseFloat(order.total) || 0;
        
        const discountDisplay = discountNum > 0 ? `₱${discountNum.toFixed(2)}` : '-';
        const totalDisplay    = `₱${totalNum.toFixed(2)}`;
        const itemDisplay     = order.item ? order.item : 'No items tracked';

        return `
          <tr>
            <td style="font-weight: 700;">#${order.order_no}</td>
            <td>${itemDisplay}</td>
            <td><span class="${getPaymentBadgeClass(order.payment)}">${order.payment}</span></td>
            <td>${discountDisplay}</td>
            <td class="price-mono">${totalDisplay}</td>
            <td style="text-align: center;">
              <button class="view-receipt-btn">View</button>
            </td>
          </tr>
        `;
      }).join('');
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

    // Initialize/Render table contents on page boot load context
    filterHistoryTable();
  </script>
</body>

</html>