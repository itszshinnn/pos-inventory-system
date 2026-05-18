<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>K's Inventory — Products</title>
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
    <a href="/Inventory Frontend/dashboard.php">Dashboard</a>
    <a href="/Inventory Frontend/categories.php">Categories</a>
    <a href="/Inventory Frontend/products.php" class="active">Products</a>
    <a href="/Inventory Frontend/add-product.php" class="sub">- Add Products</a>
  </nav>

  <div class="main">
    <div class="table-wrap">
      <div class="table-toolbar">
        <input class="search-box" type="text" id="searchInput" placeholder="Search..." oninput="filterTable()">
        <a href="add-product.php" class="btn" style="text-decoration:none;">Add</a>
      </div>
      <table>
        <thead>
          <tr>
            <th style="width:55px;">No.</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stocks</th>
            <th>Status</th>
            <th style="width:160px;">Actions</th>
          </tr>
        </thead>
        <tbody id="prodTableBody">
          <tr><td colspan="7"><span class="spinner"></span> Loading...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal-overlay" id="editModal">
  <div class="modal">
    <h3>Edit Product</h3>
    <input type="hidden" id="editId" />
    <input type="text"   id="editName"     placeholder="Product name" />
    <select id="editCategory"></select>
    <input type="number" id="editPrice"    placeholder="Price (₱)" min="0" step="0.01" />
    <input type="number" id="editStock"    placeholder="Stock" min="0" />
    <div class="modal-btns">
      <button class="btn-cancel" onclick="closeModal()">Cancel</button>
      <button class="btn" onclick="saveEdit()">Save</button>
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
    if (n <= 5)  return '<span class="status-low">Low</span>';
    return '<span class="status-fine">Fine</span>';
  }

  async function loadData() {
    const [pRes, cRes] = await Promise.all([
      fetch('../Inventory Backend/api_products.php'),
      fetch('../Inventory Backend/api_categories.php')
    ]);
    allProducts   = await pRes.json();
    allCategories = await cRes.json();
    renderTable(allProducts);
  }

  function renderTable(products) {
    const tbody = document.getElementById('prodTableBody');
    if (!products.length) {
      tbody.innerHTML = '<tr><td colspan="7" style="color:var(--text-muted);padding:20px;">No products found.</td></tr>';
      return;
    }
    tbody.innerHTML = products.map((p, i) => `
      <tr>
        <td>${i + 1}</td>
        <td>${p.name}</td>
        <td>${p.category}</td>
        <td>₱${Number(p.price).toFixed(2)}</td>
        <td>${p.stock}</td>
        <td>${stockStatus(p.stock)}</td>
        <td>
          <button class="action-btn" onclick="openEdit(${p.id})">Edit</button>
          <button class="action-btn del" onclick="deleteProduct(${p.id})">Delete</button>
        </td>
      </tr>
    `).join('');
  }

  function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    renderTable(allProducts.filter(p =>
      p.name.toLowerCase().includes(q) || p.category.toLowerCase().includes(q)
    ));
  }

  async function deleteProduct(id) {
    if (!confirm('Delete this product?')) return;
    const res  = await fetch('../Inventory Backend/api_products.php', {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    });
    const data = await res.json();
    if (data.error) return showToast(data.error, true);
    showToast('Product deleted.');
    loadData();
  }

  function openEdit(id) {
    const p = allProducts.find(x => x.id === id);
    document.getElementById('editId').value    = p.id;
    document.getElementById('editName').value  = p.name;
    document.getElementById('editPrice').value = p.price;
    document.getElementById('editStock').value = p.stock;

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
    const id          = parseInt(document.getElementById('editId').value);
    const name        = document.getElementById('editName').value.trim();
    const category_id = parseInt(document.getElementById('editCategory').value);
    const price       = parseFloat(document.getElementById('editPrice').value);
    const stock       = parseInt(document.getElementById('editStock').value);

    if (!name || !category_id || isNaN(price) || isNaN(stock)) return showToast('All fields are required.', true);

    const res  = await fetch('../Inventory Backend/api_products.php', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, name, category_id, price, stock })
    });
    const data = await res.json();
    if (data.error) return showToast(data.error, true);
    closeModal();
    showToast('Product updated!');
    loadData();
  }

  loadData();
</script>
</body>
</html>
