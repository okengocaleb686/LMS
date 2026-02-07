<?php
session_start();
if (!isset($_SESSION['user']) || !in_array($_SESSION['type'], ['user', 'admin'])) {
    header("Location: login.php");
    exit();
}

$message = "";
if (isset($_POST['update'])) {
    // In a real app, update database
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $target_dir = "IMAGES/";
        $target_file = $target_dir . "PROFILE.jpg";
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file);
        $message = "Profile and picture updated successfully!";
    } else {
        $message = "Profile updated successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - Library Management System</title>
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
                <h2>Edit Profile</h2>
                <?php if ($message) echo "<p class='message success'>$message</p>"; ?>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="<?php echo $_SESSION['type'] == 'admin' ? 'Admin' : 'User'; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $_SESSION['user']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="profile_pic">Profile Picture</label>
                        <input type="file" id="profile_pic" name="profile_pic" accept="image/*">
                    </div>
                    <button type="submit" name="update" class="btn">Update Profile</button>
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
