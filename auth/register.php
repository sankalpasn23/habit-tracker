<?php
/**
 * HabitForge — User Registration
 * 
 * Handles new user sign-up with server-side validation,
 * password hashing, and duplicate checking.
 */

session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

$errors = [];
$success = false;

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validate username
    $usernameCheck = validate_username($username);
    if ($usernameCheck !== true) {
        $errors[] = $usernameCheck;
    }

    // Validate email
    if (!is_valid_email($email)) {
        $errors[] = "Please enter a valid email address.";
    }

    // Validate password
    $passwordCheck = validate_password($password);
    if ($passwordCheck !== true) {
        $errors[] = $passwordCheck;
    }

    // Check passwords match
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    // Check for duplicate username
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = "Username is already taken.";
        }
        $stmt->close();
    }

    // Check for duplicate email
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = "An account with this email already exists.";
        }
        $stmt->close();
    }

    // Register the user
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())"
        );
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        if ($stmt->execute()) {
            $success = true;
            // Auto-login after registration
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $stmt->close();

            // Redirect to dashboard
            header('Location: ../dashboard.php');
            exit();
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Create your HabitForge account — Start building better habits today.">
  <title>Sign Up — HabitForge</title>
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
        <a href="login.php" class="btn-login">Log In</a>
      </div>
    </div>
  </nav>

  <!-- Registration Form -->
  <div class="section" style="padding-top: 120px; min-height: 100vh; display: flex; align-items: center;">
    <div class="container">
      <div class="form-forge">
        <div class="form-card">
          <div class="form-header">
            <h2>Create Account</h2>
            <p>Start your habit-building journey today</p>
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

          <form id="registerForm" method="POST" action="register.php" novalidate>
            <div class="form-group">
              <label for="reg-username"><i class="bi bi-person"></i> Username</label>
              <input type="text" class="form-control-forge" id="reg-username" name="username" 
                     placeholder="Choose a username" 
                     value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" 
                     required>
              <div class="error-message">Username must be at least 3 characters</div>
            </div>

            <div class="form-group">
              <label for="reg-email"><i class="bi bi-envelope"></i> Email Address</label>
              <input type="email" class="form-control-forge" id="reg-email" name="email" 
                     placeholder="you@example.com"
                     value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" 
                     required>
              <div class="error-message">Valid email is required</div>
            </div>

            <div class="form-group">
              <label for="reg-password"><i class="bi bi-lock"></i> Password</label>
              <input type="password" class="form-control-forge" id="reg-password" name="password" 
                     placeholder="Create a strong password" required>
              <div class="error-message">Password must be at least 8 characters</div>
              <div class="password-strength">
                <div class="strength-bar"></div>
                <div class="strength-bar"></div>
                <div class="strength-bar"></div>
                <div class="strength-bar"></div>
              </div>
              <div class="strength-label"></div>
            </div>

            <div class="form-group">
              <label for="reg-confirm-password"><i class="bi bi-lock-fill"></i> Confirm Password</label>
              <input type="password" class="form-control-forge" id="reg-confirm-password" name="confirm_password" 
                     placeholder="Re-enter your password" required>
              <div class="error-message">Passwords do not match</div>
            </div>

            <button type="submit" class="btn-forge btn-forge-primary w-100" id="registerBtn">
              <i class="bi bi-rocket-takeoff"></i> Create Account
            </button>
          </form>

          <div class="form-divider">or</div>

          <div class="form-footer">
            Already have an account? <a href="login.php">Log in here</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../js/main.js"></script>
</body>
</html>
