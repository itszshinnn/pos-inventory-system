<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>K's Inventory — Add Product</title>
<link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="topbar">
  <div class="topbar-admin">
    <svg viewBox="0 0 32 32" fill="none"><circle cx="16" cy="16" r="16" fill="rgba(255,255,255,0.2)"/><circle cx="16" cy="13" r="5" fill="#fff"/><path d="M6 26c0-5 4.5-8 10-8s10 3 10 8" fill="#fff"/></svg>
    Admin ▾
  </div>
  <span class="topbar-title">K's Inventory System</span>
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

    // Reset
    document.getElementById('prodName').value  = '';
    document.getElementById('prodPrice').value = '';
    document.getElementById('prodStock').value = '';
    document.getElementById('prodCategory').selectedIndex = 0;

    showToast('Product added successfully!');
  }

  loadCategories();
</script>
</body>
</html>
