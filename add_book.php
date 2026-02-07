<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['type'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'simple_db.php';
global $db;

$message = "";

// Fetch authors and categories for dropdowns
$authors = $db->getAll('authors');
$categories = $db->getAll('categories');

if (isset($_POST['add_book'])) {
    $book_name = trim($_POST['book_name']);
    $author_id = (int)$_POST['author_id'];
    $category_id = (int)$_POST['category_id'];
    $book_number = trim($_POST['book_number']);
    $book_price = (float)$_POST['book_price'];
    $quantity = (int)$_POST['quantity'];

    // Input validation
    if (empty($book_name) || strlen($book_name) > 255) {
        $message = "Book name is required and must be less than 255 characters.";
    } elseif ($author_id <= 0) {
        $message = "Please select a valid author.";
    } elseif ($category_id <= 0) {
        $message = "Please select a valid category.";
    } elseif (empty($book_number) || strlen($book_number) > 50) {
        $message = "ISBN is required and must be less than 50 characters.";
    } elseif ($book_price <= 0 || $book_price > 9999.99) {
        $message = "Price must be greater than 0 and less than 10000.";
    } else {
        // Check if ISBN already exists
        $existing = $db->getWhere('books', ['isbn' => $book_number]);
        if (!empty($existing)) {
            $message = "A book with this ISBN already exists.";
        } else {
            $db->insert('books', [
                'title' => htmlspecialchars($book_name),
                'author_id' => $author_id,
                'category_id' => $category_id,
                'isbn' => htmlspecialchars($book_number),
                'price' => $book_price,
                'quantity' => $quantity,
                'available_quantity' => $quantity,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $message = "Book added successfully!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Book - Library Management System</title>
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
                <h2><i class="fas fa-plus-circle"></i> Add New Book</h2>
                <?php if ($message) {
                    $alertClass = strpos($message, 'successfully') !== false ? 'success' : 'error';
                    echo "<p class='message $alertClass'>$message</p>";
                } ?>
                <form method="post">
                    <div class="form-group">
                        <label for="book_name"><i class="fas fa-book"></i> Book Title</label>
                        <input type="text" id="book_name" name="book_name" placeholder="Enter book title" required>
                    </div>
                    <div class="form-group">
                        <label for="author_id"><i class="fas fa-user"></i> Author</label>
                        <select id="author_id" name="author_id" required>
                            <option value="">Choose an author...</option>
                            <?php foreach ($authors as $author): ?>
                                <option value="<?php echo $author['id']; ?>"><?php echo htmlspecialchars($author['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="category_id"><i class="fas fa-tags"></i> Category</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Choose a category...</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="book_number"><i class="fas fa-hashtag"></i> ISBN</label>
                        <input type="text" id="book_number" name="book_number" placeholder="Enter ISBN" required>
                    </div>
                    <div class="form-group">
                        <label for="book_price"><i class="fas fa-dollar-sign"></i> Price ($)</label>
                        <input type="number" step="0.01" id="book_price" name="book_price" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label for="quantity"><i class="fas fa-boxes"></i> Quantity</label>
                        <input type="number" min="1" id="quantity" name="quantity" placeholder="1" value="1" required>
                    </div>
                    <button type="submit" name="add_book" class="btn"><i class="fas fa-plus"></i> Add Book</button>
                </form>
            </div>
            <div class="edit-profile-footer">
                <a href="manage_books.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Books</a>
            </div>
        </div>
    </div>
</body>
</html>
