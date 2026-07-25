<?php
require '../Database/Database.php';
$database = new Database();
$pdo = $database->getConnection();

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../Inventory_frontend/login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>K's Inventory — Products</title>
  <link rel="stylesheet" href="../style.css?v=<?= filemtime(__DIR__ . '/../style.css') ?>">

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

    .main {
      overflow-y: hidden !important;
    }

    .table-wrap {
      flex: 1;
      overflow-y: auto !important;
      overflow-x: hidden !important;
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

    table {
      width: 100%;
      border-collapse: collapse;
      white-space: nowrap;
    }

    table td:nth-child(2),
    table th:nth-child(2) {
      white-space: normal;
      min-width: 200px;
      max-width: 350px;
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

      .table-toolbar {
        display: flex !important;
        flex-direction: column !important;
        gap: 8px !important;
        margin-bottom: 12px !important;
        padding: 10px 0px !important;
      }

      .table-toolbar input,
      .table-toolbar select {
        width: 100% !important;
        font-size: 0.85rem !important;
        padding: 9px 12px !important;
        height: 40px !important;
      }

      .table-wrap {
        overflow-x: auto !important;
        margin-bottom: 0px !important;
      }

      .table-wrap table {
        min-width: 850px;
      }

      .modal-overlay {
        z-index: 99999 !important;
        padding: 16px !important;
      }

      .modal {
        width: calc(100% - 24px) !important;
        max-width: 700px !important;
        max-height: 82vh !important;
        margin: auto !important;
        padding: 18px 16px !important;
        border-radius: 14px !important;
        box-sizing: border-box !important;
      }

      .edit-modal-grid {
        grid-template-columns: 1fr !important;
        gap: 10px !important;
      }
    }
  </style>
</head>

<body>

  <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

  <div class="layout">
    <nav class="sidebar no-transition" id="sidebarNav">
      <div class="sidebar-brand">
        <div class="brand-logo-icon">
          <img src="../Images/logo.svg" alt="Logo" style="width: 26px; height: 26px; object-fit: contain;">
        </div>
        <div class="brand-text">
          <span class="brand-name">Kinetix</span>
          <span class="brand-sub">Inventory System</span>
        </div>
      </div>

      <span class="sidebar-group-label">Menu</span>
      <a href="dashboard.php"><?php include '../Images/dashboard.svg'; ?> <span>Dashboard</span></a>
      <a href="categories.php"><?php include '../Images/categories.svg'; ?> <span>Categories</span></a>
      <a href="products.php" class="active"><?php include '../Images/products.svg'; ?> <span>Products</span></a>
      <a href="add-product.php" class="sub-tab">Add Products</a>
      <a href="purchase_orders.php"><?php include '../Images/purchase_orders.svg'; ?> <span>Purchase Orders</span></a>

      <span class="sidebar-group-label">Reports</span>
      <a href="xml.php"><?php include '../Images/backup.svg'; ?> <span>Backup and Restore</span></a>
      <a href="#" onclick="toggleHistorySubmenu(event)" id="historyParentLink"><?php include '../Images/history.svg'; ?> <span>History</span></a>
      <div id="historySubmenu" style="display: <?= (in_array(basename($_SERVER['PHP_SELF']), ['history.php', 'product_history.php', 'login_history.php']) ? 'block' : 'none') ?>;">
        <a href="history.php" class="sub-tab<?= (basename($_SERVER['PHP_SELF']) == 'history.php' ? ' active' : '') ?>">Sales History</a>
        <a href="product_history.php" class="sub-tab<?= (basename($_SERVER['PHP_SELF']) == 'product_history.php' ? ' active' : '') ?>">Inventory Logs</a>
        <a href="login_history.php" class="sub-tab<?= (basename($_SERVER['PHP_SELF']) == 'login_history.php' ? ' active' : '') ?>">Log History</a>
      </div>
      <a href="users.php"><?php include '../Images/users.svg'; ?> <span>Users</span></a>

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

        <span class="topbar-title">Products</span>

        <div class="topbar-right-group">
          <button id="topbar-ai-btn" onclick="toggleAiChat()" class="topbar-ai-btn"><img src="../Images/message.svg" alt="AI" style="width: 15px; height: 15px; object-fit: contain; filter: brightness(0) invert(1); flex-shrink: 0;"> AI Assistant</button>
          <div class="topbar-admin">
            <img src="../Images/profile.png" alt="Profile" class="profile-img">
            <span class="topbar-admin-text"><span class="welcome-prefix">Welcome back, </span><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>!</span>
          </div>
        </div>
      </div>

      <div class="main" style="padding-bottom: 90px;">
        <div class="table-toolbar" style="display: flex; gap: 12px; margin-bottom: 16px; align-items: center;">
          <input class="search-box" type="text" id="searchInput" placeholder="Search name, category, brand, color..." oninput="applyFilters()" style="flex: 1; height: 40px; padding: 0 12px; border-radius: 8px; border: 1.5px solid #bcbcbc; outline: none; font-family: inherit;">

          <select id="filterCategory" onchange="applyFilters()" style="height: 40px; padding: 0 12px; border-radius: 8px; border: 1.5px solid #bcbcbc; outline: none; background: white; cursor: pointer; font-family: inherit;">
            <option value="">All Categories</option>
          </select>

          <select id="filterBrand" onchange="applyFilters()" style="height: 40px; padding: 0 12px; border-radius: 8px; border: 1.5px solid #bcbcbc; outline: none; background: white; cursor: pointer; font-family: inherit;">
            <option value="">All Brands</option>
          </select>

          <select id="filterColor" onchange="applyFilters()" style="height: 40px; padding: 0 12px; border-radius: 8px; border: 1.5px solid #bcbcbc; outline: none; background: white; cursor: pointer; font-family: inherit;">
            <option value="">All Colors</option>
          </select>
        </div>

        <div class="table-wrap">
          <table id="productsTable">
            <thead>
              <tr>
                <th style="width:50px;">No.</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Color</th>
                <th>Size</th>
                <th>Bought</th>
                <th>Retail</th>
                <th>Stocks</th>
                <th>Status</th>
                <th style="width:230px;">Actions</th>
              </tr>
            </thead>
            <tbody id="prodTableBody">
              <tr>
                <td colspan="11"><span class="spinner"></span> Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="mobile-bottom-spacer"></div>
      </div>
    </div>

  <div class="modal-overlay" id="editModal">
    <div class="modal" style="width: 800px; max-width: 90%; max-height: 90vh; overflow-y: auto;">
      <h3>Edit Product</h3>
      <input type="hidden" id="editId" />

      <div class="edit-modal-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 12px;">
        <div>
          <label style="font-size: 0.85rem; font-weight: 600;">Product Name</label>
          <input type="text" id="editName" placeholder="Product name" />

          <label style="font-size: 0.85rem; font-weight: 600;">Category</label>
          <select id="editCategory"></select>

          <label style="font-size: 0.85rem; font-weight: 600;">Price Bought / Cost (₱)</label>
          <input type="number" id="editPriceBought" placeholder="Cost (Php)" min="0" step="0.01" />

          <label style="font-size: 0.85rem; font-weight: 600;">Price Sold / Retail (₱)</label>
          <input type="number" id="editPrice" placeholder="Retail Price (Php)" min="0" step="0.01" />

          <label style="font-size: 0.85rem; font-weight: 600;">Stock</label>
          <input type="number" id="editStock" placeholder="Stock" min="0" />
        </div>

        <div>
          <label style="font-size: 0.85rem; font-weight: 600;">Brand</label>
          <input type="text" id="editBrand" placeholder="Brand" />

          <label style="font-size: 0.85rem; font-weight: 600;">Color</label>
          <input type="text" id="editColor" placeholder="Color" />

          <label style="font-size: 0.85rem; font-weight: 600;">Type</label>
          <input type="text" id="editType" placeholder="Type" />

          <label style="font-size: 0.85rem; font-weight: 600;">Capacity / Size</label>
          <input type="text" id="editSize" placeholder="Capacity / Size" />

          <label style="font-size: 0.85rem; font-weight: 600;">Resolution</label>
          <input type="text" id="editRes" placeholder="Resolution" />
        </div>
      </div>

      <div style="margin-bottom: 16px;">
        <label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 6px;">Description</label>
        <textarea id="editDesc" placeholder="Product description (Optional)" rows="3" style="width: 100%; border: 1.5px solid var(--border); border-radius: 6px; padding: 9px 12px; font-family: var(--font); font-size: 0.93rem; outline: none; resize: vertical; box-sizing: border-box;"></textarea>
      </div>

      <div class="modal-btns">
        <button class="btn-cancel" onclick="closeModal()">Cancel</button>
        <button class="btn" onclick="saveEdit()">Save Changes</button>
      </div>
    </div>
  </div>

  <div class="toast" id="toast"></div>

  <script>
    let allProducts = [];
    let allCategories = [];

    function showToast(msg, error = false) {
      const t = document.getElementById('toast');
      t.textContent = msg;
      t.className = 'toast show' + (error ? ' error' : '');
      setTimeout(() => t.className = 'toast', 2400);
    }

    function stockStatus(stock) {
      const n = Number(stock);
      if (n === 0) return '<span class="status-out">Out of stock</span>';
      if (n <= 3) return '<span class="status-low">Low</span>';
      return '<span class="status-fine">Fine</span>';
    }

    async function loadData() {
      const [pRes, cRes] = await Promise.all([
        fetch('../Inventory_backend/api_products.php'),
        fetch('../Inventory_backend/api_categories.php')
      ]);
      allProducts = await pRes.json();
      allCategories = await cRes.json();
      populateFilters();
      renderTable(allProducts);
    }

    function renderTable(products) {
      const tbody = document.getElementById('prodTableBody');
      if (!products.length) {
        tbody.innerHTML = '<tr><td colspan="11" style="color:var(--text-muted);padding:20px;">No products found.</td></tr>';
        return;
      }
      tbody.innerHTML = products.map((p, i) => `
      <tr>
        <td>${i + 1}</td>
        <td>${p.name}</td>
        <td>${p.category}</td>
        <td>${p.brand || '-'}</td>
        <td>${p.color || '-'}</td>
        <td>${p.capacity_size || '-'}</td>
        <td>Php${Number(p.price_bought).toFixed(2)}</td>
        <td>Php${Number(p.price).toFixed(2)}</td>
        <td>${p.stock}</td>
        <td>${stockStatus(p.stock)}</td>
        <td>
          <button class="action-btn" onclick="restockProduct(${p.id})">Restock</button>
          <button class="action-btn" onclick="openEdit(${p.id})">Edit</button>
          <button class="action-btn del" onclick="deleteProduct(${p.id})">Delete</button>
        </td>
      </tr>
    `).join('');
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

    function populateFilters() {
      const uniqueBrands = [...new Set(allProducts.map(p => p.brand).filter(b => b))].sort();
      const uniqueColors = [...new Set(allProducts.map(p => p.color).filter(c => c))].sort();
      const uniqueCategories = [...new Set(allProducts.map(p => p.category).filter(cat => cat))].sort();

      const brandSelect = document.getElementById('filterBrand');
      const colorSelect = document.getElementById('filterColor');
      const categorySelect = document.getElementById('filterCategory');

      uniqueCategories.forEach(cat => {
        categorySelect.innerHTML += `<option value="${cat}">${cat}</option>`;
      });

      uniqueBrands.forEach(brand => {
        brandSelect.innerHTML += `<option value="${brand}">${brand}</option>`;
      });

      uniqueColors.forEach(color => {
        colorSelect.innerHTML += `<option value="${color}">${color}</option>`;
      });
    }

    function applyFilters() {
      const query = document.getElementById('searchInput').value.toLowerCase();
      const selectedBrand = document.getElementById('filterBrand').value;
      const selectedColor = document.getElementById('filterColor').value;
      const selectedCategory = document.getElementById('filterCategory').value;
      const sortOrder = document.getElementById('sortPrice') ? document.getElementById('sortPrice').value : '';

      let filtered = allProducts.filter(p => {
        const matchesText =
          p.name.toLowerCase().includes(query) ||
          p.category.toLowerCase().includes(query) ||
          (p.brand && p.brand.toLowerCase().includes(query)) ||
          (p.color && p.color.toLowerCase().includes(query));

        const matchesBrand = selectedBrand === "" || p.brand === selectedBrand;
        const matchesColor = selectedColor === "" || p.color === selectedColor;
        const matchesCategory = selectedCategory === "" || p.category === selectedCategory;

        return matchesText && matchesBrand && matchesColor && matchesCategory;
      });

      if (sortOrder === 'asc') {
        filtered.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
      } else if (sortOrder === 'desc') {
        filtered.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
      } else {
        filtered.sort((a, b) => a.id - b.id);
      }

      renderTable(filtered);
    }

    async function deleteProduct(id) {
      if (!confirm('Delete this product?')) return;
      const res = await fetch('../Inventory_backend/api_products.php', {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          id
        })
      });
      const data = await res.json();
      if (data.error) return showToast(data.error, true);
      showToast('Product deleted.');
      loadData();
    }

    function restockProduct(id) {
      const p = allProducts.find(x => x.id === id);
      if (!p) return;

      window.location.href = `purchase_orders.php?items=${id}`;
    }

    function openEdit(id) {
      const p = allProducts.find(x => x.id === id);
      if (!p) return;

      document.getElementById('editId').value = p.id;
      document.getElementById('editName').value = p.name;
      document.getElementById('editPriceBought').value = p.price_bought;
      document.getElementById('editPrice').value = p.price;
      document.getElementById('editStock').value = p.stock;

      document.getElementById('editBrand').value = p.brand || '';
      document.getElementById('editColor').value = p.color || '';
      document.getElementById('editType').value = p.type || '';
      document.getElementById('editSize').value = p.capacity_size || '';
      document.getElementById('editRes').value = p.resolution || '';
      document.getElementById('editDesc').value = p.description || '';

      const sel = document.getElementById('editCategory');
      sel.innerHTML = allCategories.map(c =>
        `<option value="${c.id}" ${c.id == p.category_id ? 'selected' : ''}>${c.name}</option>`
      ).join('');

      document.getElementById('editModal').classList.add('show');
    }

    function closeModal() {
      document.getElementById('editModal').classList.remove('show');
    }

    async function saveEdit() {
      const id = parseInt(document.getElementById('editId').value);
      const name = document.getElementById('editName').value.trim();
      const category_id = parseInt(document.getElementById('editCategory').value);
      const price_bought = parseFloat(document.getElementById('editPriceBought').value);
      const price = parseFloat(document.getElementById('editPrice').value);
      const stock = parseInt(document.getElementById('editStock').value);

      const brand = document.getElementById('editBrand').value.trim();
      const color = document.getElementById('editColor').value.trim();
      const type = document.getElementById('editType').value.trim();
      const capacity_size = document.getElementById('editSize').value.trim();
      const resolution = document.getElementById('editRes').value.trim();
      const description = document.getElementById('editDesc').value.trim();

      if (!name || !category_id || isNaN(price_bought) || isNaN(price) || isNaN(stock)) {
        return showToast('All base product fields are required.', true);
      }

      const res = await fetch('../Inventory_backend/api_products.php', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          id,
          name,
          category_id,
          price_bought,
          price,
          stock,
          brand,
          color,
          type,
          capacity_size,
          resolution,
          description
        })
      });

      const data = await res.json();
      if (data.error) return showToast(data.error, true);

      closeModal();
      showToast('Product updated successfully!');
      loadData();
    }

    window.onclick = function() {
      const dropdown = document.getElementById("userDropdownMenu");
      if (dropdown && dropdown.style.display === "block") {
        dropdown.style.display = "none";
      }
    }

    loadData();
  </script>
  <?php require_once 'ai_widget.php'; ?>
  <?php require_once 'stock_alert.php'; ?>
</body>

</html>