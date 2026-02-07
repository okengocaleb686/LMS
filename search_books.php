<?php
session_start();
require_once 'security.php';
require_login();

require_once 'simple_db.php';
global $db;

$books = [];
$query = '';

$query = sanitize_input($_POST['query'] ?? '');
$category_filter = sanitize_input($_POST['category_filter'] ?? '');
$author_filter = sanitize_input($_POST['author_filter'] ?? '');

$all_books = $db->getAll('books');
$authors = $db->getAll('authors');
$categories = $db->getAll('categories');

$author_map = array_column($authors, 'name', 'id');
$category_map = array_column($categories, 'name', 'id');

$books = array_filter($all_books, function($book) use ($query, $category_filter, $author_filter, $author_map, $category_map) {
    // Apply text search
    $search_match = true;
    if ($query) {
        $author_name = $author_map[$book['author_id']] ?? '';
        $category_name = $category_map[$book['category_id']] ?? '';
        $search_match = stripos($book['title'], $query) !== false ||
                       stripos($author_name, $query) !== false ||
                       stripos($category_name, $query) !== false ||
                       stripos($book['isbn'], $query) !== false;
    }

    // Apply category filter
    $category_match = true;
    if ($category_filter) {
        $category_match = $book['category_id'] == $category_filter;
    }

    // Apply author filter
    $author_match = true;
    if ($author_filter) {
        $author_match = $book['author_id'] == $author_filter;
    }

    return $search_match && $category_match && $author_match;
});

// Add author and category names to books
foreach ($books as &$book) {
    $book['author'] = $author_map[$book['author_id']] ?? '';
    $book['category'] = $category_map[$book['category_id']] ?? '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Books - Library Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="navbar">
        <div class="logo"><img src="IMAGES/LOGO.PNG" alt="LMS Logo"></div>
        <div class="welcome">Welcome: <?php echo $_SESSION['type'] == 'admin' ? 'Admin' : 'User'; ?>, Email: <?php echo $_SESSION['user']; ?></div>
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
        <h1><i class="fas fa-search"></i> Search Books</h1>
        <div class="form-container">
            <form method="post" class="search-form">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <div class="form-group">
                    <label for="query"><i class="fas fa-search"></i> Search Query</label>
                    <input type="text" id="query" name="query" placeholder="Search by title, author, or category" value="<?php echo htmlspecialchars($query); ?>">
                </div>
                <div class="form-group">
                    <label for="category_filter"><i class="fas fa-tags"></i> Filter by Category</label>
                    <select id="category_filter" name="category_filter">
                        <option value="">All Categories</option>
                        <?php
                        $categories = $db->getAll('categories');
                        foreach ($categories as $category) {
                            echo "<option value='{$category['id']}'" . ($category_filter == $category['id'] ? ' selected' : '') . ">{$category['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="author_filter"><i class="fas fa-user-edit"></i> Filter by Author</label>
                    <select id="author_filter" name="author_filter">
                        <option value="">All Authors</option>
                        <?php
                        $authors = $db->getAll('authors');
                        foreach ($authors as $author) {
                            echo "<option value='{$author['id']}'" . ($author_filter == $author['id'] ? ' selected' : '') . ">{$author['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="search" class="btn">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($books)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No books found.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?php echo $book['id']; ?></td>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td><?php echo htmlspecialchars($book['category']); ?></td>
                        <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                        <td>$<?php echo number_format($book['price'], 2); ?></td>
                        <td>
                            <a href="view_book.php?id=<?php echo $book['id']; ?>" class="btn btn-secondary">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="issue_book.php?book_id=<?php echo $book['id']; ?>" class="btn">
                                <i class="fas fa-hand-holding"></i> Issue
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="text-center mt-2">
            <a href="<?php echo $_SESSION['type'] == 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
    <footer class="footer">
        <div class="footer-content">
            <p>2026 Library Management System. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Contact Us</a>
            </div>
        </div>
    </footer>
</body>
</html>