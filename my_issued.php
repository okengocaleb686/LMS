<?php
session_start();
require_once 'simple_db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Fetch issued books for the current user
$issued_data = $db->getWhere('issued_books', ['user_id' => $_SESSION['user_id']]);
$books = $db->getAll('books');
$book_map = [];
foreach ($books as $book) {
    $book_map[$book['id']] = $book['title'];
}

$issued_books = [];
foreach ($issued_data as $issue) {
    $issued_books[] = [
        'id' => $issue['id'],
        'book_name' => $book_map[$issue['book_id']] ?? 'Unknown Book',
        'issue_date' => $issue['issue_date'],
        'due_date' => $issue['due_date'],
        'return_date' => $issue['return_date'],
        'status' => ucfirst($issue['status'])
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Issued Books - Library Management System</title>
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
        .dashboard { max-width: 1200px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #2c3e50; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        tr:hover { background-color: #e9ecef; transition: background 0.3s; }
        .status-issued { color: #f39c12; font-weight: 500; }
        .status-returned { color: #27ae60; font-weight: 500; }
        .back-link { display: inline-block; margin-top: 20px; color: #667eea; text-decoration: none; font-weight: 500; transition: color 0.3s; }
        .back-link:hover { color: #764ba2; }
        @media (max-width: 768px) {
            .navbar { flex-direction: column; text-align: center; }
            .navbar .right { margin-top: 10px; }
            .navmenu ul { flex-direction: column; }
            .navmenu li { margin: 0; }
            .dashboard { padding: 20px; }
            table { font-size: 14px; }
            th, td { padding: 10px; }
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
        </ul>
    </nav>
    <div class="dashboard">
        <h1>My Issued Books</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Book Title</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($issued_books as $book): ?>
                <tr>
                    <td><?php echo $book['id']; ?></td>
                    <td><?php echo $book['book_name']; ?></td>
                    <td><?php echo $book['issue_date']; ?></td>
                    <td><?php echo $book['due_date']; ?></td>
                    <td><?php echo $book['return_date'] ?: 'Not returned'; ?></td>
                    <td class="status-<?php echo strtolower($book['status']); ?>"><?php echo $book['status']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="user_dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
</body>
</html>