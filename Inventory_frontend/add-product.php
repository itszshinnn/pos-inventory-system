<?php
require '../Database/Database.php';
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
  <title>K's Inventory — Add Product</title>
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

    .file-input-wrapper {
      position: relative;
      display: inline-block;
      width: 100%;
    }

    .file-input-label {
      display: block;
      background: #f0f0f0;
      border: 1.5px dashed #bcbcbc;
      padding: 10px;
      text-align: center;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.9rem;
      font-weight: 500;
      transition: 0.2s;
    }

    .file-input-label:hover {
      background: #e6e6e6;
      border-color: #000000d6;
    }

    #prodImage {
      display: none;
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
      <a href="products.php" class="active">Products</a>
      <a href="add-product.php" class="active">- Add Products</a>
      <a href="xml.php">XML Files</a>
      <a href="history.php">History</a>
      <a href="users.php">Users</a>
    </nav>

    <div class="main">
      <form id="productForm" onsubmit="event.preventDefault(); addProduct();" enctype="multipart/form-data">
        <div class="form-card">
          <h2>Add new product</h2>

          <div class="form-row">
            <label>Product Name</label>
            <input type="text" id="prodName" placeholder="Product name" required />
          </div>
          <div class="form-row">
            <label>Category</label>
            <select id="prodCategory" required>
              <option value="" disabled selected>Select category</option>
            </select>
          </div>
          <div class="form-row">
            <label>Price (₱)</label>
            <input type="number" id="prodPrice" placeholder="Product price" min="0" step="0.01" required />
          </div>
          <div class="form-row">
            <label>Stock</label>
            <input type="number" id="prodStock" placeholder="Product quantity" min="0" required />
          </div>
          <div class="form-row">
            <label>Product Image</label>
            <div class="file-input-wrapper">
              <label for="prodImage" id="fileLabel" class="file-input-label">📁 Choose Image File...</label>
              <input type="file" id="prodImage" accept=".jpg,.jpeg,.png,.webp,.gif" onchange="updateFileLabel()" />
            </div>
          </div>

          <div class="btn-row">
            <button type="submit" class="btn">Add Product</button>
          </div>
        </div>
      </form>
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
      } else {
        label.textContent = "📁 Choose Image File...";
        label.style.borderColor = "#bcbcbc";
      }
    }

    async function loadCategories() {
      const res = await fetch('../Inventory_backend/api_categories.php');
      const cats = await res.json();
      const sel = document.getElementById('prodCategory');
      sel.innerHTML = '<option value="" disabled selected>Select category</option>' +
        cats.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    }

    async function addProduct() {
      const name = document.getElementById('prodName').value.trim();
      const category_id = document.getElementById('prodCategory').value;
      const price = document.getElementById('prodPrice').value;
      const stock = document.getElementById('prodStock').value;
      const imageFile = document.getElementById('prodImage').files[0];

      if (!name) return showToast('Please enter a product name.', true);
      if (!category_id) return showToast('Please select a category.', true);
      if (!price || price < 0) return showToast('Please enter a valid price.', true);
      if (!stock || stock < 0) return showToast('Please enter a valid stock quantity.', true);

      const formData = new FormData();
      formData.append('name', name);
      formData.append('category_id', category_id);
      formData.append('price', price);
      formData.append('stock', stock);
      if (imageFile) {
        formData.append('image', imageFile);
      }

      const res = await fetch('../Inventory_backend/api_products.php', {
        method: 'POST',
        body: formData
      });

      const data = await res.json();
      if (data.error) return showToast(data.error, true);

      document.getElementById('productForm').reset();
      document.getElementById('fileLabel').textContent = "📁 Choose Image File...";
      document.getElementById('fileLabel').style.borderColor = "#bcbcbc";

      showToast('Product added successfully!');
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

    loadCategories();
  </script>
</body>

</html>