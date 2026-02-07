<?php
// Database setup script for Library Management System

require_once 'db_config.php';

try {
    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        type ENUM('admin', 'user') NOT NULL DEFAULT 'user',
        profile_pic VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create categories table
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) UNIQUE NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create authors table
    $pdo->exec("CREATE TABLE IF NOT EXISTS authors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) UNIQUE NOT NULL,
        bio TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create books table
    $pdo->exec("CREATE TABLE IF NOT EXISTS books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        author_id INT,
        category_id INT,
        isbn VARCHAR(20) UNIQUE,
        description TEXT,
        quantity INT DEFAULT 1,
        available_quantity INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE SET NULL,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    )");

    // Create issued_books table
    $pdo->exec("CREATE TABLE IF NOT EXISTS issued_books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        book_id INT NOT NULL,
        user_id INT NOT NULL,
        issue_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        due_date TIMESTAMP,
        return_date TIMESTAMP NULL,
        status ENUM('issued', 'returned', 'overdue') DEFAULT 'issued',
        FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Insert default admin user
    $admin_password = password_hash('admin@1234', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (email, password, name, type) VALUES (?, ?, ?, ?)");
    $stmt->execute(['admin@gmail.com', $admin_password, 'Administrator', 'admin']);

    // Insert default user
    $user_password = password_hash('user@1234', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (email, password, name, type) VALUES (?, ?, ?, ?)");
    $stmt->execute(['user@gmail.com', $user_password, 'Regular User', 'user']);

    // Insert sample categories
    $categories = [
        ['Fiction', 'Fictional books and novels'],
        ['Non-Fiction', 'Educational and informative books'],
        ['Science', 'Books related to science and technology'],
        ['History', 'Historical books and biographies']
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name, description) VALUES (?, ?)");
    foreach ($categories as $cat) {
        $stmt->execute($cat);
    }

    // Insert sample authors
    $authors = [
        ['J.K. Rowling', 'British author best known for the Harry Potter series'],
        ['George Orwell', 'English novelist and essayist'],
        ['Stephen King', 'American author of horror, supernatural fiction'],
        ['Agatha Christie', 'English writer known for detective novels']
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO authors (name, bio) VALUES (?, ?)");
    foreach ($authors as $author) {
        $stmt->execute($author);
    }

    // Insert sample books
    $books = [
        ['Harry Potter and the Philosopher\'s Stone', 1, 1, '9780747532699', 'The first book in the Harry Potter series', 5, 5],
        ['1984', 2, 2, '9780451524935', 'A dystopian social science fiction novel', 3, 3],
        ['The Shining', 3, 1, '9780307743657', 'A horror novel about a family in an isolated hotel', 2, 2],
        ['Murder on the Orient Express', 4, 1, '9780062693662', 'A detective novel featuring Hercule Poirot', 4, 4]
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO books (title, author_id, category_id, isbn, description, quantity, available_quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($books as $book) {
        $stmt->execute($book);
    }

    echo "Database setup completed successfully!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>