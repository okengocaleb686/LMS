<?php
session_start();
require_once 'simple_db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$book_id = $_GET['book_id'] ?? null;

if (isset($_POST['issue_book'])) {
    $user_id = $_SESSION['type'] == 'admin' ? $_POST['user_id'] : $_SESSION['user_id'];
    $book_id = $_POST['book_id'];
    $issue_date = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime('+14 days')); // 14 days due

    // Check if book is available
    $book = $db->getById('books', $book_id);
    if ($book && $book['available_quantity'] > 0) {
        // Check if user already has this book issued
        $existing = $db->getWhere('issued_books', ['user_id' => $user_id, 'book_id' => $book_id, 'status' => 'issued']);
        if (empty($existing)) {
            // Issue the book
            $db->insert('issued_books', [
                'book_id' => $book_id,
                'user_id' => $user_id,
                'issue_date' => $issue_date,
                'due_date' => $due_date,
                'status' => 'issued'
            ]);

            // Decrease available quantity
            $db->update('books', $book_id, ['available_quantity' => $book['available_quantity'] - 1]);

            $message = "Book issued successfully!";
        } else {
            $message = "You already have this book issued.";
        }
    } else {
        $message = "Book is not available.";
    }
}

// If book_id is provided, pre-fill the form
$selected_book = null;
if ($book_id) {
    $selected_book = $db->getById('books', $book_id);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issue Book - Library Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); margin: 0; padding: 0; min-height: 100vh; }
        .navbar { background-color: #2c3e50; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .navbar .logo img { height: 50px; }
        .navbar .welcome { font-size: 18px; font-weight: 500; }
        .navbar .right { display: flex; align-items: center; }
        .navbar .profile { position: relative; margin-right: 20px; }
        .navbar .profile button { background: none; border: none; color: white; cursor: pointer; font-size: 16px; padding: 8px 12px; border-radius: 4px; transition: background 0.3s; }
        .navbar .profile button:hover { background: rgba(255,255,255,0.1); }
        .navbar .profile .dropdown { display: none; position: absolute; right: 0; background-color: white; min-width: 180px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; border-radius: 4px; }
        .navbar .profile .dropdown a { color: #333; padding: 12px 16px; text-decoration: none; display: block; transition: background 0.3s; }
        .navbar .profile .dropdown a:hover { background-color: #f8f9fa; }
        .navbar .profile:hover .dropdown { display: block; }
        .navbar .logout a { color: white; text-decoration: none; font-size: 16px; padding: 8px 12px; border-radius: 4px; transition: background 0.3s; }
        .navbar .logout a:hover { background: rgba(255,255,255,0.1); }
        .navmenu { background-color: #34495e; padding: 0; border-bottom: none; }
        .navmenu ul { list-style: none; margin: 0; padding: 0; display: flex; justify-content: center; flex-wrap: wrap; }
        .navmenu li { position: relative; margin: 0; }
        .navmenu a { color: white; text-decoration: none; padding: 15px 20px; display: block; transition: background 0.3s; }
        .navmenu a:hover { background-color: #2c3e50; }
        .navmenu .dropdown { position: relative; }
        .navmenu .dropdown-content { display: none; position: absolute; background-color: #34495e; min-width: 200px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; top: 100%; left: 0; border-radius: 0 0 4px 4px; }
        .navmenu .dropdown-content a { color: white; padding: 12px 16px; text-decoration: none; display: block; transition: background 0.3s; }
        .navmenu .dropdown-content a:hover { background-color: #2c3e50; }
        .navmenu .dropdown:hover .dropdown-content { display: block; }
        .dashboard { max-width: 700px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #2c3e50; margin-bottom: 30px; }
        form { max-width: 500px; margin: auto; }
        input { width: 100%; padding: 15px; margin: 15px 0; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; transition: border 0.3s; }
        input:focus { border-color: #667eea; outline: none; }
        button { width: 100%; padding: 15px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; transition: transform 0.3s; }
        button:hover { transform: translateY(-2px); }
        .message { text-align: center; color: #28a745; font-weight: 500; margin-bottom: 20px; }
        .error { text-align: center; color: #dc3545; font-weight: 500; margin-bottom: 20px; }
        .back-link { display: inline-block; margin-top: 20px; color: #667eea; text-decoration: none; font-weight: 500; transition: color 0.3s; }
        .back-link:hover { color: #764ba2; }
        @media (max-width: 768px) {
            .navbar { flex-direction: column; text-align: center; }
            .navbar .right { margin-top: 10px; }
            .navmenu ul { flex-direction: column; }
            .navmenu li { margin: 0; }
            .dashboard { padding: 20px; }
            form { max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo"><img src="IMAGES/LOGO.PNG" alt="LMS Logo"></div>
        <div class="welcome">Welcome: <?php echo $_SESSION['user_name']; ?>, Email: <?php echo $_SESSION['user']; ?></div>
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
            <?php if ($_SESSION['type'] == 'admin'): ?>
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
            <?php else: ?>
            <li><a href="user_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-book"></i> Books <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="view_books.php"><i class="fas fa-eye"></i> View Books</a>
                    <a href="search_books.php"><i class="fas fa-search"></i> Search Books</a>
                </div>
            </li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-tags"></i> Categories <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="view_categories.php"><i class="fas fa-eye"></i> View Categories</a>
                </div>
            </li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-user-edit"></i> Authors <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="view_authors.php"><i class="fas fa-eye"></i> View Authors</a>
                </div>
            </li>
            <li><a href="issue_book.php"><i class="fas fa-hand-holding"></i> Issue Book</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="dashboard">
        <h1>Issue Book</h1>
        <?php if ($message) {
            if (strpos($message, 'successfully') !== false) {
                echo "<p class='message'>$message</p>";
            } else {
                echo "<p class='error'>$message</p>";
            }
        } ?>
        <form method="post">
            <?php if ($_SESSION['type'] == 'admin'): ?>
            <input type="number" name="user_id" placeholder="User ID" required>
            <?php endif; ?>
            <input type="hidden" name="book_id" value="<?php echo $selected_book ? $selected_book['id'] : ''; ?>">
            <?php if ($selected_book): ?>
            <p><strong>Book:</strong> <?php echo $selected_book['title']; ?></p>
            <?php else: ?>
            <input type="number" name="book_id" placeholder="Book ID" required>
            <?php endif; ?>
            <button type="submit" name="issue_book"><i class="fas fa-plus"></i> Issue Book</button>
        </form>
        <a href="<?php echo $_SESSION['type'] == 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'; ?>" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
</body>
</html>