<?php
session_start();
require_once 'simple_db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] != 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->delete('books', $id);
    $message = "Book deleted successfully!";
    header("Location: manage_books.php" . (isset($_GET['page']) ? "?page=" . $_GET['page'] : ""));
    exit();
}

// Pagination
$per_page = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$all_books = $db->getAll('books');
$total_books = count($all_books);
$total_pages = ceil($total_books / $per_page);
$offset = ($page - 1) * $per_page;
$books = array_slice($all_books, $offset, $per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - Library Management System</title>
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
        <h1><i class="fas fa-book"></i> Manage Books</h1>
        <?php if ($message) echo "<div class='message success'>$message</div>"; ?>
        <div class="text-center mb-2">
            <a href="add_book.php" class="btn">
                <i class="fas fa-plus"></i> Add New Book
            </a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>ISBN</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?php echo $book['id']; ?></td>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo $book['author_id']; ?></td>
                        <td><?php echo $book['category_id']; ?></td>
                        <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                        <td>$<?php echo number_format($book['price'], 2); ?></td>
                        <td class="actions">
                            <a href="view_book.php?id=<?php echo $book['id']; ?>" class="btn btn-secondary">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="?delete=<?php echo $book['id']; ?>&page=<?php echo $page; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this book?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Next</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <div class="text-center mt-2">
            <a href="admin_dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2024 Library Management System. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Contact Us</a>
            </div>
        </div>
    </footer>
</body>
</html>