<?php
require '../Database/Database.php';

$database = new Database();
$pdo = $database->getConnection();

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>K's Inventory — Categories</title>
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
        width: 240px;
        z-index: 2000;
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
      }

      .sidebar.active {
        transform: translateX(0);
      }

      .main {
        width: 100%;
        padding: 12px !important;
        padding-bottom: 90px !important;
      }

      .cat-layout {
        grid-template-columns: 1fr !important;
        gap: 16px;
      }

      .table-wrap {
        overflow-x: auto;
        width: 100%;
      }

      .table-wrap table {
        min-width: 480px;
      }

      .modal-overlay {
        z-index: 99999 !important;
        padding: 16px !important;
      }

      .modal {
        width: calc(100% - 24px) !important;
        max-width: 400px !important;
        margin: auto !important;
        padding: 20px 16px !important;
        border-radius: 14px !important;
        box-sizing: border-box !important;
      }
    }
  </style>
</head>

<body>

  <div class="topbar">
    <button class="sidebar-toggle-btn" onclick="toggleSidebar(event)" aria-label="Toggle Navigation Sidebar">
      <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round">
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
      </svg>
    </button>

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

  <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

  <div class="layout">
    <nav class="sidebar" id="sidebarNav">
      <a href="dashboard.php">Dashboard</a>
      <a href="categories.php" class="active">Categories</a>
      <a href="products.php">Products</a>
      <a href="purchase_orders.php">Purchase Orders</a>
      <a href="xml.php">Backup & Restore</a>
      <a href="history.php">History</a>
      <a href="users.php">Users</a>
    </nav>

    <div class="main" style="padding-bottom: 90px;">
      <div class="cat-layout">

        <div class="form-card">
          <h2>Add new category</h2>
          <input type="text" id="catName" placeholder="Category name"
            style="width:100%;border:1.5px solid var(--border);border-radius:6px;padding:9px 12px;font-family:var(--font);font-size:0.93rem;outline:none;margin-bottom:16px;" />
          <button class="btn" onclick="addCategory()">Add</button>
        </div>

        <div class="table-wrap">
          <div style="padding:16px 18px;font-weight:700;font-size:1rem;border-bottom:1px solid var(--border);">All categories</div>
          <table>
            <thead>
              <tr>
                <th style="width:60px;">No.</th>
                <th>Categories</th>
                <th>Total Items</th>
                <th style="width:160px;">Actions</th>
              </tr>
            </thead>
            <tbody id="catTableBody">
              <tr>
                <td colspan="3"><span class="spinner"></span> Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>

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
      const res = await fetch('../Inventory_backend/api_categories.php');
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
        <td style="text-align:center; font-weight:600;">${c.item_count || 0}</td>
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
      const res = await fetch('../Inventory_backend/api_categories.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          name
        })
      });
      const data = await res.json();
      if (data.error) return showToast(data.error, true);
      document.getElementById('catName').value = '';
      showToast('Category added!');
      loadCategories();
    }

    async function deleteCategory(id) {
      if (!confirm('Delete this category? All its products will also be deleted.')) return;
      const res = await fetch('../Inventory_backend/api_categories.php', {
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
      showToast('Category deleted.');
      loadCategories();
    }

    function openEdit(id, name) {
      document.getElementById('editId').value = id;
      document.getElementById('editName').value = name;
      document.getElementById('editModal').classList.add('show');
    }

    function closeModal() {
      document.getElementById('editModal').classList.remove('show');
    }

    async function saveEdit() {
      const id = document.getElementById('editId').value;
      const name = document.getElementById('editName').value.trim();
      if (!name) return showToast('Name cannot be empty.', true);
      const res = await fetch('../Inventory_backend/api_categories.php', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          id,
          name
        })
      });
      const data = await res.json();
      if (data.error) return showToast(data.error, true);
      closeModal();
      showToast('Category updated!');
      loadCategories();
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
      sidebar.classList.toggle("active");
      backdrop.classList.toggle("active");
    }

    function closeSidebar() {
      const sidebar = document.getElementById("sidebarNav");
      const backdrop = document.getElementById("sidebarBackdrop");
      if (sidebar) sidebar.classList.remove("active");
      if (backdrop) backdrop.classList.remove("active");
    }

    window.onclick = function() {
      const dropdown = document.getElementById("userDropdownMenu");
      if (dropdown && dropdown.style.display === "block") {
        dropdown.style.display = "none";
      }
    }

    document.getElementById('catName').addEventListener('keydown', e => {
      if (e.key === 'Enter') addCategory();
    });
    loadCategories();
  </script>
  <?php require_once 'ai_widget.php'; ?>
  <?php require_once 'stock_alert.php'; ?>
</body>

</html>