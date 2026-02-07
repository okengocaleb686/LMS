<?php
session_start();
if (!isset($_SESSION['user']) || !in_array($_SESSION['type'], ['user', 'admin'])) {
    header("Location: login.php");
    exit();
}

// Get user details from data file
$usersFile = 'data/users.json';
$userEmail = $_SESSION['user'];
$userData = ['name' => 'User', 'email' => $userEmail, 'role' => 'User'];

if (file_exists($usersFile)) {
    $users = json_decode(file_get_contents($usersFile), true);
    foreach ($users as $user) {
        if ($user['email'] === $userEmail) {
            $userData = $user;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - Library Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-picture">
                    <img src="IMAGES/PROFILE.jpg" alt="Profile Picture">
                </div>
            </div>
            <div class="profile-body">
                <h2><?php echo htmlspecialchars($userData['name']); ?></h2>
                <p class="email"><?php echo htmlspecialchars($userData['email']); ?></p>
                <p class="role"><?php echo htmlspecialchars(ucfirst($userData['role'])); ?></p>
            </div>
            <div class="profile-footer">
                <a href="edit_profile.php" class="btn">Edit Profile</a>
                <a href="user_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>