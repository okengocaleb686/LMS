<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['type'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Dummy category data
$categories = [
    ['id' => 1, 'name' => 'Fiction'],
    ['id' => 2, 'name' => 'Non-Fiction'],
];

$message = "";
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // In real app, delete from database
    $message = "Category deleted successfully!";
    // Remove from dummy array
    $categories = array_filter($categories, function($cat) use ($id) { return $cat['id'] != $id; });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories - Library Management System</title>
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
    <div class="dashboard">
        <h1>Manage Categories</h1>
        <?php if ($message) echo "<p class='message success'>$message</p>"; ?>
        <div class="actions" style="text-align: center; margin-bottom: 1rem;">
            <a href="add_category.php" class="btn"><i class="fas fa-plus"></i> Add New Category</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-id-badge"></i> ID</th>
                        <th><i class="fas fa-tag"></i> Category Name</th>
                        <th><i class="fas fa-cogs"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo $category['id']; ?></td>
                        <td><?php echo $category['name']; ?></td>
                        <td class="actions">
                            <a href="edit_category.php?id=<?php echo $category['id']; ?>"><i class="fas fa-edit"></i> Edit</a>
                            <a href="?delete=<?php echo $category['id']; ?>" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i> Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="actions" style="text-align: center; margin-top: 1rem;">
            <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
