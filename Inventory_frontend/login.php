<?php

session_start();

require '../Database/Database.php';

$database = new Database();
$pdo = $database->getConnection();

$loginError = '';
$signupError = '';
$signupSuccess = '';

if (isset($_POST['login'])) {
    $now = time();
    $maxAttempts = 5;
    $lockoutTime = 60; // 60 seconds cooldown

    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt_time'] = $now;
    }

    // Reset attempt count if lockout period has passed
    if (($now - $_SESSION['last_attempt_time']) > $lockoutTime) {
        $_SESSION['login_attempts'] = 0;
    }

    // Enforce rate limiting lockout
    if ($_SESSION['login_attempts'] >= $maxAttempts) {
        $remainingLock = $lockoutTime - ($now - $_SESSION['last_attempt_time']);
        $loginError = "Too many failed login attempts. Please wait {$remainingLock} seconds before trying again.";
    } else {
        $username = trim($_POST['login_username']);
        $password = trim($_POST['login_password']);

        if ($username === 'admin' && $password === 'admin') {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['user_id'] = 0;
            $_SESSION['username'] = 'Admin';
            $_SESSION['role'] = 'admin';

            $ip = $_SERVER['REMOTE_ADDR'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'];

            $stmt = $pdo->prepare("
                INSERT INTO login_logs
                (user_id, username, ip_address, user_agent)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $_SESSION['user_id'],
                $_SESSION['username'],
                $ip,
                $userAgent
            ]);

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
            $_SESSION['login_attempts'] = 0;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            $ip = $_SERVER['REMOTE_ADDR'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'];

            $stmt = $pdo->prepare("
                INSERT INTO login_logs
                (user_id, username, ip_address, user_agent)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $user['id'],
                $user['username'],
                $ip,
                $userAgent
            ]);

            if ($_SESSION['role'] === 'admin') {
                header("Location: ../Inventory_frontend/dashboard.php");
            } else {
                header("Location: ../Inventory_frontend/point_of_sale_menu.php");
            }
            exit;
        } else {
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt_time'] = $now;
            $remaining = $maxAttempts - $_SESSION['login_attempts'];
            if ($remaining > 0) {
                $loginError = "Invalid username or password. ({$remaining} attempts remaining)";
            } else {
                $loginError = "Too many failed attempts. Account locked for 60 seconds.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kinetix Inventory System — Login</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <style>
        :root {
            --bg: #f0f1f4;
            --brand: #1B4EF5;
            --brand-hover: #153ec3;
            --text: #1a1c20;
            --text-muted: #64748b;
            --border: #dde1e9;
            --card-bg: #ffffff;
            --error-bg: #fef2f2;
            --error-text: #ef4444;
            --error-border: #fee2e2;
        }

        html,
        body {
            height: 100%;
            overflow: hidden;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'DM Sans', 'Segoe UI', sans-serif;
        }

        body {
            background: var(--bg);
            color: var(--text);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: var(--card-bg);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.13), 0 20px 48px rgba(0, 0, 0, 0.13);
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .brand-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 32px;
            text-align: center;
        }

        .brand-logo-icon {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            background: var(--brand);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(27, 78, 245, 0.25);
        }

        .brand-logo-icon img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .brand-name {
            font-size: 1.45rem;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        .brand-sub {
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 2px;
        }

        .alert-error {
            background: var(--error-bg);
            color: var(--error-text);
            border: 1px solid var(--error-border);
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            line-height: 1.4;
        }

        .alert-error i {
            margin-top: 2px;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group label {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i.input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.95rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            outline: none;
            font-size: 0.93rem;
            background: #fafafa;
            color: var(--text);
            transition: all 0.2s ease;
        }

        .input-wrapper input:focus {
            border-color: var(--brand);
            background: var(--card-bg);
            box-shadow: 0 0 0 3px rgba(27, 78, 245, 0.15);
        }

        .input-wrapper input:focus+i.input-icon {
            color: var(--brand);
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            user-select: none;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: var(--text);
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: var(--brand);
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(27, 78, 245, 0.15);
            margin-top: 4px;
        }

        .btn-submit:hover {
            background: var(--brand-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(27, 78, 245, 0.25);
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(27, 78, 245, 0.15);
        }

        @media(max-width: 480px) {
            .login-card {
                padding: 30px 24px;
            }
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="brand-header">
            <div class="brand-logo-icon">
                <img src="../Images/logo.svg" alt="Logo">
            </div>
            <h1 class="brand-name">Kinetix</h1>
            <span class="brand-sub">Inventory System</span>
        </div>

        <form id="loginForm" method="POST">
            <?php if ($loginError): ?>
                <div class="alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= htmlspecialchars($loginError) ?></span>
                </div>
            <?php endif; ?>

            <div class="input-group">
                <label for="username">Username</label>
                <div class="input-wrapper">
                    <input
                        type="text"
                        id="username"
                        name="login_username"
                        placeholder="Enter username"
                        required>
                    <i class="fa-solid fa-user input-icon"></i>
                </div>
            </div>

            <div class="input-group">
                <label for="loginPassword">Password</label>
                <div class="input-wrapper">
                    <input
                        type="password"
                        id="loginPassword"
                        name="login_password"
                        placeholder="Enter password"
                        required>
                    <i class="fa-solid fa-lock input-icon"></i>
                    <button type="button" class="toggle-password" onclick="toggleVisibility('loginPassword', this)">
                        <i class="fa-regular fa-eye-slash"></i>
                    </button>
                </div>
            </div>

            <button type="submit" name="login" class="btn-submit">
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
        sessionStorage.removeItem("sidebarState");
    </script>

</body>

</html>