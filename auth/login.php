<?php
/**
 * HabitForge — User Login
 * 
 * Authenticates users against stored credentials,
 * verifies hashed passwords, and initiates sessions.
 */

session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: ../dashboard.php');
    exit();
}

$errors = [];

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate inputs
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!is_valid_email($email)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // Authenticate
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            // Login successful — create session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            // Regenerate session ID for security
            session_regenerate_id(true);

            header('Location: ../dashboard.php');
            exit();
        } else {
            $errors[] = "Invalid email or password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Log in to HabitForge — Access your habit dashboard and keep your streaks alive.">
  <title>Log In — HabitForge</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔥</text></svg>">
</head>
<body>
  <div class="bg-particles">
    <div class="orb"></div>
    <div class="orb"></div>
    <div class="orb"></div>
  </div>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-forge scrolled">
    <div class="container">
      <a class="navbar-brand" href="../index.php">
        <span class="brand-icon">🔥</span>HabitForge
      </a>
      <div class="nav-auth d-flex align-items-center gap-2">
        <a href="register.php" class="btn-forge btn-forge-sm btn-forge-primary">Sign Up</a>
      </div>
    </div>
  </nav>

  <!-- Login Form -->
  <div class="section" style="padding-top: 120px; min-height: 100vh; display: flex; align-items: center;">
    <div class="container">
      <div class="form-forge">
        <div class="form-card">
          <div class="form-header">
            <h2>Welcome Back</h2>
            <p>Log in to continue your streak 🔥</p>
          </div>

          <?php if (!empty($errors)): ?>
            <div class="alert-forge error">
              <i class="bi bi-exclamation-circle-fill"></i>
              <div>
                <?php foreach ($errors as $err): ?>
                  <div><?php echo htmlspecialchars($err); ?></div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if (isset($_GET['registered'])): ?>
            <div class="alert-forge success">
              <i class="bi bi-check-circle-fill"></i>
              Account created successfully! Please log in.
            </div>
          <?php endif; ?>

          <?php if (isset($_GET['error'])): ?>
            <div class="alert-forge warning">
              <i class="bi bi-exclamation-triangle-fill"></i>
              <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
          <?php endif; ?>

          <form id="loginForm" method="POST" action="login.php" novalidate>
            <div class="form-group">
              <label for="login-email"><i class="bi bi-envelope"></i> Email Address</label>
              <input type="email" class="form-control-forge" id="login-email" name="email" 
                     placeholder="you@example.com"
                     value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                     required>
              <div class="error-message">Valid email is required</div>
            </div>

            <div class="form-group">
              <label for="login-password"><i class="bi bi-lock"></i> Password</label>
              <input type="password" class="form-control-forge" id="login-password" name="password" 
                     placeholder="Enter your password" required>
              <div class="error-message">Password is required</div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                <label class="form-check-label text-secondary" for="rememberMe" style="font-size: 0.85rem;">
                  Remember me
                </label>
              </div>
            </div>

            <button type="submit" class="btn-forge btn-forge-primary w-100" id="loginBtn">
              <i class="bi bi-box-arrow-in-right"></i> Log In
            </button>
          </form>

          <div class="form-divider">or</div>

          <div class="form-footer">
            Don't have an account? <a href="register.php">Create one now</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../js/main.js"></script>
</body>
</html>
