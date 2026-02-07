<?php
session_start();
if (!isset($_SESSION['user']) || !in_array($_SESSION['type'], ['user', 'admin'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$messageClass = "";
if (isset($_POST['change'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Hardcoded check
    $old_pass = $_SESSION['type'] == 'admin' ? 'admin@1234' : 'user@1234';
    if ($old_password == $old_pass && $new_password == $confirm_password) {
        $message = "Password changed successfully!";
        $messageClass = "success";
    } else {
        $message = "Invalid old password or passwords do not match.";
        $messageClass = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password - Library Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="profile-container">
        <div class="edit-profile-card">
            <div class="edit-profile-header">
                <div class="profile-picture">
                    <img src="IMAGES/PROFILE.jpg" alt="Profile Picture">
                </div>
            </div>
            <div class="edit-profile-body">
                <h2>Change Password</h2>
                <?php if ($message) echo "<p class='message $messageClass'>$message</p>"; ?>
                <form method="post">
                    <div class="form-group">
                        <label for="old_password">Old Password</label>
                        <input type="password" id="old_password" name="old_password" placeholder="Enter old password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                    </div>
                    <button type="submit" name="change" class="btn">Change Password</button>
                </form>
            </div>
            <div class="edit-profile-footer">
                <a href="view_profile.php" class="btn btn-secondary">Cancel</a>
                <a href="user_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
