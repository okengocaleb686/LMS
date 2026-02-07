<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['type'] != 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";
if (isset($_POST['add_user'])) {
    // In a real app, insert into database
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $type = $_POST['type'];

    // Dummy validation
    if ($user_id && $name && $email && $type) {
        $message = "User added successfully!";
    } else {
        $message = "Please fill all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User - Library Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="navbar">
        <div class="logo"><img src="IMAGES/LOGO.PNG" alt="LMS Logo"></div>
        <div class="welcome">Welcome: Admin, Email: <?php echo $_SESSION['user']; ?></div>
        <div class="right">
            <div class="profile">
                <button>My Profile ▼</button>
                <div class="dropdown">
                    <a href="view_profile.php">View Profile</a>
                    <a href="edit_profile.php">Edit Profile</a>
                    <a href="change_password.php">Change Password</a>
                </div>
            </div>
            <div class="logout">
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
    <nav class="navmenu">
        <ul>
            <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-book"></i> Books <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="manage_books.php"><i class="fas fa-cogs"></i> Manage Books</a>
                    <a href="add_book.php"><i class="fas fa-plus"></i> Add Book</a>
                </div>
            </li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-tags"></i> Categories <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="manage_categories.php"><i class="fas fa-cogs"></i> Manage Categories</a>
                </div>
            </li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-user-edit"></i> Authors <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="manage_authors.php"><i class="fas fa-cogs"></i> Manage Authors</a>
                </div>
            </li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-users"></i> Users <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="view_registered_users.php"><i class="fas fa-eye"></i> View Registered Users</a>
                    <a href="add_user.php"><i class="fas fa-plus"></i> Add User</a>
                </div>
            </li>
            <li><a href="issue_book.php"><i class="fas fa-hand-holding"></i> Issue Book</a></li>
        </ul>
    </nav>
    <div class="profile-container">
        <div class="edit-profile-card">
            <div class="edit-profile-header">
                <div class="profile-picture">
                    <img src="IMAGES/PROFILE.jpg" alt="Profile Picture">
                </div>
            </div>
            <div class="edit-profile-body">
                <h2>Add New User</h2>
                <?php if ($message) {
                    if (strpos($message, 'successfully') !== false) {
                        echo "<p class='message success'>$message</p>";
                    } else {
                        echo "<p class='message error'>$message</p>";
                    }
                } ?>
                <form method="post">
                    <div class="form-group">
                        <label for="user_id">User ID</label>
                        <input type="number" id="user_id" name="user_id" placeholder="Enter user ID" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter email" required>
                    </div>
                    <div class="form-group">
                        <label for="type">User Type</label>
                        <select id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" name="add_user" class="btn">Add User</button>
                </form>
            </div>
            <div class="edit-profile-footer">
                <a href="view_registered_users.php" class="btn btn-secondary">Back to Users</a>
            </div>
        </div>
    </div>
</body>
</html>
