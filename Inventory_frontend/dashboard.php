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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>K's Inventory — Dashboard</title>
  <link rel="stylesheet" href="../style.css">

  <style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=DM+Mono:wght@400;500&display=swap');

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

    .icon-products {
      background: #5c7fe0;
    }

    .icon-units {
      background: #00ab36;
    }

    .icon-cats {
      background: #9729ff;
    }

    .icon-low {
      background: #ff8800;
    }

    .icon-out {
      background: #bc1a1a;
    }

    .dashboard-container {
      display: grid;
      grid-template-columns: 1fr 320px;
      gap: 20px;
      align-items: start;
    }

    .dashboard-left-content {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    .dashboard-left-content .stat-card {
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
    }

    .dashboard-left-content .stat-icon {
      width: 52px;
    }

    .dashboard-left-content .stat-icon svg {
      width: 24px;
      height: 24px;
    }

    .dashboard-left-content .stat-body {
      padding: 10px 14px;
    }

    .dashboard-left-content .stat-body .num {
      font-size: 1.4rem;
    }

    .dashboard-left-content .stat-body .label {
      font-size: 0.78rem;
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
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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

    .notifications-sidebar {
      background: #ffffff;
      border-radius: 16px;
      padding: 20px;
      border: 1px solid #eef0f2;
      min-height: 100%;
      max-height: 520px;
      overflow-y: auto;
    }

    .notif-item {
      background: #f7f7f7;
      border-radius: 10px;
      padding: 12px 14px;
      margin-bottom: 10px;
      font-size: 14px;
      color: #333;
      line-height: 1.5;
      border: 1px solid #ececec;
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

    .stock-alert-overlay {
      position: fixed;
      inset: 0;

      display: none;

      justify-content: center;
      align-items: center;

      background: rgba(0, 0, 0, 0.25);

      z-index: 9999;
    }

    .stock-alert {

      height: 350px;

      width: 550px;

      max-width: 90%;

      background: #fff;

      border-left: 5px solid #ff9800;
      border-radius: 10px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, .2);

      padding: 34px;
      display: flex;
      flex-direction: column;
      animation: popupFade .25s ease;
    }

    .stock-alert h4 {
      font-size: 24px;
      margin-bottom: 12px;
    }

    .stock-alert p {
      font-size: 16px;
      margin: 12px 0;
    }

    .stock-alert ul {
      font-size: 15px;
      max-height: 220px;
      overflow-y: auto;
    }

    .stock-alert-buttons {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: auto;
    }

    .stock-alert-buttons button {
      border: none;
      padding: 8px 15px;
      cursor: pointer;
      border-radius: 6px;
      font-weight: bold;
    }

    .stock-alert-buttons button:first-child {
      background: #ddd;
    }

    .stock-alert-buttons button:last-child {
      background: #2E8B57;
      color: white;
    }

    .graph-card {
      background: #fff;
      border-radius: 12px;
      padding: 20px;
      position: relative;
      height: 420px;
      margin-top: 20px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
    }

    .graph-card h3 {
      margin-bottom: 15px;
    }

    .graph-container {
      display: grid;
      grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
      gap: 20px;
      width: 100%;
    }

    .revenue-card {
      grid-column: span 2;
    }

    @keyframes popupFade {

      from {
        opacity: 0;
        transform: scale(.9);
      }

      to {
        opacity: 1;
        transform: scale(1);
      }

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
      <a href="dashboard.php">Dashboard</a>
      <a href="categories.php">Categories</a>
      <a href="products.php">Products</a>
      <a href="purchase_orders.php">Purchase Orders</a>
      <a href="xml.php">Backup & Restore</a>
      <a href="history.php">History</a>
      <a href="users.php">Users</a>
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

          <div class="dashboard-section">
            <div class="section-title">Today's Summary</div>

            <div class="metrics-row-grid" style="grid-template-columns:repeat(4,1fr);">

              <div class="metric-sub-card">
                <h3>Today's Revenue</h3>
                <p class="green-txt">
                  Php<?= number_format($todayRevenue, 2) ?>
                </p>
              </div>

              <div class="metric-sub-card">
                <h3>Today's COGS</h3>
                <p style="color:#ef4444;font-family:'DM Mono',monospace;">
                  Php<?= number_format($todayCOGS, 2) ?>
                </p>
              </div>

              <div class="metric-sub-card">
                <h3>Today's Profit</h3>
                <p style="color:#22c55e;font-family:'DM Mono',monospace;">
                  Php<?= number_format($todayProfit, 2) ?>
                </p>
              </div>

              <div class="metric-sub-card">
                <h3>Today's Sales</h3>
                <p class="blue-txt">
                  <?= $todayTransactions ?>
                </p>
              </div>

            </div>
          </div>

          <div class="dashboard-section">
            <div class="section-title">Overall Statistics</div>

            <div class="metrics-row-grid">

              <div class="metric-sub-card">
                <h3>Total Revenue</h3>
                <p class="green-txt">
                  Php<?= number_format($totalRevenue, 2) ?>
                </p>
              </div>

              <div class="metric-sub-card">
                <h3>Transactions</h3>
                <p class="blue-txt">
                  <?= $transactions ?>
                </p>
              </div>

              <div class="metric-sub-card">
                <h3>Items Sold</h3>
                <p>
                  <?= $itemsSold ?>
                </p>
              </div>

            </div>
          </div>

          <div class="dashboard-section">
            <div class="section-title">Total Revenue and Stuff</div>
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
      <div class="graph-container">

        <div class="graph-card revenue-card">
          <h3>Revenue & Profit Overview</h3>
          <canvas id="profitChart"></canvas>
        </div>

        <div class="graph-card cogs-card">
          <h3>Cost of Goods Sold</h3>
          <canvas id="cogsChart"></canvas>
        </div>

        <div class="graph-card top-selling-card">
          <h3>Top Selling Products</h3>
          <canvas id="topSellingChart"></canvas>
        </div>

      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    let profitChart;
    let cogsChart;
    let topSellingChart;

    async function loadDashboardGraph() {

      const response = await fetch('../Inventory_backend/api_dashboard_graph.php');
      const result = await response.json();

      if (!result.success) return;

      const labels = result.profitGraph.map(r => r.day);
      const revenue = result.profitGraph.map(r => r.revenue);
      const profit = result.profitGraph.map(r => r.profit);
      const cogs = result.profitGraph.map(r => r.cost);
      const purchases = result.profitGraph.map(r => r.purchases);

      const ctx = document.getElementById("profitChart");
      if (profitChart) {
        profitChart.destroy();
      }
      profitChart = new Chart(ctx, {
        type: "line",

        data: {
          labels: labels,

          datasets: [{
              label: "Revenue",
              data: revenue,

              borderColor: "#3b82f6",
              backgroundColor: "rgba(59,130,246,0.20)",

              fill: true,
              tension: 0.4,

              borderWidth: 3,
              pointRadius: 4
            },

            {
              label: "Profit",
              data: profit,

              borderColor: "#22c55e",
              backgroundColor: "rgba(34,197,94,0.20)",

              fill: true,
              tension: 0.4,

              borderWidth: 3,
              pointRadius: 4
            },
            {
              label: "Inventory Purchases",
              data: purchases,

              borderColor: "#f59e0b",
              backgroundColor: "rgba(245,158,11,.18)",

              fill: true,
              tension: 0.4,

              borderWidth: 3,
              pointRadius: 4
            }
          ]
        },

        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: "top"
            },

            tooltip: {
              callbacks: {
                label: function(context) {
                  return context.dataset.label + ": Php" +
                    context.raw.toLocaleString();
                }
              }
            }
          },

          scales: {
            y: {
              beginAtZero: true,

              ticks: {
                callback: function(value) {
                  return "Php" + value.toLocaleString();
                }
              }
            }
          }
        }
      });
      const cogsCtx = document.getElementById("cogsChart");


      if (cogsChart) {
        cogsChart.destroy();
      }


      cogsChart = new Chart(cogsCtx, {

        type: "bar",

        data: {

          labels: labels,

          datasets: [

            {
              label: "COGS",

              data: cogs,

              backgroundColor: "#ef4444",

              borderRadius: 8
            }

          ]

        },


        options: {

          responsive: true,
          maintainAspectRatio: false,

          plugins: {

            legend: {
              position: "top"
            },


            tooltip: {
              callbacks: {
                label: function(context) {

                  return "COGS: Php" +
                    context.raw.toLocaleString();

                }
              }
            }

          },


          scales: {

            y: {

              beginAtZero: true,

              ticks: {

                callback: function(value) {

                  return "Php" +
                    value.toLocaleString();

                }

              }

            }

          }

        }

      });
      const productNames = result.topProducts.map(p => p.name);
      const soldQty = result.topProducts.map(p => p.sold);
      const topCtx = document.getElementById("topSellingChart");

      if (topSellingChart) {
        topSellingChart.destroy();
      }
      topSellingChart = new Chart(topCtx, {
        type: "bar",
        data: {
          labels: productNames,
          datasets: [{
            label: "Units Sold",
            data: soldQty,
            backgroundColor: "#5470ff"
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                precision: 0
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