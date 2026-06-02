<?php
require '../Database/config.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Inventory_frontend/login.php");
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
    <link rel="stylesheet" href="../style.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=DM+Mono:wght@400;500&display=swap');

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
            background: #5c7fe0;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
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
            font-family: 'DM Mono', monospace;
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
            background: #4d66ff;
            color: white;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
        }

        .btn-create-user:hover {
            background: #3f57eb;
            transform: translateY(-1px);
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
            <a href="xml.php">XML Files</a>
            <a href="history.php">History</a>
            <a href="users.php" class="active">Users</a>
        </nav>

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

                window.onclick = function() {
                    const dropdown = document.getElementById("userDropdownMenu");
                    if (dropdown && dropdown.style.display === "block") {
                        dropdown.style.display = "none";
                    }
                }
                filterUserTable();
            </script>
</body>

</html>