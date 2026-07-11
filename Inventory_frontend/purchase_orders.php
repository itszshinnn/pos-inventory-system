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
            <a href="xml.php">XML Files</a>
            <a href="history.php">History</a>
            <a href="users.php">Users</a>
        </nav>

        <div class="main">

            <div class="restock-section">
                <div class="section-title">Draft Purchase Order</div>
                <p style="color: #666; margin-bottom: 16px; font-size: 14px;">Add items to your supplier order draft manually or review critical alerts.</p>

                <div class="search-container">
                    <input type="text" id="manualSearch" class="search-input" placeholder="Search product to add..." oninput="handleSearch()">
                    <div id="searchResults" class="search-results"></div>
                </div>

                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; background: #f8f9fb;">
                            <th style="padding: 12px; border-bottom: 1px solid #ddd;">Product Name</th>
                            <th style="padding: 12px; border-bottom: 1px solid #ddd;">Current Stock</th>
                            <th style="padding: 12px; border-bottom: 1px solid #ddd;">Unit Price</th>
                            <th style="padding: 12px; border-bottom: 1px solid #ddd; width: 150px;">Order Quantity</th>
                            <th style="padding: 12px; border-bottom: 1px solid #ddd;">Total</th>
                            <th style="padding: 12px; border-bottom: 1px solid #ddd; width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="draftTableBody">
                        <tr>
                            <td colspan="4" style="padding: 12px; text-align: center; color: #888;">Draft is empty. Add items to begin.</td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn-submit" id="submitOrderBtn" onclick="submitPurchaseOrder()" style="display: none;">Submit Order to Supplier</button>
            </div>

            <div class="restock-section">
                <div class="section-title">Pending Incoming Orders</div>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; background: #f8f9fb;">
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
            const res = await fetch('../Inventory_backend/api_products.php');
            allProducts = await res.json();

            await loadDraftItemsFromURL();
            await loadPendingOrders();
        }

        async function loadDraftItemsFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            const itemsParam = urlParams.get('items');

            if (itemsParam) {
                const itemIds = itemsParam.split(',');
                draftItems = allProducts.filter(p => itemIds.includes(p.id.toString()));
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
        <td style="padding: 12px; border-bottom: 1px solid #eee;">₱${costPrice.toFixed(2)}</td>
        <td style="padding: 12px; border-bottom: 1px solid #eee;">
          <input type="number" class="qty-input draft-qty" data-id="${item.id}" data-cost="${costPrice}" min="1" value="${defaultQty}" oninput="updateTotals()">
        </td>
        <td style="padding: 12px; border-bottom: 1px solid #eee; font-weight: bold;" class="line-total" id="total-${item.id}">₱${lineTotal.toFixed(2)}</td>
        <td style="padding: 12px; border-bottom: 1px solid #eee;">
          <button class="btn-remove" onclick="removeItemFromDraft(${item.id})">Remove</button>
        </td>
      </tr>
    `
            }).join('');

            tbody.innerHTML += `
        <tr style="background: #f8f9fb;">
            <td colspan="4" style="text-align: right; padding: 12px;"><strong>Total Supplier Cost:</strong></td>
            <td colspan="2" style="padding: 12px; font-weight: bold; color: #4d66ff; font-size: 16px;" id="grandTotalDisplay">₱${grandTotal.toFixed(2)}</td>
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

                document.getElementById(`total-${input.dataset.id}`).innerText = `₱${lineTotal.toFixed(2)}`;
                grandTotal += lineTotal;
            });

            document.getElementById('grandTotalDisplay').innerText = `₱${grandTotal.toFixed(2)}`;
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
                const supplierRes = await fetch('../Inventory_backend/mock_supplier_api.php', {
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

                const internalRes = await fetch('../Inventory_backend/api_po.php', {
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
            const res = await fetch('../Inventory_backend/api_po.php');
            const orders = await res.json();
            const tbody = document.getElementById('pendingTableBody');

            if (!orders || orders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="padding: 12px; text-align: center; color: #888;">No pending orders.</td></tr>';
                return;
            }

            tbody.innerHTML = orders.map(order => `
        <tr>
          <td style="padding: 12px; border-bottom: 1px solid #eee; font-weight: bold;">${order.reference_no}</td>
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

            const res = await fetch('../Inventory_backend/api_po.php', {
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
        window.onclick = function() {
            const dropdown = document.getElementById("userDropdownMenu");
            if (dropdown && dropdown.style.display === "block") dropdown.style.display = "none";
        }

        init();
    </script>
</body>

</html>