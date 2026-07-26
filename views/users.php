<?php
require '../src/Database.php';

$database = new Database();
$pdo = $database->getConnection();

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = '';

if (isset($_POST['create_user'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    $check = $pdo->prepare("
        SELECT id
        FROM users
        WHERE username = ?
    ");

    $check->execute([$username]);

    if ($check->fetch()) {

        $message = "Username already exists.";
    } else {

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $pdo->prepare("
            INSERT INTO users
            (username,password,role)
            VALUES (?,?,?)
        ");

        $stmt->execute([
            $username,
            $hashedPassword,
            $role
        ]);

        $message = "User created successfully.";
    }
}

if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    if ($id != $_SESSION['user_id']) {

        $stmt = $pdo->prepare("
            DELETE FROM users
            WHERE id = ?
        ");

        $stmt->execute([$id]);
    }

    header("Location: users.php");
    exit;
}

if (isset($_POST['update_user'])) {

    $id = (int)$_POST['id'];
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $password = trim($_POST['password']);

    if (!empty($password)) {

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $pdo->prepare("
            UPDATE users
            SET username = ?,
                role = ?,
                password = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $username,
            $role,
            $hashedPassword,
            $id
        ]);
    } else {

        $stmt = $pdo->prepare("
            UPDATE users
            SET username = ?,
                role = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $username,
            $role,
            $id
        ]);
    }

    header("Location: users.php");
    exit;
}
try {

    $totalUsers = $pdo->query("
        SELECT COUNT(*)
        FROM users
    ")->fetchColumn();

    $adminUsers = $pdo->query("
        SELECT COUNT(*)
        FROM users
        WHERE role = 'admin'
    ")->fetchColumn();

    $staffUsers = $pdo->query("
        SELECT COUNT(*)
        FROM users
        WHERE role = 'user'
    ")->fetchColumn();

    $query = "
        SELECT
            id,
            username,
            role,
            created_at
        FROM users
        ORDER BY id DESC
    ";

    $stmt = $pdo->query($query);

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {

    $totalUsers = 0;
    $adminUsers = 0;
    $staffUsers = 0;

    $users = [];

    $errorMsg = $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K's Inventory — Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=DM+Mono:wght@400;500&display=swap');

        input,
        select,
        button,
        textarea {
            font-family: 'DM Sans', 'Segoe UI', sans-serif;
        }

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
            background: #fff;
            min-width: 140px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 8px 16px rgba(0, 0, 0, .2);
        }

        .dropdown-menu a {
            color: #ff4b4b;
            padding: 12px 16px;
            display: block;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .dropdown-menu a:hover {
            background: #fff0f0;
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

        .table-wrap {
            overflow-x: auto;
            width: 100%;
            border-radius: 8px;
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
                padding-bottom: 75px !important;
            }

            .history-stats-grid {
                display: flex !important;
                flex-direction: column !important;
                grid-template-columns: 1fr !important;
                gap: 10px !important;
            }

            .create-user-form {
                grid-template-columns: 1fr !important;
                gap: 10px !important;
            }

            .history-toolbar {
                grid-template-columns: 1fr !important;
                gap: 8px !important;
            }

            .table-wrap {
                margin-bottom: 70px !important;
            }

            .table-wrap table {
                min-width: 550px;
            }

            .modal-overlay {
                z-index: 99999 !important;
                padding: 16px !important;
            }

            .modal {
                width: calc(100vw - 24px) !important;
                max-height: 82vh !important;
                overflow-y: auto !important;
                box-sizing: border-box !important;
            }
        }

        .layout {
            display: flex;
        }

        .main {
            flex: 1;
            padding: 24px;
        }

        .dashboard-section {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #eef0f2;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #414141;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 16px;
        }

        .user-input,
        .user-select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #d8d8d8;
            border-radius: 8px;
            font-size: 14px;
            background: #fff;
            box-sizing: border-box;
        }

        .user-input:focus,
        .user-select:focus {
            outline: none;
            border-color: #5c7fe0;
        }

        .btn-save {
            background: #234bb8;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
        }

        .btn-save:hover {
            opacity: .9;
        }

        .btn-delete {
            background: #ff4b4b;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-left: 6px;
        }

        .btn-delete:hover {
            opacity: .9;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table th {
            background: #f8f9fb;
            padding: 14px;
            text-align: left;
            font-size: 13px;
            font-weight: 700;
        }

        .users-table td {
            padding: 12px;
            border-top: 1px solid #eef0f2;
        }

        .users-table tr:hover {
            background: #fafafa;
        }

        .history-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .history-stat-card {
            background: white;
            padding: 16px;
            border-radius: 12px;
            border: 1px solid var(--border, #cfcfcf);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .history-stat-card h3 {
            font-size: 13px;
            font-weight: 700;
            color: #666;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .history-stat-card p {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .history-stat-card p.green-txt {
            color: #2db84d;
            font-weight: 500;
        }

        .history-stat-card p.blue-txt {
            color: #4d66ff;
        }

        .history-toolbar {
            display: grid;
            grid-template-columns: 1fr 180px 150px;
            gap: 12px;
            margin-bottom: 16px;
        }

        .history-toolbar input,
        .history-toolbar select {
            height: 40px;
            border: 1.5px solid var(--border, #bcbcbc);
            outline: none;
            background: white;
            border-radius: 8px;
            padding: 0 12px;
            font-size: 14px;
            color: #333;
            transition: 0.2s;
        }

        .history-toolbar input:focus,
        .history-toolbar select:focus {
            border-color: #4d66ff;
        }

        .create-user-card {
            margin-bottom: 20px;
        }

        .create-user-form {
            display: grid;
            grid-template-columns: 1fr 1fr 180px 160px;
            gap: 12px;
            align-items: center;
        }

        .create-user-form input,
        .create-user-form select {
            height: 44px;
            border: 1.5px solid #d8d8d8;
            border-radius: 10px;
            padding: 0 14px;
            font-size: 14px;
            background: white;
            transition: .2s;
        }

        .create-user-form input:focus,
        .create-user-form select:focus {
            outline: none;
            border-color: #4d66ff;
            box-shadow: 0 0 0 3px rgba(77, 102, 255, .12);
        }

        .btn-create-user {
            height: 44px;
            border: none;
            border-radius: 10px;
            color: black;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
            border: 2px solid #4d66ff;
        }

        .btn-create-user:hover {
            color: white;
            background: #3f57eb;
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
            <a href="promos.php"><?php include '../assets/images/promos.svg'; ?> <span>Promo Codes</span></a>

            <span class="sidebar-group-label">Reports</span>
            <a href="xml.php"><?php include '../assets/images/backup.svg'; ?> <span>Backup and Restore</span></a>
            <a href="#" onclick="toggleHistorySubmenu(event)" id="historyParentLink"><?php include '../assets/images/history.svg'; ?> <span>History</span></a>
            <div id="historySubmenu" style="display: <?= (in_array(basename($_SERVER['PHP_SELF']), ['history.php', 'product_history.php', 'login_history.php']) ? 'block' : 'none') ?>;">
                <a href="history.php" class="sub-tab<?= (basename($_SERVER['PHP_SELF']) == 'history.php' ? ' active' : '') ?>">Sales History</a>
                <a href="product_history.php" class="sub-tab<?= (basename($_SERVER['PHP_SELF']) == 'product_history.php' ? ' active' : '') ?>">Inventory Logs</a>
                <a href="login_history.php" class="sub-tab<?= (basename($_SERVER['PHP_SELF']) == 'login_history.php' ? ' active' : '') ?>">Log History</a>
            </div>
            <a href="users.php" class="active"><?php include '../assets/images/users.svg'; ?> <span>Users</span></a>

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

                <span class="topbar-title">Users</span>

                <div class="topbar-right-group">
                    <button id="topbar-ai-btn" onclick="toggleAiChat()" class="topbar-ai-btn"><img src="../assets/images/message.svg" alt="AI" style="width: 15px; height: 15px; object-fit: contain; filter: brightness(0) invert(1); flex-shrink: 0;"> AI Assistant</button>
                    <div class="topbar-admin">
                        <img src="../assets/images/profile.png" alt="Profile" class="profile-img">
                        <span class="topbar-admin-text">Welcome back, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>!</span>
                    </div>
                </div>
            </div>

            <div class="main">
            <?php if (isset($errorMsg)): ?>
                <div style="background: #fff0f0; border: 1px solid #ffbcbc; color: #ff4b4b; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                    <strong>Database Notice:</strong> <?= htmlspecialchars($errorMsg) ?>
                </div>
            <?php endif; ?>
            <div class="history-stats-grid">

                <div class="history-stat-card">
                    <h3>Total Accounts</h3>
                    <p class="blue-txt"><?= $totalUsers ?></p>
                </div>

                <div class="history-stat-card">
                    <h3>Admin Accounts</h3>
                    <p class="green-txt"><?= $adminUsers ?></p>
                </div>

                <div class="history-stat-card">
                    <h3>User Accounts</h3>
                    <p><?= $staffUsers ?></p>
                </div>

            </div>

            <?php if ($message): ?>
                <div style="
    background:#e8f9ee;
    border:1px solid #8ce0a6;
    color:#157347;
    padding:12px;
    border-radius:8px;
    margin-bottom:20px;
">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="dashboard-section" style="margin-bottom: 20px;">
                <div class="section-title">System Settings</div>
                <p style="color: #666; margin-bottom: 12px; font-size: 14px;">Define which business email address will receive automated low-stock warnings and restock confirmations.</p>

                <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                    <input type="email" id="settingAlertEmail" placeholder="manager@company.com" style="height: 44px; border: 1.5px solid #d8d8d8; border-radius: 10px; padding: 0 14px; font-size: 14px; flex: 1; min-width: 220px; outline:none;" />
                    <button type="button" onclick="saveAlertEmailSetting()" style="height: 44px; padding: 0 20px; background: #4d66ff; color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; min-width: 140px;">Update Settings</button>
                </div>
            </div>

            <div class="dashboard-section create-user-card">

                <div class="section-title">
                    Create User
                </div>

                <form method="POST" class="create-user-form">

                    <input
                        type="text"
                        name="username"
                        placeholder="Username"
                        required>

                    <input
                        type="password"
                        name="password"
                        placeholder="Password"
                        required>

                    <select name="role">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>

                    <button
                        type="submit"
                        name="create_user"
                        class="btn-create-user">
                        Create
                    </button>

                </form>

            </div>

            <br>
            <div class="history-toolbar">
                <input
                    type="text"
                    id="userSearchInput"
                    placeholder="Search users..."
                    oninput="filterUserTable()">

                <select
                    id="sortFilter"
                    onchange="filterUserTable()">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                </select>
            </div>
            <div class="dashboard-section">

                <div class="section-title">
                    Existing Users
                </div>

                <div class="table-wrap">
                    <table>

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody id="userTableBody">
                        </tbody>

                    </table>
                </div>

            </div>
            <div class="modal-overlay" id="editModal">

                <div class="modal">

                    <h3>Edit User</h3>

                    <form method="POST">

                        <input
                            type="hidden"
                            id="editId"
                            name="id">

                        <input
                            type="text"
                            id="editUsername"
                            name="username"
                            placeholder="Username"
                            required>
                        <input
                            type="password"
                            id="editPassword"
                            name="password"
                            placeholder="New Password (leave blank to keep current)">

                        <select
                            id="editRole"
                            name="role">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>

                        <div class="modal-btns">

                            <button
                                type="button"
                                class="btn-cancel"
                                onclick="closeModal()">
                                Cancel
                            </button>

                            <button
                                type="submit"
                                name="update_user"
                                class="btn">
                                Save
                            </button>

                        </div>

                    </form>

                </div>

            </div>
            <script>
                const allUsers = <?= json_encode($users) ?>;
                const currentUserId = <?= $_SESSION['user_id'] ?>;

                async function fetchAlertEmailSetting() {
                    try {
                        const response = await fetch('../api/api_settings.php');
                        const data = await response.json();
                        if (data.admin_alert_email) {
                            document.getElementById('settingAlertEmail').value = data.admin_alert_email;
                        }
                    } catch (err) {
                        console.error("Failed to load email configurations:", err);
                    }
                }

                async function saveAlertEmailSetting() {
                    const emailVal = document.getElementById('settingAlertEmail').value.trim();
                    if (!emailVal) return alert("Please enter an email address.");

                    try {
                        const response = await fetch('../api/api_settings.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                admin_alert_email: emailVal
                            })
                        });
                        const data = await response.json();

                        if (data.success) {
                            alert("System notification settings updated successfully!");
                        } else {
                            alert(data.error || "An integration error occurred.");
                        }
                    } catch (err) {
                        alert("Unable to reach configuration servers.");
                    }
                }

                fetchAlertEmailSetting();

                function openEdit(id, username, role) {

                    document.getElementById('editId').value = id;
                    document.getElementById('editUsername').value = username;
                    document.getElementById('editRole').value = role;

                    document
                        .getElementById('editModal')
                        .classList.add('show');
                }

                function closeModal() {

                    document
                        .getElementById('editModal')
                        .classList.remove('show');
                }

                function filterUserTable() {

                    const searchVal = document
                        .getElementById('userSearchInput')
                        .value
                        .toLowerCase()
                        .trim();

                    const sortVal = document
                        .getElementById('sortFilter')
                        .value;

                    let filtered = allUsers.filter(user => {

                        return (
                            user.username.toLowerCase().includes(searchVal) ||
                            user.role.toLowerCase().includes(searchVal)
                        );

                    });

                    if (sortVal === 'oldest') {

                        filtered.sort((a, b) => a.id - b.id);

                    } else {

                        filtered.sort((a, b) => b.id - a.id);

                    }

                    const tbody = document.getElementById('userTableBody');

                    if (filtered.length === 0) {

                        tbody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align:center;padding:20px;color:#888;">
                    No users found.
                </td>
            </tr>
        `;

                        return;
                    }

                    tbody.innerHTML = filtered.map(user => `

        <tr>

            <td>${user.id}</td>

            <td>${user.username}</td>

            <td>${user.role}</td>

            <td>${user.created_at}</td>

            <td>

                <button
                    class="action-btn"
                    onclick="openEdit(
                        ${user.id},
                        '${user.username.replace(/'/g, "\\'")}',
                        '${user.role}'
                    )"
                >
                    Edit
                </button>

                ${user.id != currentUserId ? `
                    <a
                        class="action-btn del"
                        href="users.php?delete=${user.id}"
                        onclick="return confirm('Delete this user?')"
                    >
                        Delete
                    </a>
                ` : ''}

            </td>

        </tr>

    `).join('');
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
                filterUserTable();
            </script>
            <?php require_once 'stock_alert.php'; ?>
            <?php require_once 'ai_widget.php'; ?>
            </div>
        </div>
    </div>
</body>

</html>