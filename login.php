<?php
// login.php
require_once 'config.php';
$msg = $error = '';
$selected_role = 'driver'; // Default role
$submitted_email = ''; // Store email for form prefill

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);
    $selected_role = $role; // Preserve the selected role
    $submitted_email = $email; // Store email to prefill

    $valid_roles = ['driver', 'mechanic', 'garage_owner'];
    if (in_array($role, $valid_roles)) {
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ? AND role = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("ss", $email, $role);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    
                    $uid = $user['id'];
                    $name_stmt = $conn->query("SELECT full_name FROM profiles WHERE user_id = $uid LIMIT 1");
                    if ($name_stmt && $name_row = $name_stmt->fetch_assoc()) {
                        $_SESSION['full_name'] = $name_row['full_name'];
                    } else {
                        $_SESSION['full_name'] = 'User';
                    }
                    
                    // ✅ FIX: garage_owner maps to /garage/ folder
                    $folder = ($role === 'garage_owner') ? 'garage' : $role;
                    header("Location: /roadside_ally_php/{$folder}/dashboard.php");
                    exit;
                } else {
                    $error = 'Invalid email or password.';
                }
            } else {
                $error = 'Invalid email or password for this role.';
            }
            $stmt->close();
        }
    } else {
        $error = 'Invalid role selected.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login – RoadRescue</title>
  <style>
    :root {
      --primary: #0284c7;
      --primary-hover: #0369a1;
      --secondary: #075985;
      --accent: #38bdf8;
      --muted: #f0f9ff;
      --border: #bae6fd;
      --card-bg: #FFFFFF;
      --text: #0f172a;
      --text-muted: #4a5568;
      --radius: 0.75rem;
      --error: #e11d48;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body { 
      font-family: 'Inter', system-ui, -apple-system, sans-serif; 
      background: var(--muted); 
      color: var(--text); 
      -webkit-font-smoothing: antialiased;
      display: flex;
      min-height: 100vh;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }

    .auth-wrapper { width: 100%; max-width: 420px; }

    .card { 
      background: var(--card-bg); 
      border: 1px solid var(--border); 
      border-radius: var(--radius); 
      overflow: hidden; 
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
      transition: all 0.3s ease;
    }

    .card-header { padding: 1.75rem; text-align: left; border-bottom: 1px solid var(--border); }
    .card-header h2 { font-size: 1.5rem; font-weight: 800; margin-bottom: 0.35rem; color: var(--secondary); }
    .card-header p { color: var(--text-muted); font-size: 0.875rem; }
    .card-body { padding: 1.75rem; }

    .role-selector {
      display: flex;
      gap: 0.5rem;
      margin-bottom: 1.5rem;
      background: var(--muted);
      padding: 0.35rem;
      border-radius: calc(var(--radius) - 0.25rem);
      border: 1px solid var(--border);
    }
    .role-option {
      flex: 1;
      text-align: center;
      padding: 0.5rem 0.25rem;
      font-size: 0.78rem;
      font-weight: 700;
      color: var(--secondary);
      cursor: pointer;
      border-radius: calc(var(--radius) - 0.35rem);
      transition: all 0.2s;
    }
    .role-option:hover { background: var(--border); }
    .role-option input { display: none; }
    .role-option.active {
      background: var(--primary);
      color: #FFFFFF;
    }

    .form-group { margin-bottom: 1.15rem; }
    .form-group label { display: block; font-size: 0.825rem; font-weight: 600; margin-bottom: 0.35rem; color: var(--secondary); }
    .form-group input {
      width: 100%; padding: 0.65rem 0.85rem; border: 1.5px solid var(--border);
      background-color: var(--muted);
      border-radius: var(--radius); font-size: 0.9rem; outline: none; transition: all 0.2s;
      color: var(--text);
    }
    .form-group input:focus { 
      border-color: var(--primary); 
      box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15); 
    }

    .btn { 
      display: inline-flex; 
      align-items: center; 
      justify-content: center; 
      padding: 0.65rem 1.25rem; 
      border-radius: var(--radius); 
      font-size: 0.925rem; 
      font-weight: 600; 
      cursor: pointer; 
      border: none; 
      transition: all 0.2s; 
    }
    .btn-primary { background: var(--primary); color: #fff; width: 100%; }
    .btn-primary:hover { background: var(--primary-hover); }

    .alert { padding: 0.75rem 1rem; border-radius: var(--radius); margin-bottom: 1.25rem; font-size: 0.8125rem; font-weight: 500; }
    .alert-error { background: rgba(225, 29, 72, 0.1); color: var(--error); border: 1px solid rgba(225, 29, 72, 0.2); }
    .alert-success { background: rgba(44, 104, 123, 0.1); color: var(--secondary); border: 1px solid rgba(44, 104, 123, 0.2); }

    .auth-links { margin-top: 1.25rem; text-align: center; font-size: 0.8125rem; color: var(--text-muted); }
    .auth-links a { color: var(--primary); text-decoration: none; font-weight: 600; }
    .auth-links a:hover { text-decoration: underline; }
    .auth-links p { margin-top: 0.35rem; }
  </style>
</head>
<body>
  <div class="auth-wrapper">
    <div class="card">
      <div class="card-header">
        <h2>Sign In</h2>
        <p>Choose your role and sign in to continue</p>
      </div>
      <div class="card-body">
        
        <?php if ($msg): ?>
          <div class="alert alert-success"><?= $msg ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="role-selector" id="roleSelector">
            <label class="role-option active" id="opt-driver" onclick="setRole('driver')">
              Driver
              <input type="radio" name="role" value="driver" checked>
            </label>
            <label class="role-option" id="opt-mechanic" onclick="setRole('mechanic')">
              Mechanic
              <input type="radio" name="role" value="mechanic">
            </label>
            <label class="role-option" id="opt-garage_owner" onclick="setRole('garage_owner')">
              Garage Owner
              <input type="radio" name="role" value="garage_owner">
            </label>
          </div>

          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($submitted_email) ?>" required>
          </div>
          
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
          </div>

          <button type="submit" class="btn btn-primary">Sign In</button>
        </form>

        <div class="auth-links">
          <p>Don't have an account? <a id="registerLink" href="./register.php?role=driver">Register here</a></p>
          <p><a href="/roadside_ally_php/">Back to Home</a></p>
        </div>
      </div>
    </div>
  </div>

  <script>
    function setRole(role) {
      document.querySelectorAll('.role-option input').forEach(input => input.checked = false);
      document.getElementById(`opt-${role}`).querySelector('input').checked = true;

      document.querySelectorAll('.role-option').forEach(el => el.classList.remove('active'));
      document.getElementById(`opt-${role}`).classList.add('active');

      document.getElementById('registerLink').href = './register.php?role=' + role;
      applyRoleColors(role);
    }

    function applyRoleColors(role) {
      const root = document.documentElement;
      if (role === 'driver') {
        root.style.setProperty('--primary', '#0284c7');
        root.style.setProperty('--primary-hover', '#0369a1');
        root.style.setProperty('--secondary', '#075985');
        root.style.setProperty('--accent', '#38bdf8');
        root.style.setProperty('--muted', '#f0f9ff');
        root.style.setProperty('--border', '#bae6fd');
      } else if (role === 'mechanic') {
        root.style.setProperty('--primary', '#d97706');
        root.style.setProperty('--primary-hover', '#b45309');
        root.style.setProperty('--secondary', '#78350f');
        root.style.setProperty('--accent', '#f59e0b');
        root.style.setProperty('--muted', '#fffbeb');
        root.style.setProperty('--border', '#fcd34d');
      } else if (role === 'garage_owner') {
        root.style.setProperty('--primary', '#0d9488');
        root.style.setProperty('--primary-hover', '#0f766e');
        root.style.setProperty('--secondary', '#115e59');
        root.style.setProperty('--accent', '#2dd4bf');
        root.style.setProperty('--muted', '#f0fdfa');
        root.style.setProperty('--border', '#99f6e4');
      }
    }

    // Initialize with the selected role on page load
    document.addEventListener('DOMContentLoaded', function() {
      const selectedRole = '<?= $selected_role ?>';
      setRole(selectedRole);
    });
  </script>
</body>
</html>