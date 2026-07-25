<?php
require '../Database/Database.php';
$database = new Database();
$pdo = $database->getConnection();

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../Inventory_frontend/login_sloginignup.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>K's Inventory — Backup & Restore</title>
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
        padding-bottom: 80px !important;
      }

      .form-container {
        padding: 16px 12px !important;
        margin-bottom: 60px !important;
      }

      .submit-btn,
      .import-btn {
        width: 100% !important;
      }

      input[type="file"] {
        width: 100% !important;
        box-sizing: border-box;
        margin-bottom: 12px;
      }

      .backup-grid {
        grid-template-columns: 1fr !important;
        gap: 16px !important;
      }
    }

    .form-container {
      background: #fff;
      padding: 24px;
      border-radius: 14px;
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
      width: 100%;
      box-sizing: border-box;
    }

    .form-header h2 {
      margin-bottom: 5px;
      font-size: 1.6rem;
      font-weight: 700;
    }

    .form-header p {
      color: #64748b;
      margin-bottom: 24px;
      font-size: 0.95rem;
    }

    .backup-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      align-items: stretch;
    }

    .form-section {
      padding: 24px;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      background: #f8fafc;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .form-section h3 {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 12px;
      color: #0f172a;
      min-height: 54px;
      display: flex;
      align-items: center;
    }

    .section-desc {
      color: #475569;
      font-size: 0.88rem;
      line-height: 1.5;
      margin-bottom: 16px;
      min-height: 64px;
    }

    .warning-desc {
      font-size: 0.88rem;
      line-height: 1.5;
      margin-bottom: 16px;
    }

    .form-section form {
      margin-top: auto;
      width: 100%;
    }

    .submit-btn,
    .import-btn {
      background: var(--accent2, #1b4ef5);
      color: white;
      border: none;
      padding: 11px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 0.9rem;
      font-weight: 600;
      transition: 0.2s;
      width: 100%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .submit-btn:hover,
    .import-btn:hover {
      background: #0d3cd8;
    }

    input[type="file"] {
      width: 100%;
      padding: 8px 12px;
      border: 1.5px solid #cbd5e1;
      border-radius: 6px;
      background: #fff;
      font-size: 0.85rem;
      font-family: inherit;
      margin-bottom: 12px;
      cursor: pointer;
      box-sizing: border-box;
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
      <a href="products.php"><?php include '../Images/products.svg'; ?> <span>Products</span></a>
      <a href="purchase_orders.php"><?php include '../Images/purchase_orders.svg'; ?> <span>Purchase Orders</span></a>

      <span class="sidebar-group-label">Reports</span>
      <a href="xml.php" class="active"><?php include '../Images/backup.svg'; ?> <span>Backup and Restore</span></a>
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

        <span class="topbar-title">Backup and Restore</span>

        <div class="topbar-right-group">
          <button id="topbar-ai-btn" onclick="toggleAiChat()" class="topbar-ai-btn"><img src="../Images/message.svg" alt="AI" style="width: 15px; height: 15px; object-fit: contain; filter: brightness(0) invert(1); flex-shrink: 0;"> AI Assistant</button>
          <div class="topbar-admin">
            <img src="../Images/profile.png" alt="Profile" class="profile-img">
            <span class="topbar-admin-text"><span class="welcome-prefix">Welcome back, </span><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>!</span>
          </div>
        </div>
      </div>

      <div class="main">

      <div class="form-container">

        <div class="form-header">
          <h2>Backup & Restore</h2>
          <p>Backup & Restore your database using XML files.</p>
        </div>

        <div class="backup-grid">
          <div class="form-section">
            <h3>Export into one XML File</h3>
            <p class="section-desc">
              Export all database tables into a single XML backup file.
            </p>
            <form action="../Inventory_backend/export_all.php" method="POST">
              <button type="submit" class="submit-btn">
                Export Database XML
              </button>
            </form>
          </div>

          <div class="form-section">
            <h3>Export into separate XML Files</h3>
            <p class="section-desc">
              Export all database tables into XML backup files. Located in the <strong>XML_files folder</strong>.
            </p>
            <form action="../Inventory_backend/export_tables.php" method="POST">
              <button type="submit" class="submit-btn">
                Export XML Files
              </button>
            </form>
          </div>

          <div class="form-section">
            <h3>Import XML Files</h3>
            <p class="section-desc">
              Restore database tables from existing XML files. Both Individual table XML files and full database export file are supported.
            </p>
            <p class="warning-desc" style="color: #e83832; font-weight: 600; margin-top: -8px;">
              NOTE: XML files will overwrite the corresponding table data.
            </p>
            <form action="../Inventory_backend/import.php" method="POST" enctype="multipart/form-data">
              <input type="file" name="xml_file" accept=".xml" required>
              <button type="submit" class="import-btn">Import XML</button>
            </form>
          </div>
        </div>
        <div class="mobile-bottom-spacer"></div>
      </div>

    </div>
  </div>

  <div class="toast" id="toast"></div>
  <script>
    function showToast(message, isError = false) {

      const toast = document.getElementById("toast");

      toast.textContent = message;

      if (isError) {
        toast.style.background = "#d9534f";
      } else {
        toast.style.background = "#111";
      }

      toast.classList.add("show");

      setTimeout(() => {
        toast.classList.remove("show");
      }, 4000);
    }

    window.onload = function() {

      <?php if (isset($_GET['success'])): ?>

        <?php if ($_GET['success'] == 'exported'): ?>

          showToast("XML files exported successfully!");

        <?php elseif ($_GET['success'] == 'imported'): ?>

          showToast("XML files imported successfully!");

        <?php endif; ?>

      <?php endif; ?>

      <?php if (isset($_GET['error'])): ?>

        showToast("Error: <?= htmlspecialchars($_GET['error']) ?>", true);

      <?php endif; ?>

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
  </script>
      </div>
    </div>
  </div>
  <?php require_once 'stock_alert.php'; ?>
  <?php require_once 'ai_widget.php'; ?>
</body>

</html>