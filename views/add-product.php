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
  <title>K's Inventory — Add Product</title>
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

    .modern-form-container {
      max-width: 100%;
      display: flex;
      flex-direction: column;
      gap: 16px;
      padding-bottom: 30px;
    }

    .form-section {
      background: #ffffff;
      border: 1px solid #eef0f2;
      border-radius: 10px;
      padding: 18px 20px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .section-header {
      font-size: 1rem;
      font-weight: 700;
      color: #1a1a1a;
      margin-bottom: 14px;
      padding-bottom: 10px;
      border-bottom: 1px solid #f0f0f0;
    }

    .grid-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 16px;
    }

    .input-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .input-group.full-width {
      grid-column: 1 / -1;
    }

    .input-group label {
      font-size: 0.8rem;
      font-weight: 700;
      color: #4a4a4a;
    }

    .modern-input {
      width: 100%;
      padding: 8px 12px;
      border: 1.5px solid #d1d5db;
      border-radius: 6px;
      font-size: 0.88rem;
      font-family: inherit;
      outline: none;
      transition: all 0.2s ease;
      background: #fafafa;
      box-sizing: border-box;
    }

    .modern-input:focus {
      border-color: #4d66ff;
      background: #ffffff;
      box-shadow: 0 0 0 3px rgba(77, 102, 255, 0.1);
    }

    textarea.modern-input {
      resize: vertical;
      min-height: 80px;
    }

    .file-input-wrapper {
      position: relative;
      display: block;
      width: 100%;
    }

    .file-input-label {
      display: block;
      background: #fafafa;
      border: 1.5px dashed #bcbcbc;
      padding: 10px;
      text-align: center;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.85rem;
      font-weight: 600;
      color: #555;
      transition: 0.2s;
    }

    .file-input-label:hover {
      background: #f0f4ff;
      border-color: #4d66ff;
      color: #4d66ff;
    }

    #prodImage,
    #prodModel {
      display: none;
    }

    .action-bar {
      display: flex;
      justify-content: flex-end;
      margin-top: 12px;
      margin-bottom: 65px;
    }

    .btn-submit {
      background: #4d66ff;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 6px;
      font-size: 0.95rem;
      font-weight: 700;
      cursor: pointer;
      transition: 0.2s;
      box-shadow: 0 4px 12px rgba(77, 102, 255, 0.2);
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
        padding-bottom: 65px !important;
      }

      .grid-row {
        grid-template-columns: 1fr !important;
        gap: 12px !important;
      }

      .form-section {
        padding: 14px 12px !important;
      }

      .btn-submit {
        width: 100% !important;
        padding: 12px 16px !important;
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
      <a href="add-product.php" class="sub-tab active">Add Products</a>
      <a href="purchase_orders.php"><?php include '../assets/images/purchase_orders.svg'; ?> <span>Purchase Orders</span></a>
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

        <span class="topbar-title">Add Products</span>

        <div class="topbar-right-group">
          <button id="topbar-ai-btn" onclick="toggleAiChat()" class="topbar-ai-btn"><img src="../assets/images/message.svg" alt="AI" style="width: 15px; height: 15px; object-fit: contain; filter: brightness(0) invert(1); flex-shrink: 0;"> AI Assistant</button>
          <div class="topbar-admin">
            <img src="../assets/images/profile.png" alt="Profile" class="profile-img">
            Welcome back, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>!
          </div>
        </div>
      </div>

      <div class="main">

        <div style="margin-bottom: 20px;">
          <h2 style="font-size: 1.5rem; color: #1a1a1a; margin-bottom: 4px;">Add New Product</h2>
          <p style="color: #666; font-size: 0.9rem; margin-top: 0;">Fill in the details below to add a new item to your inventory catalog.</p>
        </div>

        <form id="productForm" onsubmit="event.preventDefault(); addProduct();" enctype="multipart/form-data">
          <div class="modern-form-container">

            <div class="form-section">
              <div class="section-header">Basic Information</div>
              <div class="grid-row">
                <div class="input-group">
                  <label>Product Name <span style="color:#e05c5c;">*</span></label>
                  <input type="text" id="prodName" class="modern-input" placeholder="e.g. Wireless Gaming Mouse" required />
                </div>
                <div class="input-group">
                  <label>Category <span style="color:#e05c5c;">*</span></label>
                  <select id="prodCategory" class="modern-input" required>
                    <option value="" disabled selected>Loading categories...</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="form-section">
              <div class="section-header">Pricing & Stock</div>
              <div class="grid-row">
                <div class="input-group">
                  <label>Cost Price (₱) <span style="color:#e05c5c;">*</span></label>
                  <input type="number" id="prodPriceBought" class="modern-input" placeholder="0.00" min="0" step="0.01" required />
                </div>
                <div class="input-group">
                  <label>Retail Selling Price (₱) <span style="color:#e05c5c;">*</span></label>
                  <input type="number" id="prodPrice" class="modern-input" placeholder="0.00" min="0" step="0.01" required />
                </div>
                <div class="input-group">
                  <label>Initial Stock Quantity <span style="color:#e05c5c;">*</span></label>
                  <input type="number" id="prodStock" class="modern-input" placeholder="0" min="0" required />
                </div>
              </div>
            </div>

            <div class="form-section">
              <div class="section-header">Product Specifications (Optional)</div>
              <div class="grid-row">
                <div class="input-group">
                  <label>Brand</label>
                  <input type="text" id="prodBrand" class="modern-input" placeholder="e.g. Razer, Logitech, ASUS" />
                </div>
                <div class="input-group">
                  <label>Color</label>
                  <input type="text" id="prodColor" class="modern-input" placeholder="e.g. Black, White, RGB" />
                </div>
                <div class="input-group">
                  <label>Type / Series</label>
                  <input type="text" id="prodType" class="modern-input" placeholder="e.g. Wireless, Mechanical" />
                </div>
              </div>
              <div class="grid-row" style="margin-top: 14px;">
                <div class="input-group">
                  <label>Capacity / Size</label>
                  <input type="text" id="prodSize" class="modern-input" placeholder="e.g. 16GB, 27-inch, Large" />
                </div>
                <div class="input-group">
                  <label>Resolution / Specs</label>
                  <input type="text" id="prodRes" class="modern-input" placeholder="e.g. 4K 144Hz, 2560x1440" />
                </div>
              </div>
            </div>

            <div class="form-section">
              <div class="section-header">Media & Description</div>
              <div class="input-group" style="margin-bottom: 16px;">
                <label>Description</label>
                <textarea id="prodDesc" class="modern-input" placeholder="Enter product features, warranty information..." rows="3"></textarea>
              </div>

              <div class="grid-row">
                <div class="input-group">
                  <label>Product Image</label>
                  <div class="file-input-wrapper">
                    <label for="prodImage" id="fileLabel" class="file-input-label">📁 Click to Browse Image...</label>
                    <input type="file" id="prodImage" accept="image/*" onchange="updateFileLabel()" />
                  </div>
                </div>
                <div class="input-group">
                  <label>3D Model (.glb)</label>
                  <div class="file-input-wrapper">
                    <label for="prodModel" id="modelLabel" class="file-input-label">📦 Click to Browse .GLB (Optional)...</label>
                    <input type="file" id="prodModel" accept=".glb,.gltf" onchange="updateModelLabel()" />
                  </div>
                </div>
              </div>
            </div>

            <div class="action-bar">
              <button type="submit" class="btn-submit">Save Product to Inventory</button>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="toast" id="toast"></div>

  <script>
    function showToast(msg, error = false) {
      const t = document.getElementById('toast');
      t.textContent = msg;
      t.className = 'toast show' + (error ? ' error' : '');
      setTimeout(() => t.className = 'toast', 2400);
    }

    function updateFileLabel() {
      const input = document.getElementById('prodImage');
      const label = document.getElementById('fileLabel');
      if (input.files && input.files[0]) {
        label.textContent = `✅ ${input.files[0].name}`;
        label.style.borderColor = "#2db84d";
        label.style.background = "#e8f9ee";
        label.style.color = "#157347";
      } else {
        label.textContent = "📁 Click to Browse Image...";
        label.style.borderColor = "#bcbcbc";
        label.style.background = "#fafafa";
        label.style.color = "#555";
      }
    }

    function updateModelLabel() {
      const input = document.getElementById('prodModel');
      const label = document.getElementById('modelLabel');
      if (input.files && input.files[0]) {
        label.textContent = `✅ ${input.files[0].name}`;
        label.style.borderColor = "#4d66ff";
        label.style.background = "#f0f4ff";
        label.style.color = "#4d66ff";
      } else {
        label.textContent = "📦 Click to Browse .GLB (Optional)...";
        label.style.borderColor = "#bcbcbc";
        label.style.background = "#fafafa";
        label.style.color = "#555";
      }
    }

    async function loadCategories() {
      const res = await fetch('../api/api_categories.php');
      const cats = await res.json();
      const sel = document.getElementById('prodCategory');
      sel.innerHTML = '<option value="" disabled selected>Select category</option>' +
        cats.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    }

    async function addProduct() {
      const name = document.getElementById('prodName').value.trim();
      const category_id = document.getElementById('prodCategory').value;
      const price_bought = document.getElementById('prodPriceBought').value;
      const price = document.getElementById('prodPrice').value;
      const stock = document.getElementById('prodStock').value;
      const desc = document.getElementById('prodDesc').value.trim();
      const imageFile = document.getElementById('prodImage').files[0];
      const modelFile = document.getElementById('prodModel').files[0];
      const brand = document.getElementById('prodBrand').value.trim();
      const color = document.getElementById('prodColor').value.trim();
      const type = document.getElementById('prodType').value.trim();
      const size = document.getElementById('prodSize').value.trim();
      const resolution = document.getElementById('prodRes').value.trim();

      if (!name) return showToast('Please enter a product name.', true);
      if (!category_id) return showToast('Please select a category.', true);
      if (!price_bought || price_bought < 0) return showToast('Please enter a valid cost price.', true);
      if (!price || price < 0) return showToast('Please enter a valid selling price.', true);
      if (!stock || stock < 0) return showToast('Please enter a valid stock quantity.', true);

      const formData = new FormData();
      formData.append('name', name);
      formData.append('category_id', category_id);
      formData.append('price_bought', price_bought);
      formData.append('price', price);
      formData.append('stock', stock);
      formData.append('description', desc);
      formData.append('brand', brand);
      formData.append('color', color);
      formData.append('type', type);
      formData.append('capacity_size', size);
      formData.append('resolution', resolution);

      if (imageFile) formData.append('image', imageFile);
      if (modelFile) formData.append('model_file', modelFile);

      const res = await fetch('../api/api_products.php', {
        method: 'POST',
        body: formData
      });

      const data = await res.json();
      if (data.error) return showToast(data.error, true);

      document.getElementById('productForm').reset();

      const fLabel = document.getElementById('fileLabel');
      fLabel.textContent = "📁 Click to Browse Image...";
      fLabel.style.borderColor = "#bcbcbc";
      fLabel.style.background = "#fafafa";
      fLabel.style.color = "#555";

      const mLabel = document.getElementById('modelLabel');
      mLabel.textContent = "📦 Click to Browse .GLB (Optional)...";
      mLabel.style.borderColor = "#bcbcbc";
      mLabel.style.background = "#fafafa";
      mLabel.style.color = "#555";

      showToast('Product added successfully!');
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
      if (dropdown && dropdown.style.display === "block") {
        dropdown.style.display = "none";
      }
    }

    loadCategories();
  </script>
  <?php require_once 'ai_widget.php'; ?>
  <?php require_once 'stock_alert.php'; ?>
</body>

</html>