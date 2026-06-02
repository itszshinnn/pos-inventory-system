<?php
session_start();

require_once '../Database/Database.php';
require_once '../Inventory_backend/ReportManager.php'; 

$database = new Database();
$pdo = $database->getConnection();
$reportManager = new ReportManager($pdo);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Inventory_frontend/login_signup.php");
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
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
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
      <a href="xml.php">XML Files</a>
      <a href="history.php" class="active">History</a>
      <a href="history.php" class="sub-tab">Sales History</a>
      <a href="product_history.php" class="sub-tab active">Inventory Logs</a>
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
        <span id="rcptTotal">₱0.00</span>
      </div>

      <button class="receipt-close-btn" onclick="closeReceiptModal()">Close Receipt</button>
    </div>
  </div>

  <script>

    const allLogs = <?= json_encode($logs) ?>;

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
                const action  = (log.action_type || '').toLowerCase();
                const admin    = (log.admin_name || '').toLowerCase();

                return product.includes(searchVal)
                    || action.includes(searchVal)
                    || admin.includes(searchVal);
            });

            filtered.sort((a, b) => {

                const timeA = new Date(a.created_at).getTime();
                const timeB = new Date(b.created_at).getTime();

                if (sortVal === 'oldest') {
                    return timeA - timeB;
                } else {
                    return timeB - timeA;
                }
            });

            if (filtered.length === 0) {

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

            tbody.innerHTML = filtered.map(log => `
                <tr>
                    <td>#${log.id}</td>
                    <td>${log.product_name}</td>
                    <td>${log.action_type}</td>
                    <td>${log.old_stock ?? '-'}</td>
                    <td>${log.new_stock ?? '-'}</td>
                    <td>${log.admin_name ?? 'Admin'}</td>
                    <td>${log.created_at}</td>
                </tr>
            `).join('');
        }

    function toggleUserDropdown(event) {

        event.stopPropagation();

        const dropdown =
            document.getElementById("userDropdownMenu");

        dropdown.style.display =
            (dropdown.style.display === "block")
            ? "none"
            : "block";
    }

    window.onclick = function() {

        const dropdown =
            document.getElementById("userDropdownMenu");

        if (dropdown &&
            dropdown.style.display === "block") {

            dropdown.style.display = "none";
        }
    }

    filterHistoryTable();

    </script>
</body>
</html>