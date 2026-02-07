<?php
require_once 'simple_db.php';

$message = "";
if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } elseif (empty($name) || empty($email) || empty($password)) {
        $message = "All fields are required.";
    } else {
        // Check if email already exists
        $existing = $db->getWhere('users', ['email' => $email]);
        if (!empty($existing)) {
            $message = "Email already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $user_id = $db->insert('users', [
                'email' => $email,
                'password' => $hashed_password,
                'name' => $name,
                'type' => 'user',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            if ($user_id) {
                $message = "Registration successful! You can now login.";
            } else {
                $message = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Library Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="has-navbar">
    <div class="login-container">
        <div class="logo"><img src="IMAGES/LOGO.PNG" alt="LMS Logo"></div>
        <h2><i class="fas fa-user-plus"></i> Register</h2>
        <?php if ($message): ?>
            <p class="<?php echo strpos($message, 'successful') !== false ? 'message success' : 'message error'; ?>"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="name"><i class="fas fa-user"></i> Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
            </div>
            <button type="submit" name="register" class="btn"><i class="fas fa-user-plus"></i> Register</button>
        </form>
        <div class="login-link">
            <p>Already have an account? <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login here</a></p>
        </div>
    </div>
</body>
</html>
