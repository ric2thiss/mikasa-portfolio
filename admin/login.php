<?php
require_once 'auth.php';
if (isLoggedIn()) { header('Location: dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    if (attemptLogin($pdo, $user, $pass)) {
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mikasa Admin — Login</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body { display:flex; align-items:center; justify-content:center; min-height:100vh; background:var(--dark); }
        .login-card {
            background: var(--dark-2);
            border: 1px solid var(--gray-light);
            border-radius: 10px;
            padding: 3rem;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-brand {
            font-family: var(--font-heading);
            font-size: 1.8rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--light);
            margin-bottom: 0.3rem;
        }
        .login-sub {
            font-size: 0.7rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gray);
            margin-bottom: 2.5rem;
        }
        .login-input {
            width: 100%;
            padding: 0.85rem 1rem;
            margin-bottom: 1rem;
            background: var(--dark-3);
            border: 1px solid var(--gray-light);
            border-radius: 4px;
            color: var(--light);
            font-family: var(--font-body);
            font-size: 0.9rem;
        }
        .login-input:focus { outline:none; border-color: var(--gray); }
        .login-btn {
            width: 100%;
            padding: 0.9rem;
            background: var(--light);
            color: var(--dark);
            border: none;
            font-family: var(--font-body);
            font-size: 0.8rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 0.5rem;
            transition: background 0.3s ease;
        }
        .login-btn:hover { background: var(--light-2); }
        .login-error {
            background: rgba(255,80,80,0.1);
            color: #ff5050;
            border: 1px solid rgba(255,80,80,0.2);
            padding: 0.7rem;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-brand">Mikasa</div>
        <div class="login-sub">Admin Dashboard</div>
        <?php if ($error): ?>
            <div class="login-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" class="login-input" placeholder="Username" required>
            <input type="password" name="password" class="login-input" placeholder="Password" required>
            <button type="submit" class="login-btn">Sign In</button>
        </form>
    </div>
</body>
</html>
