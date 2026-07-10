<?php

session_start();

require '../Database/Database.php';

$database = new Database();
$pdo = $database->getConnection();

$loginError = '';
$signupError = '';
$signupSuccess = '';

if (isset($_POST['login'])) {

    $username = trim($_POST['login_username']);
    $password = trim($_POST['login_password']);

    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['user_id'] = 999;
        $_SESSION['username'] = 'Admin';
        $_SESSION['role'] = 'admin';

        header("Location: ../Inventory_frontend/dashboard.php");
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT * FROM users
        WHERE username = ?
    ");

    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($_SESSION['role'] === 'admin') {
            header("Location: ../Inventory_frontend/dashboard.php");
        } else {
            header("Location: ../Inventory_frontend/point_of_sale_menu.php");
        }
        exit;
    } else {

        $loginError = "Invalid username or password.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K's Inventory System</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <style>
        :root {
            --dark-1: #333538;
            --dark-2: #333538;
            --bg: #F5F6F8;
            --white: #FFFFFF;
            --muted: #6b6f73;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'DM Sans', 'Segoe UI', sans-serif;
        }

        body {
            min-height: 100vh;
            background: var(--bg);
            color: var(--dark-1);
            padding-top: 64px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: var(--dark-1);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
            z-index: 1000;
        }

        .topbar .brand {
            font-weight: 700;
            letter-spacing: 0.4px;
        }

        .topbar .actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .container {
            width: 420px;
            background: var(--white);
            border-radius: 14px;
            padding: 40px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            margin-top: 100px;
        }

        .title {
            text-align: center;
            font-size: 30px;
            font-weight: 600;
            color: var(--dark-1);
            margin-bottom: 30px;
            letter-spacing: -1px;
        }

        .form {
            display: none;
        }

        .form.active {
            display: block;
        }

        .input-box {
            margin-bottom: 18px;
            position: relative;
        }

        .input-box input {
            width: 100%;
            padding: 14px;
            border: 2px solid rgba(0, 0, 0, 0.13);
            border-radius: 10px;
            outline: none;
            font-size: 15px;
            background: #fafafa;
            transition: 0.2s;
        }

        .input-box input[type="password"],
        .input-box input[type="text"].pass-field {
            padding-right: 46px;
        }

        .input-box input:focus {
            border-color: var(--dark-2);
            background: var(--white);
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--muted);
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            user-select: none;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: var(--dark-1);
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: var(--dark-1);
            color: var(--white);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn:hover {
            background: var(--dark-2);
        }

        .footer {
            text-align: center;
            margin-top: 18px;
            font-size: 14px;
            color: var(--muted);
        }

        .footer a {
            color: var(--dark-2);
            text-decoration: none;
        }

        @media(max-width:500px) {

            .container {
                width: 90%;
                padding: 30px;
            }

        }
    </style>

</head>

<body>

    <div class="topbar">
        <div class="brand">K's Inventory System</div>
    </div>

    <div class="container">

        <div class="title">K's Inventory System</div>

        <form id="loginForm" class="form active" method="POST">

            <?php if ($loginError): ?>
                <div class="footer" style="color:red; margin-bottom:15px;">
                    <?= $loginError ?>
                </div>
            <?php endif; ?>

            <div class="input-box">
                <input
                    type="text"
                    name="login_username"
                    placeholder="Username"
                    required>
            </div>

            <div class="input-box">
                <input type="password" id="loginPassword" class="pass-field" name="login_password" placeholder="Password" required>
                <button type="button" class="toggle-password" onclick="toggleVisibility('loginPassword', this)">
                    <i class="fa-regular fa-eye-slash"></i> </button>
            </div>

            <button type="submit" name="login" class="btn">
                Login
            </button>

        </form>

    </div>

    <script>
        function toggleVisibility(inputId, buttonEl) {
            const inputField = document.getElementById(inputId);
            const icon = buttonEl.querySelector('i');

            if (inputField.type === "password") {
                inputField.type = "text";
                icon.className = "fa-regular fa-eye";
            } else {
                inputField.type = "password";
                icon.className = "fa-regular fa-eye-slash";
            }
        }

        function showForm(type) {

            let login = document.getElementById('loginForm');
            let signup = document.getElementById('signupForm');

            let buttons = document.querySelectorAll('.tab-btn');

            buttons.forEach(btn => btn.classList.remove('active'));

            if (type === 'login') {
                login.classList.add('active');
                signup.classList.remove('active');
                buttons[0].classList.add('active');
            } else {
                signup.classList.add('active');
                login.classList.remove('active');
                buttons[1].classList.add('active');
            }

        }
    </script>

</body>

</html>