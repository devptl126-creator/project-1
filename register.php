<?php
require_once 'config.php';

if (isLoggedIn()) redirect('/roadside_ally_php/login.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['full_name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    $role     = $_POST['role'];

    $allowed_roles = ['driver', 'garage_owner', 'mechanic'];

    if (!in_array($role, $allowed_roles)) {
        $error = 'Invalid role selected.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        // Check email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Email already registered.';
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $email, $hashed, $role);
                $stmt->execute();
                $user_id = $conn->insert_id;

                $stmt = $conn->prepare("INSERT INTO profiles (user_id, full_name, phone) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $user_id, $name, $phone);
                $stmt->execute();

                // If mechanic, create mechanic record
                if ($role === 'mechanic') {
                    $stmt = $conn->prepare("INSERT INTO mechanics (user_id) VALUES (?)");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                }

                // If garage owner, create a default garage record
                if ($role === 'garage_owner') {
                    $stmt = $conn->prepare("INSERT INTO garages (owner_id, name) VALUES (?, 'My Garage')");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                }

                $conn->commit();
                $success = 'Account created! You can now log in.';
            } catch (Exception $e) {
                $conn->rollback();
                $error = 'Registration failed. Please try again.';
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
  <title>Register – RoadRescue</title>
  <link rel="stylesheet" href="/roadside_ally_php/assets/css/style.css">
</head>
<body class="auth-bg">

  <div class="auth-wrapper" style="max-width:480px">
    <div class="card">
      <div class="card-header">
        <a href="/roadside_ally_php/index.php" class="brand">
          <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2Z"/><path d="M9 17v2"/><path d="M15 17v2"/></svg>
          RoadRescue
        </a>
        <h2>Create Account</h2>
        <p>Sign up to get started</p>
      </div>
      <div class="card-body">
        <?php if ($error): ?>
          <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="POST">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" placeholder="John Doe" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="you@example.com" required>
          </div>
          <div class="form-group">
            <label>Phone</label>
            <input type="tel" name="phone" placeholder="+91 9999999999">
          </div>
          <div class="form-group">
            <label>Role</label>
            <select name="role" required>
              <option value="">Select your role</option>
              <option value="driver">Driver</option>
              <option value="garage_owner">Garage Owner</option>
              <option value="mechanic">Mechanic</option>
            </select>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
          </div>
          <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="••••••••" required>
          </div>
          <button type="submit" class="btn btn-primary btn-full">Create Account</button>
          <div class="auth-links">
            <p>Already have an account? <a href="/roadside_ally_php/login.php">Sign in</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>
</html>