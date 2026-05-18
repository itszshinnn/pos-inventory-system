<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>K's Inventory — Categories</title>
<link rel="stylesheet" href="style.css">
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
    <a href="categories.php" class="active">Categories</a>
    <a href="products.php">Products</a>
  </nav>

  <div class="main">
    <div class="cat-layout">

      <!-- ADD FORM -->
      <div class="form-card">
        <h2>Add new category</h2>
        <input type="text" id="catName" placeholder="Category name"
          style="width:100%;border:1.5px solid var(--border);border-radius:6px;padding:9px 12px;font-family:var(--font);font-size:0.93rem;outline:none;margin-bottom:16px;" />
        <button class="btn" onclick="addCategory()">Add</button>
      </div>

      <!-- TABLE -->
      <div class="table-wrap">
        <div style="padding:16px 18px;font-weight:700;font-size:1rem;border-bottom:1px solid var(--border);">All categories</div>
        <table>
          <thead>
            <tr>
              <th style="width:60px;">No.</th>
              <th>Categories</th>
              <th style="width:160px;">Actions</th>
            </tr>
          </thead>
          <tbody id="catTableBody">
            <tr><td colspan="3"><span class="spinner"></span> Loading...</td></tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal-overlay" id="editModal">
  <div class="modal">
    <h3>Edit Category</h3>
    <input type="hidden" id="editId" />
    <input type="text" id="editName" placeholder="Category name" />
    <div class="modal-btns">
      <button class="btn-cancel" onclick="closeModal()">Cancel</button>
      <button class="btn" onclick="saveEdit()">Save</button>
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
    const res  = await fetch('api_categories.php');
    const cats = await res.json();
    const tbody = document.getElementById('catTableBody');
    if (!cats.length) {
      tbody.innerHTML = '<tr><td colspan="3" style="color:var(--text-muted);padding:20px;">No categories yet.</td></tr>';
      return;
    }
    tbody.innerHTML = cats.map((c, i) => `
      <tr>
        <td>${i + 1}</td>
        <td style="text-align:left;padding-left:16px;">${c.name}</td>
        <td>
          <button class="action-btn" onclick="openEdit(${c.id}, '${c.name.replace(/'/g,"\\'")}')">Edit</button>
          <button class="action-btn del" onclick="deleteCategory(${c.id})">Delete</button>
        </td>
      </tr>
    `).join('');
  }

  async function addCategory() {
    const name = document.getElementById('catName').value.trim();
    if (!name) return showToast('Please enter a category name.', true);
    const res  = await fetch('api_categories.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name })
    });
    const data = await res.json();
    if (data.error) return showToast(data.error, true);
    document.getElementById('catName').value = '';
    showToast('Category added!');
    loadCategories();
  }

  async function deleteCategory(id) {
    if (!confirm('Delete this category? All its products will also be deleted.')) return;
    const res  = await fetch('api_categories.php', {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    });
    const data = await res.json();
    if (data.error) return showToast(data.error, true);
    showToast('Category deleted.');
    loadCategories();
  }

  function openEdit(id, name) {
    document.getElementById('editId').value   = id;
    document.getElementById('editName').value = name;
    document.getElementById('editModal').classList.add('show');
  }

  function closeModal() {
    document.getElementById('editModal').classList.remove('show');
  }

  async function saveEdit() {
    const id   = document.getElementById('editId').value;
    const name = document.getElementById('editName').value.trim();
    if (!name) return showToast('Name cannot be empty.', true);
    const res  = await fetch('api_categories.php', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, name })
    });
    const data = await res.json();
    if (data.error) return showToast(data.error, true);
    closeModal();
    showToast('Category updated!');
    loadCategories();
  }

  document.getElementById('catName').addEventListener('keydown', e => { if (e.key === 'Enter') addCategory(); });
  loadCategories();
</script>
</body>
</html>
