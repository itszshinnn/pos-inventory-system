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
                padding-bottom: 90px !important;
            }

            .restock-section {
                padding: 16px 12px !important;
                margin-bottom: 20px !important;
            }

            .search-container {
                width: 100% !important;
            }

            .table-wrap table {
                min-width: 600px;
            }

            .btn-submit {
                width: 100% !important;
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
</head><body>

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
            <a href="purchase_orders.php" class="active"><?php include '../assets/images/purchase_orders.svg'; ?> <span>Purchase Orders</span></a>
            <a href="purchase_history.php" class="sub-tab">Purchase History</a>
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

                <span class="topbar-title">Purchase Orders</span>

                <div class="topbar-right-group">
                    <button id="topbar-ai-btn" onclick="toggleAiChat()" class="topbar-ai-btn"><img src="../assets/images/message.svg" alt="AI" style="width: 15px; height: 15px; object-fit: contain; filter: brightness(0) invert(1); flex-shrink: 0;"> AI Assistant</button>
                    <div class="topbar-admin">
                        <img src="../assets/images/profile.png" alt="Profile" class="profile-img">
                        <span class="topbar-admin-text"><span class="welcome-prefix">Welcome back, </span><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>!</span>
                    </div>
                </div>
            </div>

            <div class="main">

                <div class="restock-section">
                    <div class="restock-card">
                        <div class="restock-header">
                            <div>
                                <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b;">DRAFT PURCHASE ORDER</h3>
                                <p style="margin: 4px 0 0 0; font-size: 0.85rem; color: #64748b;">Add items to your supplier order draft manually or review critical alerts.</p>
                            </div>
                        </div>

                        <div class="search-container" style="margin-top: 14px;">
                            <input type="text" id="manualSearch" class="search-input" placeholder="Search product to add..." oninput="handleSearch()">
                            <div id="searchResults" class="search-results"></div>
                        </div>

                        <div class="table-wrap">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f8fafc; text-align: left;">
                                        <th style="padding: 12px; border-bottom: 1px solid #ddd;">Product Name</th>
                                        <th style="padding: 12px; border-bottom: 1px solid #ddd;">Current Stock</th>
                                        <th style="padding: 12px; border-bottom: 1px solid #ddd;">Unit Price</th>
                                        <th style="padding: 12px; border-bottom: 1px solid #ddd;">Order Quantity</th>
                                        <th style="padding: 12px; border-bottom: 1px solid #ddd;">Total</th>
                                        <th style="padding: 12px; border-bottom: 1px solid #ddd; width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="draftTableBody">
                                    <tr>
                                        <td colspan="6" style="padding: 12px; text-align: center; color: #888;">Draft is empty. Add items to begin.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 16px; margin-top: 16px;">
                            <button id="submitOrderBtn" class="btn-submit" onclick="submitPurchaseOrder()" style="display: none;">Submit Draft Purchase Order</button>
                        </div>
                    </div>
                </div>

                <div class="restock-section" style="margin-top: 20px;">
                    <div class="restock-card">
                        <div class="restock-header">
                            <div>
                                <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b;">PENDING INCOMING ORDERS</h3>
                            </div>
                        </div>

                        <div class="table-wrap" style="margin-top: 14px;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f8fafc; text-align: left;">
                                        <th style="padding: 12px; border-bottom: 1px solid #ddd;">Purchase Orders</th>
                                        <th style="padding: 12px; border-bottom: 1px solid #ddd;">Date Drafted</th>
                                        <th style="padding: 12px; border-bottom: 1px solid #ddd;">Total Items</th>
                                        <th style="padding: 12px; border-bottom: 1px solid #ddd; width: 150px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="pendingTableBody">
                                    <tr>
                                        <td colspan="4" style="padding: 12px; text-align: center; color: #888;">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="mobile-bottom-spacer"></div>
        </div>
    </div>

    <div class="api-modal-backdrop" id="simulatedApiModal">
        <div class="api-modal-card">
            <div class="spinner" id="apiSpinner"></div>
            <div class="api-text" id="apiModalText">Establishing secure connection to supplier API...</div>
        </div>
    </div>

    <script>
        let allProducts = [];
        let draftItems = [];

        async function init() {
            const res = await fetch('../api/api_products.php');
            allProducts = await res.json();

            await loadDraftItemsFromURL();
            await loadPendingOrders();
        }

        async function loadDraftItemsFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            const itemsParam = urlParams.get('items');

            if (itemsParam) {
                const itemIds = itemsParam.split(',');
                const newItems = allProducts.filter(p => itemIds.includes(p.id.toString()));

                newItems.forEach(newItem => {
                    if (!draftItems.find(item => item.id === newItem.id)) {
                        draftItems.push(newItem);
                    }
                });

                window.history.replaceState({}, document.title, window.location.pathname);
            }
            renderDraftTable();
        }

        function handleSearch() {
            const query = document.getElementById('manualSearch').value.toLowerCase();
            const resultsBox = document.getElementById('searchResults');

            if (query.length === 0) {
                resultsBox.style.display = 'none';
                return;
            }

            const filtered = allProducts.filter(p => p.name.toLowerCase().includes(query));

            if (filtered.length === 0) {
                resultsBox.innerHTML = '<div class="search-item" style="color:#888;">No products found</div>';
            } else {
                resultsBox.innerHTML = filtered.map(p => `
                <div class="search-item" onclick="addItemToDraft(${p.id})">
                    <strong>${p.name}</strong> (Stock: ${p.stock})
                </div>
            `).join('');
            }
            resultsBox.style.display = 'block';
        }

        function addItemToDraft(id) {
            if (!draftItems.find(item => item.id === id)) {
                const product = allProducts.find(p => p.id === id);
                draftItems.push(product);
                renderDraftTable();
            }
            document.getElementById('manualSearch').value = '';
            document.getElementById('searchResults').style.display = 'none';
        }

        function removeItemFromDraft(id) {
            draftItems = draftItems.filter(item => item.id !== id);
            renderDraftTable();
        }

        function renderDraftTable() {
            const tbody = document.getElementById('draftTableBody');
            const submitBtn = document.getElementById('submitOrderBtn');

            if (draftItems.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="padding: 12px; text-align: center; color: #888;">Draft is empty. Add items to begin.</td></tr>';
                submitBtn.style.display = 'none';
                return;
            }

            let grandTotal = 0;

            tbody.innerHTML = draftItems.map(item => {
                const costPrice = item.price_bought ? parseFloat(item.price_bought) : 0.00;
                const defaultQty = 20;
                const lineTotal = costPrice * defaultQty;
                grandTotal += lineTotal;

                return `
      <tr>
        <td style="padding: 12px; border-bottom: 1px solid #eee;"><strong>${item.name}</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #eee; font-weight: bold; color: ${item.stock <= 3 ? 'red' : '#333'}">${item.stock}</td>
        <td style="padding: 12px; border-bottom: 1px solid #eee;">Php${costPrice.toFixed(2)}</td>
        <td style="padding: 12px; border-bottom: 1px solid #eee;">
          <input type="number" class="qty-input draft-qty" data-id="${item.id}" data-cost="${costPrice}" min="1" value="${defaultQty}" oninput="updateTotals()">
        </td>
        <td style="padding: 12px; border-bottom: 1px solid #eee; font-weight: bold;" class="line-total" id="total-${item.id}">Php${lineTotal.toFixed(2)}</td>
        <td style="padding: 12px; border-bottom: 1px solid #eee;">
          <button class="btn-remove" onclick="removeItemFromDraft(${item.id})">Remove</button>
        </td>
      </tr>
    `
            }).join('');

            tbody.innerHTML += `
        <tr style="background: #f8f9fb;">
            <td colspan="4" style="text-align: right; padding: 12px;"><strong>Total Supplier Cost:</strong></td>
            <td colspan="2" style="padding: 12px; font-weight: bold; color: #4d66ff; font-size: 16px;" id="grandTotalDisplay">Php${grandTotal.toFixed(2)}</td>
        </tr>
    `;

            submitBtn.style.display = 'inline-block';
        }

        function updateTotals() {
            const inputs = document.querySelectorAll('.draft-qty');
            let grandTotal = 0;

            inputs.forEach(input => {
                const qty = parseInt(input.value) || 0;
                const costPrice = parseFloat(input.getAttribute('data-cost'));
                const lineTotal = qty * costPrice;

                document.getElementById(`total-${input.dataset.id}`).innerText = `Php${lineTotal.toFixed(2)}`;
                grandTotal += lineTotal;
            });

            document.getElementById('grandTotalDisplay').innerText = `Php${grandTotal.toFixed(2)}`;
        }

        async function submitPurchaseOrder() {
            const inputs = document.querySelectorAll('.draft-qty');
            const orderPayload = [];
            let orderTotal = 0;

            inputs.forEach(input => {
                const qty = parseInt(input.value);
                const costPrice = parseFloat(input.getAttribute('data-cost'));

                if (qty > 0) {
                    orderPayload.push({
                        product_id: parseInt(input.dataset.id),
                        price_bought: costPrice,
                        order_qty: qty
                    });
                    orderTotal += (qty * costPrice);
                }
            });

            if (orderPayload.length === 0) return alert("Please enter valid quantities.");

            const modal = document.getElementById('simulatedApiModal');
            const modalText = document.getElementById('apiModalText');
            const spinner = document.getElementById('apiSpinner');

            modal.style.display = 'flex';
            spinner.style.display = 'block';
            modalText.style.color = '#333';
            modalText.innerText = "Transmitting Purchase Order to Supplier API...";

            try {
                const supplierRes = await fetch('../api/mock_supplier_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        items: orderPayload
                    })
                });
                const supplierData = await supplierRes.json();

                if (!supplierData.success) {
                    modal.style.display = 'none';
                    return alert("Supplier Error: " + supplierData.error);
                }

                modalText.innerText = `Supplier approved! Saving internal records...`;

                const internalRes = await fetch('../api/api_po.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'create_po',
                        items: orderPayload,
                        supplier_ref: supplierData.supplier_reference,
                        total_paid: supplierData.total_deducted
                    })
                });

                const internalData = await internalRes.json();

                if (internalData.success) {
                    spinner.style.display = 'none';
                    modalText.style.color = '#2db84d';
                    modalText.innerText = "Success! Restocking order finalized.";
                    localStorage.setItem("stockAlertNextShowTime", (Date.now() + 15000).toString());
                    setTimeout(() => {
                        modal.style.display = 'none';
                        draftItems = [];
                        renderDraftTable();
                        loadPendingOrders();
                    }, 2000);
                }

            } catch (error) {
                modal.style.display = 'none';
                alert("A network error occurred while contacting the supplier.");
            }
        }

        async function loadPendingOrders() {
            const res = await fetch('../api/api_po.php');
            const orders = await res.json();
            const tbody = document.getElementById('pendingTableBody');

            if (!orders || orders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="padding: 12px; text-align: center; color: #888;">No pending orders.</td></tr>';
                return;
            }

            tbody.innerHTML = orders.map(order => `
        <tr>
          <td style="padding:12px; border-bottom:1px solid #eee;">
                <strong>${order.reference_no}</strong>

                <div style="margin-top:8px; color:#666; font-size:13px;">
                    ${
                        order.product_names
                            ? order.product_names
                                .split("|")
                                .map(item => `• ${item}`)
                                .join("<br>")
                            : ""
                    }

                    <div style="margin-top:6px; font-weight:bold; color:#4d66ff;">
                        Total Cost: ₱${Number(order.total_cost).toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })}
                    </div>
                </div>
            </td>
          <td style="padding: 12px; border-bottom: 1px solid #eee;">${order.created_at}</td>
          <td style="padding: 12px; border-bottom: 1px solid #eee;">${order.total_items} item(s)</td>
          <td style="padding: 12px; border-bottom: 1px solid #eee;">
            <button class="btn-receive" onclick="receiveOrder(${order.id}, '${order.reference_no}')">Mark as Received</button>
          </td>
        </tr>
      `).join('');
        }

        async function receiveOrder(poId, refNo) {
            if (!confirm("Confirm physical arrival of " + refNo + "? This will finalize the invoice and update your shelf stock.")) return;

            const res = await fetch('../api/api_po.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    po_id: poId
                })
            });

            const data = await res.json();

            if (data.success) {
                alert("Delivery verified! Inventory stock and batch logs successfully updated.");
                loadPendingOrders();
            } else {
                alert(data.error);
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

        init();
    </script>
            </div>
        </div>
    </div>
    <?php require_once 'ai_widget.php'; ?>
</body>

</html>