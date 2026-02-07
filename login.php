<?php
session_start();
require_once 'simple_db.php';
require_once 'security.php';

// Rate limiting for login attempts
if (!check_rate_limit('login_' . $_SERVER['REMOTE_ADDR'], 5, 300)) {
    $error = "Too many login attempts. Please try again later.";
} elseif (isset($_POST['login'])) {
    // Validate CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security error. Please try again.";
        log_security_event('CSRF violation', 'Login attempt');
    } else {
        $email = sanitize_input($_POST['email']);
        $password = $_POST['password'];

        if (!validate_email($email)) {
            $error = "Invalid email format.";
        } else {
            $users = $db->getWhere('users', ['email' => $email]);
            if (!empty($users)) {
                $user = $users[0];
                if (password_verify($password, $db->decryptData($user['password']))) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user'] = $user['email'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['type'] = $user['type'];
                    log_security_event('Successful login', $email);
                    if ($user['type'] == 'admin') {
                        header("Location: admin_dashboard.php");
                    } else {
                        header("Location: user_dashboard.php");
                    }
                    exit();
                } else {
                    log_security_event('Failed login', $email . ' - wrong password');
                }
            } else {
                log_security_event('Failed login', $email . ' - user not found');
            }
            $error = "Invalid email or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('IMAGES/classroom.jpg') center/cover;
            opacity: 0.1;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="IMAGES/LOGO.PNG" alt="LMS Logo">
        </div>
        <h2>Welcome Back</h2>
        <p class="text-center mb-2" style="color: #666;">Sign in to your account</p>
        <?php if (isset($error)) echo "<div class='message error'>$error</div>"; ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" name="login" class="btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        <div class="mt-2">
            <p>Don't have an account?
                <a href="register.php" style="color: #667eea; text-decoration: none; font-weight: 600;">
                    Register here
                </a>
            </p>
        </div>
    </div>
</body>
</html>