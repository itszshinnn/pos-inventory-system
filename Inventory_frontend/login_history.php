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

$loginLogs = $reportManager->getLoginLogs();
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
      <a href="history.php" class="sub-tab">Sales History</a>
      <a href="product_history.php" class="sub-tab">Inventory Logs</a>
      <a href="login_history.php" class="sub-tab active">Log History</a>
      <a href="users.php">Users</a>
    </nav>

    <div class="main">

    <div class="history-toolbar">
        <input
            type="text"
            id="historySearchInput"
            placeholder="Search username..."
            oninput="filterHistoryTable()">

        <select id="sortFilter" onchange="filterHistoryTable()">
            <option value="newest">Newest first</option>
            <option value="oldest">Oldest first</option>
        </select>
    </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Login Time</th>
            </tr>
          </thead>
          <tbody id="historyTableBody"></tbody>
        </table>
      </div>

    </div>
  </div>
  <script>
    const allLogs = <?= json_encode($loginLogs) ?>;

    function filterHistoryTable() {

        const search = document
            .getElementById('historySearchInput')
            .value
            .toLowerCase();

        const sort = document
            .getElementById('sortFilter')
            .value;

        const tbody = document.getElementById('historyTableBody');

        let filtered = allLogs.filter(log =>
            log.username.toLowerCase().includes(search)
        );

        filtered.sort((a, b) => {

            if (sort === "oldest")
                return new Date(a.login_time) - new Date(b.login_time);

            return new Date(b.login_time) - new Date(a.login_time);

        });

        if (filtered.length === 0) {

            tbody.innerHTML =
                `<tr>
                    <td colspan="4" style="text-align:center;padding:20px;">
                        No login logs found.
                    </td>
                </tr>`;

            return;
        }

        tbody.innerHTML = filtered.map(log => `
            <tr>
                <td>${log.log_id}</td>
                <td>${log.username}</td>
                <td>${log.login_time}</td>
            </tr>
        `).join("");

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