<?php
session_start();
require_once 'simple_db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] != 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_books.php");
    exit();
}

$id = (int)$_GET['id'];
$book = $db->getById('books', $id);

if (!$book) {
    header("Location: manage_books.php");
    exit();
}

$author = $db->getById('authors', $book['author_id']);
$category = $db->getById('categories', $book['category_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Book - Library Management System</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .navbar { background-color: #333; color: white; padding: 10px; display: flex; justify-content: space-between; align-items: center; }
        .navbar .logo img { height: 40px; }
        .navbar .welcome { font-size: 16px; }
        .navbar .right { display: flex; align-items: center; }
        .navbar .profile { position: relative; margin-right: 20px; }
        .navbar .profile button { background: none; border: none; color: white; cursor: pointer; font-size: 16px; }
        .navbar .profile .dropdown { display: none; position: absolute; right: 0; background-color: white; min-width: 160px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; }
        .navbar .profile .dropdown a { color: black; padding: 12px 16px; text-decoration: none; display: block; }
        .navbar .profile .dropdown a:hover { background-color: #f1f1f1; }
        .navbar .profile:hover .dropdown { display: block; }
        .navbar .logout a { color: white; text-decoration: none; font-size: 16px; }
        .navbar .logout a:hover { text-decoration: underline; }
        .navmenu { background-color: #f8f9fa; padding: 10px; border-bottom: 1px solid #ddd; }
        .navmenu ul { list-style: none; margin: 0; padding: 0; display: flex; justify-content: center; }
        .navmenu li { position: relative; margin: 0 15px; }
        .navmenu a { color: #333; text-decoration: none; padding: 10px; display: block; }
        .navmenu a:hover { background-color: #e9ecef; border-radius: 4px; }
        .navmenu .dropdown { position: relative; }
        .navmenu .dropdown-content { display: none; position: absolute; background-color: white; min-width: 160px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; top: 100%; left: 0; }
        .navmenu .dropdown-content a { color: black; padding: 12px 16px; text-decoration: none; display: block; }
        .navmenu .dropdown-content a:hover { background-color: #f1f1f1; }
        .navmenu .dropdown:hover .dropdown-content { display: block; }
        .dashboard { max-width: 800px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; }
        .book-details { margin-top: 20px; }
        .book-details table { width: 100%; border-collapse: collapse; }
        .book-details th, .book-details td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .book-details th { background-color: #f2f2f2; width: 30%; }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; }
        .back-link a:hover { background: #0056b3; }
    </style>
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
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li class="dropdown">
                <a href="#">Books ▼</a>
                <div class="dropdown-content">
                    <a href="manage_books.php">Manage Books</a>
                    <a href="add_book.php">Add Book</a>
                    <a href="view_books.php">View Books</a>
                    <a href="search_books.php">Search Books</a>
                </div>
            </li>
            <li class="dropdown">
                <a href="#">Categories ▼</a>
                <div class="dropdown-content">
                    <a href="manage_categories.php">Manage Categories</a>
                </div>
            </li>
            <li class="dropdown">
                <a href="#">Authors ▼</a>
                <div class="dropdown-content">
                    <a href="manage_authors.php">Manage Authors</a>
                </div>
            </li>
            <li class="dropdown">
                <a href="#">Users ▼</a>
                <div class="dropdown-content">
                    <a href="view_registered_users.php">View Registered Users</a>
                </div>
            </li>
            <li><a href="issue_book.php">Issue Book</a></li>
        </ul>
    </nav>
    <div class="dashboard">
        <h1>Book Details</h1>
        <div class="book-details">
            <table>
                <tr>
                    <th>Title</th>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                </tr>
                <tr>
                    <th>ISBN</th>
                    <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                </tr>
                <tr>
                    <th>Author</th>
                    <td><?php echo htmlspecialchars($author ? $author['name'] : 'Unknown'); ?></td>
                </tr>
                <tr>
                    <th>Category</th>
                    <td><?php echo htmlspecialchars($category ? $category['name'] : 'Unknown'); ?></td>
                </tr>
                <tr>
                    <th>Quantity</th>
                    <td><?php echo htmlspecialchars($book['quantity']); ?></td>
                </tr>
                <tr>
                    <th>Price</th>
                    <td>$<?php echo htmlspecialchars(number_format($book['price'], 2)); ?></td>
                </tr>
            </table>
        </div>
        <div class="back-link">
            <a href="manage_books.php">Back to Manage Books</a>
        </div>
    </div>
</body>
</html>