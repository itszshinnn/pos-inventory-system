<?php
require '../src/Database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K's Inventory — Purchase Orders</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">

    <style>
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
            border: 1px solid #ddd;
        }

        .dropdown-menu a {
            color: #ff4b4b;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-weight: 600;
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

        .table-wrap {
            overflow-x: auto;
            width: 100%;
            border-radius: 8px;
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
                padding-bottom: 40px !important;
            }

            .restock-section {
                padding: 16px 12px !important;
                margin-bottom: 20px !important;
            }

            .table-wrap {
                margin-bottom: 40px !important;
            }

            .table-wrap table {
                min-width: 600px;
            }
        }

        .restock-section {
            background: white;
            padding: 24px;
            border-radius: 16px;
            border: 1px solid #eef0f2;
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .qty-input {
            width: 80px;
            height: 36px;
            padding: 0 10px;
            border-radius: 6px;
            border: 1.5px solid #bcbcbc;
            outline: none;
        }

        .qty-input:focus {
            border-color: #4d66ff;
        }

        .btn-submit {
            background: #4d66ff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 16px;
        }

        .btn-submit:hover {
            background: #3b52d9;
        }

        .btn-receive {
            background: #00ab36;
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-receive:hover {
            background: #008f2d;
        }

        .btn-remove {
            background: #ff4b4b;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-remove:hover {
            background: #e04343;
        }

        .search-container {
            position: relative;
            width: 300px;
            margin-bottom: 16px;
        }

        .search-input {
            width: 100%;
            height: 40px;
            padding: 0 12px;
            border-radius: 8px;
            border: 1.5px solid #bcbcbc;
            outline: none;
            font-size: 14px;
        }

        .search-input:focus {
            border-color: #4d66ff;
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 100;
            display: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .search-item {
            padding: 10px 12px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .search-item:hover {
            background: #f0f4ff;
        }

        .api-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .api-modal-card {
            background: white;
            width: 400px;
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4d66ff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }



        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .api-text {
            font-size: 16px;
            font-weight: 600;
            color: #333;
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
            <a href="purchase_history.php" class="sub-tab active">Purchase History</a>
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
                <a href="logout.php">
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

                <span class="topbar-title">Purchase History</span>

                <div class="topbar-right-group">
                    <button id="topbar-ai-btn" onclick="toggleAiChat()" class="topbar-ai-btn"><img src="../assets/images/message.svg" alt="AI" style="width: 15px; height: 15px; object-fit: contain; filter: brightness(0) invert(1); flex-shrink: 0;"> AI Assistant</button>
                    <div class="topbar-admin">
                        <img src="../assets/images/profile.png" alt="Profile" class="profile-img">
                        Welcome back, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>!
                    </div>
                </div>
            </div>

            <div class="main">
                <div class="restock-section">
                    <div class="section-title">Purchase History</div>

                    <div class="table-wrap">
                        <table style="width:100%; border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th>Purchase Order</th>
                                    <th>Date Ordered</th>
                                    <th>Received By</th>
                                    <th>Total Items</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody id="historyTableBody">
                                <tr>
                                    <td colspan="5">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <div class="api-modal-backdrop" id="simulatedApiModal">
        <div class="api-modal-card">
            <div class="spinner" id="apiSpinner"></div>
            <div class="api-text" id="apiModalText">Establishing secure connection to supplier API...</div>
        </div>
    </div>

    <script>
        function toggleUserDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('userDropdownMenu');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        document.addEventListener('click', function() {
            const dropdown = document.getElementById('userDropdownMenu');
            if (dropdown.style.display === 'block') {
                dropdown.style.display = 'none';
            }
        });

        async function loadPurchaseHistory() {
            try {
                const res = await fetch("../api/api_po.php?type=history");
                const orders = await res.json();
                const tbody = document.getElementById('historyTableBody');
                tbody.innerHTML = '';

                if (orders.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5">No purchase history found.</td></tr>';
                    return;
                }

                orders.forEach(order => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                    <td>
                        <strong>${order.reference_no}</strong>

                        <div style="margin-top:8px;font-size:13px;color:#666;">
                            ${
                                order.product_names
                                    ? order.product_names
                                        .split("|")
                                        .map(item => `• ${item}`)
                                        .join("<br>")
                                    : ""
                            }

                            <div style="margin-top:6px;font-weight:bold;color:#4d66ff;">
                                Total Cost: Php${Number(order.total_cost).toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                })}
                            </div>
                        </div>
                    </td>

                    <td>${new Date(order.created_at).toLocaleDateString()}</td>

                    <td>${order.received_by ?? 'N/A'}</td>

                    <td>${order.total_items}</td>

                        <td>
                        <span style="
                            padding:4px 10px;
                            border-radius:6px;
                            font-weight:bold;
                            background:${order.status === 'Received' ? '#dff6dd' : '#fff3cd'};
                            color:${order.status === 'Received' ? '#198754' : '#856404'};
                        ">
                            ${order.status}
                        </span>
                `;
                    tbody.appendChild(row);
                });
            } catch (error) {
                console.error('Error loading purchase history:', error);
                const tbody = document.getElementById('historyTableBody');
                tbody.innerHTML = '<tr><td colspan="5">Error loading purchase history.</td></tr>';
            }
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
            if (dropdown && dropdown.style.display === "block") dropdown.style.display = "none";
        }

        loadPurchaseHistory();
    </script>
    <?php require_once 'ai_widget.php'; ?>
    <?php require_once 'stock_alert.php'; ?>
</body>

</html>