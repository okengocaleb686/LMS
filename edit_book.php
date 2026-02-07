<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['type'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'simple_db.php';
global $db;

$id = $_GET['id'] ?? null;
$book = $db->getById('books', $id);

if (!$book) {
    header("Location: manage_books.php");
    exit();
}

$message = "";
if (isset($_POST['update_book'])) {
    $book_name = $_POST['book_name'];
    $author_id = $_POST['author_id'];
    $category_id = $_POST['category_id'];
    $book_number = $_POST['book_number'];
    $book_price = $_POST['book_price'];

    if ($book_name && $author_id && $category_id && $book_number && $book_price) {
        $db->update('books', $id, [
            'title' => $book_name,
            'author_id' => $author_id,
            'category_id' => $category_id,
            'isbn' => $book_number,
            'price' => $book_price
        ]);
        $message = "Book updated successfully!";
        // Refresh book data
        $book = $db->getById('books', $id);
    } else {
        $message = "Please fill all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Book - Library Management System</title>
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
        .dashboard { max-width: 600px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; }
        form { max-width: 400px; margin: auto; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #218838; }
        .message { text-align: center; color: green; }
        .error { text-align: center; color: red; }
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
        <h1>Edit Book</h1>
        <?php if ($message) {
            if (strpos($message, 'successfully') !== false) {
                echo "<p class='message'>$message</p>";
            } else {
                echo "<p class='error'>$message</p>";
            }
        } ?>
        <form method="post">
            <input type="text" name="book_name" placeholder="Book Title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
            <input type="number" name="author_id" placeholder="Author ID" value="<?php echo htmlspecialchars($book['author_id']); ?>" required>
            <input type="number" name="category_id" placeholder="Category ID" value="<?php echo htmlspecialchars($book['category_id']); ?>" required>
            <input type="text" name="book_number" placeholder="ISBN" value="<?php echo htmlspecialchars($book['isbn']); ?>" required>
            <input type="number" step="0.01" name="book_price" placeholder="Book Price" value="<?php echo htmlspecialchars($book['price']); ?>" required>
            <button type="submit" name="update_book">Update Book</button>
        </form>
        <a href="manage_books.php">Back to Manage Books</a>
    </div>
</body>
</html>