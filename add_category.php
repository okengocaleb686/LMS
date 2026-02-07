<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['type'] != 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";
if (isset($_POST['add_category'])) {
    // In a real app, insert into database
    $category_name = $_POST['category_name'];

    // Dummy validation
    if ($category_name) {
        $message = "Category added successfully!";
    } else {
        $message = "Please enter category name.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Category - Library Management System</title>
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
                <h2><i class="fas fa-tag"></i> Add New Category</h2>
                <?php if ($message) {
                    if (strpos($message, 'successfully') !== false) {
                        echo "<p class='message success'>$message</p>";
                    } else {
                        echo "<p class='message error'>$message</p>";
                    }
                } ?>
                <form method="post">
                    <div class="form-group">
                        <label for="category_name"><i class="fas fa-tags"></i> Category Name</label>
                        <input type="text" id="category_name" name="category_name" placeholder="Enter category name" required>
                    </div>
                    <button type="submit" name="add_category" class="btn"><i class="fas fa-plus"></i> Add Category</button>
                </form>
            </div>
            <div class="edit-profile-footer">
                <a href="manage_categories.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Categories</a>
            </div>
        </div>
    </div>
</body>
</html>
