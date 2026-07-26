<?php
require '../src/Database.php';

$database = new Database();
$pdo = $database->getConnection();

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
  <title>K's Inventory — Promo Codes</title>
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

    .cat-layout {
      display: flex !important;
      flex-direction: column !important;
      gap: 16px !important;
      width: 100% !important;
      max-width: none !important;
    }

    .table-card,
    .table-toolbar {
      width: 100% !important;
      max-width: none !important;
    }

    .status-badge {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 6px;
      font-size: 11px;
      font-weight: 600;
    }

    .status-badge.active {
      background: #e6f9ed;
      color: #10b981;
    }

    .status-badge.inactive {
      background: #fee2e2;
      color: #ef4444;
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
      }

      .sidebar.active {
        transform: translateX(0);
      }

      .main {
        width: 100% !important;
        max-width: 100% !important;
        padding: 12px !important;
        box-sizing: border-box !important;
      }

      .cat-layout {
        display: flex !important;
        flex-direction: column !important;
        gap: 5px !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
      }

      .table-toolbar {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 10px !important;
        padding: 12px !important;
      }

      .table-toolbar input,
      .table-toolbar select,
      .table-toolbar button {
        width: 100% !important;
        min-width: unset !important;
      }

      .table-card {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow-x: hidden !important;
      }

      .table-wrap {
        overflow-x: auto !important;
        overflow-y: auto !important;
        width: 100% !important;
        max-width: 100% !important;
        max-height: 270px !important;
        margin-bottom: 75px !important;
        -webkit-overflow-scrolling: touch;
        box-sizing: border-box !important;
      }

      .table-wrap table {
        min-width: 620px;
      }

      .modal-overlay {
        z-index: 99999 !important;
        padding: 16px !important;
      }

      .modal {
        width: calc(100% - 24px) !important;
        max-width: 400px !important;
        margin: auto !important;
        box-sizing: border-box !important;
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
      <a href="purchase_orders.php"><?php include '../assets/images/purchase_orders.svg'; ?> <span>Purchase Orders</span></a>
      <a href="promos.php" class="active"><?php include '../assets/images/promos.svg'; ?> <span>Promo Codes</span></a>

      <span class="sidebar-group-label">Reports</span>
      <a href="xml.php"><?php include '../assets/images/backup.svg'; ?> <span>Backup and Restore</span></a>
      <a href="#" onclick="toggleHistorySubmenu(event)" id="historyParentLink"><?php include '../assets/images/history.svg'; ?> <span>History</span></a>
      <div id="historySubmenu" style="display: <?= (in_array(basename($_SERVER['PHP_SELF']), ['history.php', 'product_history.php', 'login_history.php']) ? 'block' : 'none') ?>;">
        <a href="history.php" class="sub-tab">Sales History</a>
        <a href="product_history.php" class="sub-tab">Inventory Logs</a>
        <a href="login_history.php" class="sub-tab">Log History</a>
      </div>
      <a href="users.php"><?php include '../assets/images/users.svg'; ?> <span>Accounts</span></a>

      <div class="sidebar-logout">
        <a href="logout.php">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" x2="9" y1="12" y2="12" />
          </svg>
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
        <button class="sidebar-toggle-btn" onclick="toggleSidebar()">
          <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round">
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>

        <span class="topbar-title">Promo Codes</span>

        <div class="topbar-right-group">
          <button id="topbar-ai-btn" onclick="toggleAiChat()" class="topbar-ai-btn"><img src="../assets/images/message.svg" alt="AI" style="width: 15px; height: 15px; object-fit: contain; filter: brightness(0) invert(1); flex-shrink: 0;"> AI Assistant</button>
          <div class="topbar-admin" onclick="toggleDropdown()">
            <img src="../assets/images/profile.png" alt="Profile" class="profile-img">
            <span class="topbar-admin-text"><span class="welcome-prefix">Welcome back, </span><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>!</span>
            <div class="dropdown-menu" id="adminDropdown">
              <a href="logout.php">Logout</a>
            </div>
          </div>
        </div>
      </div>

      <div class="main">
        <div class="cat-layout">

          <div class="table-toolbar" style="display: flex; gap: 12px; margin-bottom: 16px; align-items: center; background: white; padding: 16px; border-radius: 10px; border: 1px solid var(--border, #dde1e9); box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08); flex-wrap: wrap;">
            <input type="text" id="promoCode" placeholder="Promo Code (e.g. SUMMER10)..."
              style="flex: 1.5; min-width: 180px; height: 44px; padding: 10px; border-radius: 8px; border: 1.5px solid #bcbcbc; outline: none; font-family: var(--font); font-size: 0.93rem; box-sizing: border-box; text-transform: uppercase;" />

            <input type="number" id="promoValue" placeholder="Value..." min="0.01" step="0.01"
              style="flex: 1; min-width: 100px; height: 44px; padding: 10px; border-radius: 8px; border: 1.5px solid #bcbcbc; outline: none; font-family: var(--font); font-size: 0.93rem; box-sizing: border-box;" />

            <select id="promoType" style="flex: 1; min-width: 120px; height: 44px; padding: 10px; border-radius: 8px; border: 1.5px solid #bcbcbc; outline: none; font-family: var(--font); font-size: 0.93rem; box-sizing: border-box; background: white;">
              <option value="percent">Percentage (%)</option>
              <option value="amount">Fixed Amount (Php)</option>
            </select>

            <button class="btn" onclick="addPromo()" style="height: 44px; padding: 0 24px; border-radius: 8px; font-weight: 600; font-family: var(--font); font-size: 0.93rem; display: inline-flex; align-items: center; justify-content: center; white-space: nowrap; box-sizing: border-box; background: #1B4EF5; color: white;">Add Promo</button>
          </div>

          <div class="table-card">
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th style="width:60px;">No.</th>
                    <th>Promo Code</th>
                    <th>Discount</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th style="width:220px;">Actions</th>
                  </tr>
                </thead>
                <tbody id="promoTableBody">
                  <tr>
                    <td colspan="6"><span class="spinner"></span> Loading...</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="mobile-bottom-spacer"></div>
      </div>
    </div>
  </div>

  <div class="modal-overlay" id="editModal">
    <div class="modal" style="background: white; padding: 24px; border-radius: 12px; max-width: 400px; width: 100%; border: 1px solid #ddd; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
      <h3>Edit Promo Code</h3>
      <input type="hidden" id="editId" />

      <div style="margin-bottom: 12px; display: flex; flex-direction: column; gap: 4px; text-align: left;">
        <label style="font-size: 12px; font-weight: 600; color: #475569;">Promo Code</label>
        <input type="text" id="editCode" placeholder="Code" style="text-transform: uppercase; width: 100%; padding: 10px; border-radius: 8px; border: 1.5px solid #bcbcbc; font-family: var(--font);" />
      </div>

      <div style="margin-bottom: 12px; display: flex; flex-direction: column; gap: 4px; text-align: left;">
        <label style="font-size: 12px; font-weight: 600; color: #475569;">Discount Value</label>
        <input type="number" id="editValue" placeholder="Value" min="0.01" step="0.01" style="width: 100%; padding: 10px; border-radius: 8px; border: 1.5px solid #bcbcbc; font-family: var(--font);" />
      </div>

      <div style="margin-bottom: 16px; display: flex; flex-direction: column; gap: 4px; text-align: left;">
        <label style="font-size: 12px; font-weight: 600; color: #475569;">Discount Type</label>
        <select id="editType" style="width: 100%; padding: 10px; border-radius: 8px; border: 1.5px solid #bcbcbc; font-family: var(--font); background: white;">
          <option value="percent">Percentage (%)</option>
          <option value="amount">Fixed Amount (Php)</option>
        </select>
      </div>

      <div class="modal-btns">
        <button class="btn-cancel" onclick="closeModal()">Cancel</button>
        <button class="btn" onclick="saveEdit()" style="background: #1B4EF5; color: white;">Save</button>
      </div>
    </div>
  </div>

  <?php include 'ai_widget.php'; ?>

  <div class="toast" id="toast"></div>

  <script>
    function showToast(msg, error = false) {
      const t = document.getElementById('toast');
      t.textContent = msg;
      t.className = 'toast show' + (error ? ' error' : '');
      setTimeout(() => t.className = 'toast', 2400);
    }

    function toggleDropdown() {
      const dropdown = document.getElementById('adminDropdown');
      dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    window.addEventListener('click', function(e) {
      if (!e.target.closest('.topbar-admin')) {
        document.getElementById('adminDropdown').style.display = 'none';
      }
    });

    function toggleSidebar() {
      document.getElementById('sidebarNav').classList.toggle('active');
      document.getElementById('sidebarBackdrop').classList.toggle('active');
    }

    function closeSidebar() {
      document.getElementById('sidebarNav').classList.remove('active');
      document.getElementById('sidebarBackdrop').classList.remove('active');
    }

    function toggleHistorySubmenu(e) {
      e.preventDefault();
      const sub = document.getElementById('historySubmenu');
      sub.style.display = sub.style.display === 'block' ? 'none' : 'block';
    }

    async function loadPromos() {
      const res = await fetch('../api/api_promos.php');
      const promos = await res.json();
      const tbody = document.getElementById('promoTableBody');
      if (!promos.length) {
        tbody.innerHTML = '<tr><td colspan="6" style="color:var(--text-muted);padding:20px;">No promo codes yet.</td></tr>';
        return;
      }
      tbody.innerHTML = promos.map((p, i) => `
      <tr>
        <td>${i + 1}</td>
        <td style="text-align:center;font-weight:600;">${p.code}</td>
        <td style="text-align:center;">${p.discount_type === 'percent' ? parseFloat(p.discount_value) + '%' : 'Php' + parseFloat(p.discount_value).toFixed(2)}</td>
        <td style="text-align:center;">${p.discount_type === 'percent' ? 'Percentage' : 'Fixed Amount'}</td>
        <td style="text-align:center;">
          <span class="status-badge ${p.is_active == 1 ? 'active' : 'inactive'}">${p.is_active == 1 ? 'Active' : 'Inactive'}</span>
        </td>
        <td>
          <button class="action-btn" onclick="toggleActive(${p.id}, ${p.is_active == 1 ? 0 : 1})">${p.is_active == 1 ? 'Disable' : 'Enable'}</button>
          <button class="action-btn" onclick="openEdit(${p.id}, '${p.code}', ${p.discount_value}, '${p.discount_type}')">Edit</button>
          <button class="action-btn del" onclick="deletePromo(${p.id})">Delete</button>
        </td>
      </tr>
    `).join('');
    }

    async function addPromo() {
      const code = document.getElementById('promoCode').value.trim();
      const val = parseFloat(document.getElementById('promoValue').value) || 0;
      const type = document.getElementById('promoType').value;

      if (!code) return showToast('Please enter a promo code.', true);
      if (val <= 0) return showToast('Please enter a valid discount value.', true);

      const res = await fetch('../api/api_promos.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          code,
          discount_value: val,
          discount_type: type,
          is_active: 1
        })
      });
      const data = await res.json();
      if (data.error) return showToast(data.error, true);

      document.getElementById('promoCode').value = '';
      document.getElementById('promoValue').value = '';
      showToast('Promo code added successfully!');
      loadPromos();
    }

    async function toggleActive(id, newStatus) {
      const res = await fetch('../api/api_promos.php', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          id,
          toggle_active: true,
          is_active: newStatus
        })
      });
      const data = await res.json();
      if (data.error) return showToast(data.error, true);
      showToast('Status updated.');
      loadPromos();
    }

    async function deletePromo(id) {
      if (!confirm('Are you sure you want to delete this promo code?')) return;
      const res = await fetch('../api/api_promos.php', {
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
      showToast('Promo code deleted.');
      loadPromos();
    }

    function openEdit(id, code, val, type) {
      document.getElementById('editId').value = id;
      document.getElementById('editCode').value = code;
      document.getElementById('editValue').value = val;
      document.getElementById('editType').value = type;
      document.getElementById('editModal').classList.add('show');
    }

    function closeModal() {
      document.getElementById('editModal').classList.remove('show');
    }

    async function saveEdit() {
      const id = document.getElementById('editId').value;
      const code = document.getElementById('editCode').value.trim();
      const val = parseFloat(document.getElementById('editValue').value) || 0;
      const type = document.getElementById('editType').value;

      if (!code) return showToast('Promo code cannot be empty.', true);
      if (val <= 0) return showToast('Please enter a valid discount value.', true);

      const res = await fetch('../api/api_promos.php', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          id,
          code,
          discount_value: val,
          discount_type: type
        })
      });
      const data = await res.json();
      if (data.error) return showToast(data.error, true);

      closeModal();
      showToast('Promo code updated!');
      loadPromos();
    }

    document.addEventListener("DOMContentLoaded", function() {
      const sidebar = document.getElementById("sidebarNav");
      setTimeout(() => {
        if (sidebar) sidebar.classList.remove("no-transition");
      }, 50);
    });

    loadPromos();
  </script>
</body>

</html>