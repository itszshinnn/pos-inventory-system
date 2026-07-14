<?php
require '../Database/Database.php';
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
    <link rel="stylesheet" href="../style.css">

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

    <div class="topbar">
        <div class="topbar-admin" onclick="toggleUserDropdown(event)">
            <img src="../Images/profile.png" alt="Profile" class="profile-img">
            <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?> ▼
            <div id="userDropdownMenu" class="dropdown-menu">
                <a href="logout.php">Logout</a>
            </div>
        </div>
        <span class="topbar-title">K's Inventory System</span>
    </div>

    <div class="layout">
        <nav class="sidebar">
            <a href="dashboard.php">Dashboard</a>
            <a href="categories.php">Categories</a>
            <a href="products.php">Products</a>
            <a href="purchase_orders.php" class="active">Purchase Orders</a>
            <a href="purchase_history.php" class="sub-tab active">Purchase History</a>
            <a href="xml.php">XML Files</a>
            <a href="history.php">History</a>
            <a href="users.php">Users</a>
        </nav>

        <div class="main">
            <div class="restock-section">
                <div class="section-title">Purchase History</div>

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
                const res = await fetch("../Inventory_backend/api_po.php?type=history");
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
                                Total Cost: ₱${Number(order.total_cost).toLocaleString(undefined, {
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

        loadPurchaseHistory();
    </script>
</body>

</html>