<?php 
require '../Database/config.php';

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../Drafts/login_signup.php");
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
    /* User Dropdown Profile Container */
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

    /* Floating Menu Drawer */
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
    <a href="add-product.php" class="sub active">- Add Products</a>
  </nav>

  <div class="main">
    <div class="form-card">
      <h2>Add new product</h2>

      <div class="form-row">
        <label>Product Name</label>
        <input type="text" id="prodName" placeholder="Product name" />
      </div>
      <div class="form-row">
        <label></label>
        <select id="prodCategory">
          <option value="" disabled selected>Select category</option>
        </select>
      </div>
      <div class="form-row">
        <label>Price (₱)</label>
        <input type="number" id="prodPrice" placeholder="Product price" min="0" step="0.01" />
      </div>
      <div class="form-row">
        <label>Stock</label>
        <input type="number" id="prodStock" placeholder="Product quantity" min="0" />
      </div>

      <div class="btn-row">
        <button class="btn" onclick="addProduct()">Add</button>
      </div>
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

  async function loadCategories() {
    const res  = await fetch('../Inventory_backend/api_categories.php');
    const cats = await res.json();
    const sel  = document.getElementById('prodCategory');
    sel.innerHTML = '<option value="" disabled selected>Select category</option>' +
      cats.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
  }

  async function addProduct() {
    const name        = document.getElementById('prodName').value.trim();
    const category_id = parseInt(document.getElementById('prodCategory').value);
    const price       = parseFloat(document.getElementById('prodPrice').value);
    const stock       = parseInt(document.getElementById('prodStock').value);

    if (!name)              return showToast('Please enter a product name.', true);
    if (!category_id)       return showToast('Please select a category.', true);
    if (isNaN(price) || price < 0) return showToast('Please enter a valid price.', true);
    if (isNaN(stock) || stock < 0) return showToast('Please enter a valid stock quantity.', true);

    const res  = await fetch('../Inventory_backend/api_products.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name, category_id, price, stock })
    });
    const data = await res.json();
    if (data.error) return showToast(data.error, true);

    document.getElementById('prodName').value  = '';
    document.getElementById('prodPrice').value = '';
    document.getElementById('prodStock').value = '';
    document.getElementById('prodCategory').selectedIndex = 0;

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