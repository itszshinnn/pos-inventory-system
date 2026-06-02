<?php
require '../Database/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../Inventory_frontend/login_signup.php");
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

    .form-container {
      background: #fff;
      padding: 30px;
      border-radius: 14px;
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
      max-width: 700px;
    }

    .form-header h2 {
      margin-bottom: 5px;
      font-size: 1.8rem;
    }

    .form-header p {
      color: #191919;
      margin-bottom: 30px;
    }

    .form-section {
      padding: 20px;
      border: 1px solid #e3e3e3;
      border-radius: 10px;
      background: #fafafa;
    }

    .form-section h3 {
      margin-bottom: 10px;
    }

    .section-desc {
      color: #191919;
      font-size: 0.95rem;
      margin-bottom: 20px;
    }

    .submit-btn {
      background: #111;
      color: white;
      border: none;
      padding: 12px 22px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 0.95rem;
      font-weight: 600;
      transition: 0.2s;
    }

    .submit-btn:hover {
      background: #333;
    }

    .import-btn {
      background: #111;
      color: white;
      border: none;
      padding: 12px 22px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 0.95rem;
      font-weight: 600;
      transition: 0.2s;
    }

    .import-btn:hover {
      background: #333;
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
      <a href="xml.php" class="active">XML Files</a>
      <a href="history.php">History</a>
      <a href="users.php">Users</a>
    </nav>

    <div class="main">

      <div class="form-container">

        <div class="form-header">
          <h2>XML File Management</h2>
          <p>Import and export your database XML files.</p>
        </div>

        <div class="form-section">

          <h3>Export Database Into one XML File</h3>

          <p class="section-desc">
            Export all database tables into a single XML backup file.
          </p>

          <form action="../Inventory_backend/export_all.php" method="POST">

            <button type="submit" class="submit-btn">
              Export Database XML
            </button>

          </form>

        </div>

        <div class="form-section" style="margin-top: 30px;">

          <h3>Export XML Files</h3>

          <p class="section-desc">
            Export all database tables into XML backup files. Located in XML_files folder.
          </p>

          <form action="../Inventory_backend/export_individual.php" method="POST">

            <button type="submit" class="submit-btn">
              Export XML Files
            </button>

          </form>

        </div>

        <div class="form-section" style="margin-top: 30px;">

          <h3>Import XML Files</h3>

          <p class="section-desc">
            Restore database tables from existing XML files. Both Individual table XML files and full database export file are supported.
          </p>
          <p class="section-desc" style="color: #e83832;">
            NOTE: XML files will overwrite the corresponding table data.
          </p>
          <form action="../Inventory_backend/import.php" method="POST" enctype="multipart/form-data">

            <input type="file" name="xml_file" accept=".xml" required>
            <button type="submit" class="import-btn">Import XML</button>

          </form>

        </div>

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

    window.onclick = function() {
      const dropdown = document.getElementById("userDropdownMenu");
      if (dropdown && dropdown.style.display === "block") {
        dropdown.style.display = "none";
      }
    }
  </script>
</body>

</html>