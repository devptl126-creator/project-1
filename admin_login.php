<?php
require_once 'config.php'; // Includes session and database functions

if (isLoggedIn()) {
    $role = $_SESSION['role'];
    if ($role === 'admin') {
        redirect('/roadside_ally_php/admin/dashboard.php');
    } else {
        session_destroy();
        redirect('/roadside_ally_php/admin_login.php');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT u.id, u.password, u.role, p.full_name FROM users u JOIN profiles p ON p.user_id = u.id WHERE u.email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result && password_verify($password, $result['password'])) {
        // Verify role is strictly admin
        if ($result['role'] === 'admin') {
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['role'] = $result['role'];
            $_SESSION['full_name'] = $result['full_name'];
            redirect('/roadside_ally_php/admin/dashboard.php');
        } else {
            $error = 'Access denied: Administrator accounts only.';
        }
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Portal – RoadRescue</title>
  <link rel="stylesheet" href="/roadside_ally_php/assets/css/style.css">
  <style>
    :root {
      --primary: #b91c1c !important;
      --primary-hover: #991b1b !important;
    }
    .card-header {
      border-bottom: 1px solid var(--border);
      background-color: #fef2f2;
    }
    .brand {
      color: #b91c1c;
    }
    .btn-primary {
      background-color: #b91c1c;
    }
    .btn-primary:hover {
      background-color: #991b1b;
    }
  </style>
</head>
<body class="auth-bg">

  <div class="auth-wrapper">
    <div class="card">
      <div class="card-header">
        <a href="/roadside_ally_php/index.php" class="brand">
          <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2Z"/><path d="M9 17v2"/><path d="M15 17v2"/></svg>
          RoadRescue
        </a>
        <h2>Admin Portal</h2>
        <p>Sign in to the system administrator dashboard</p>
      </div>
      <div class="card-body">
        <?php if ($error): ?>
          <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="you@example.com" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
          </div>
          <button type="submit" class="btn btn-primary btn-full">Sign In</button>
          <div class="auth-links">
            <p>Not an administrator? <a href="/roadside_ally_php/login.php">Return to Main Login</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>
</html>