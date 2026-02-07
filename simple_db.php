<?php
// Simple file-based database for demo purposes

class SimpleDB {
    /** @var string Directory where data files are stored */
    private $data_dir = '../library_data/';

    /** @var array List of database tables */
    private $tables = ['users', 'books', 'authors', 'categories', 'issued_books', 'reservations', 'fines', 'notifications'];

    public function __construct() {
        if (!is_dir($this->data_dir)) {
            mkdir($this->data_dir, 0755, true);
        }
        foreach ($this->tables as $table) {
            if (!file_exists($this->data_dir . $table . '.json')) {
                file_put_contents($this->data_dir . $table . '.json', json_encode([]));
            }
        }
        $this->initializeData();
    }

    private function encryptData($data) {
        $key = 'your-encryption-key-here'; // TODO: Move to config
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    public function decryptData($data) {
        $key = 'your-encryption-key-here'; // TODO: Move to config
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
    }

    private function initializeData() {
        // Initialize users
        $users = $this->getAll('users');
        if (empty($users)) {
            $admin_pass = $this->encryptData(password_hash('admin@1234', PASSWORD_DEFAULT));
            $user_pass = $this->encryptData(password_hash('user@1234', PASSWORD_DEFAULT));
            $this->insert('users', [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => $admin_pass,
                'name' => 'Administrator',
                'type' => 'admin',
                'profile_pic' => null,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $this->insert('users', [
                'id' => 2,
                'email' => 'user@gmail.com',
                'password' => $user_pass,
                'name' => 'Regular User',
                'type' => 'user',
                'profile_pic' => null,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Initialize categories
        $categories = $this->getAll('categories');
        if (empty($categories)) {
            $this->insert('categories', ['id' => 1, 'name' => 'Fiction', 'description' => 'Fictional books and novels', 'created_at' => date('Y-m-d H:i:s')]);
            $this->insert('categories', ['id' => 2, 'name' => 'Non-Fiction', 'description' => 'Educational and informative books', 'created_at' => date('Y-m-d H:i:s')]);
            $this->insert('categories', ['id' => 3, 'name' => 'Science', 'description' => 'Books related to science and technology', 'created_at' => date('Y-m-d H:i:s')]);
            $this->insert('categories', ['id' => 4, 'name' => 'History', 'description' => 'Historical books and biographies', 'created_at' => date('Y-m-d H:i:s')]);
        }

        // Initialize authors
        $authors = $this->getAll('authors');
        if (empty($authors)) {
            $this->insert('authors', ['id' => 1, 'name' => 'J.K. Rowling', 'bio' => 'British author best known for the Harry Potter series', 'created_at' => date('Y-m-d H:i:s')]);
            $this->insert('authors', ['id' => 2, 'name' => 'George Orwell', 'bio' => 'English novelist and essayist', 'created_at' => date('Y-m-d H:i:s')]);
            $this->insert('authors', ['id' => 3, 'name' => 'Stephen King', 'bio' => 'American author of horror, supernatural fiction', 'created_at' => date('Y-m-d H:i:s')]);
            $this->insert('authors', ['id' => 4, 'name' => 'Agatha Christie', 'bio' => 'English writer known for detective novels', 'created_at' => date('Y-m-d H:i:s')]);
        }

        // Initialize books
        $books = $this->getAll('books');
        if (empty($books)) {
            $this->insert('books', [
                'id' => 1,
                'title' => 'Harry Potter and the Philosopher\'s Stone',
                'author_id' => 1,
                'category_id' => 1,
                'isbn' => '9780747532699',
                'description' => 'The first book in the Harry Potter series',
                'quantity' => 5,
                'available_quantity' => 5,
                'price' => 10.99,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $this->insert('books', [
                'id' => 2,
                'title' => '1984',
                'author_id' => 2,
                'category_id' => 2,
                'isbn' => '9780451524935',
                'description' => 'A dystopian social science fiction novel',
                'quantity' => 3,
                'available_quantity' => 3,
                'price' => 15.50,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $this->insert('books', [
                'id' => 3,
                'title' => 'The Shining',
                'author_id' => 3,
                'category_id' => 1,
                'isbn' => '9780307743657',
                'description' => 'A horror novel about a family in an isolated hotel',
                'quantity' => 2,
                'available_quantity' => 2,
                'price' => 12.00,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $this->insert('books', [
                'id' => 4,
                'title' => 'Murder on the Orient Express',
                'author_id' => 4,
                'category_id' => 1,
                'isbn' => '9780062693662',
                'description' => 'A detective novel featuring Hercule Poirot',
                'quantity' => 4,
                'available_quantity' => 4,
                'price' => 20.00,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Initialize sample reservations and fines for demo
        $reservations = $this->getAll('reservations');
        if (empty($reservations)) {
            $this->insert('reservations', [
                'id' => 1,
                'user_id' => 2,
                'book_id' => 1,
                'reservation_date' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'status' => 'active',
                'expiry_date' => date('Y-m-d H:i:s', strtotime('+6 days'))
            ]);
        }

        $fines = $this->getAll('fines');
        if (empty($fines)) {
            $this->insert('fines', [
                'id' => 1,
                'user_id' => 2,
                'issue_id' => 1,
                'amount' => 2.50,
                'reason' => 'Overdue book return',
                'status' => 'unpaid',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ]);
        }

        $notifications = $this->getAll('notifications');
        if (empty($notifications)) {
            $this->insert('notifications', [
                'id' => 1,
                'user_id' => 2,
                'title' => 'Welcome to Library Management System',
                'message' => 'Thank you for registering! You can now browse and reserve books.',
                'type' => 'info',
                'is_read' => false,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ]);
        }
    }

    public function getAll($table) {
        $file = $this->data_dir . $table . '.json';
        if (file_exists($file)) {
            return json_decode(file_get_contents($file), true);
        }
        return [];
    }

    public function getById($table, $id) {
        $data = $this->getAll($table);
        foreach ($data as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }
        return null;
    }

    public function getWhere($table, $conditions) {
        $data = $this->getAll($table);
        $results = [];
        foreach ($data as $item) {
            $match = true;
            foreach ($conditions as $key => $value) {
                if (!isset($item[$key]) || $item[$key] != $value) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                $results[] = $item;
            }
        }
        return $results;
    }

    public function insert($table, $data) {
        $all = $this->getAll($table);
        if (!isset($data['id'])) {
            $data['id'] = count($all) + 1;
        }
        $all[] = $data;
        file_put_contents($this->data_dir . $table . '.json', json_encode($all, JSON_PRETTY_PRINT));
        return $data['id'];
    }

    public function update($table, $id, $data) {
        $all = $this->getAll($table);
        foreach ($all as &$item) {
            if ($item['id'] == $id) {
                $item = array_merge($item, $data);
                break;
            }
        }
        file_put_contents($this->data_dir . $table . '.json', json_encode($all, JSON_PRETTY_PRINT));
        return true;
    }

    public function delete($table, $id) {
        $all = $this->getAll($table);
        $all = array_filter($all, function($item) use ($id) {
            return $item['id'] != $id;
        });
        file_put_contents($this->data_dir . $table . '.json', json_encode(array_values($all), JSON_PRETTY_PRINT));
        return true;
    }

    public function getStats() {
        $books = $this->getAll('books');
        $users = $this->getAll('users');
        $issued = $this->getAll('issued_books');
        $reservations = $this->getAll('reservations');
        $fines = $this->getAll('fines');

        $total_books = array_sum(array_column($books, 'quantity'));
        $total_users = count(array_filter($users, function($u) { return $u['type'] == 'user'; }));
        $issued_books = count(array_filter($issued, function($i) { return $i['status'] == 'issued'; }));
        $overdue_books = count(array_filter($issued, function($i) {
            return $i['status'] == 'issued' && strtotime($i['due_date']) < time();
        }));
        $active_reservations = count(array_filter($reservations, function($r) { return $r['status'] == 'active'; }));
        $total_fines = array_sum(array_column($fines, 'amount'));

        return [
            'total_books' => $total_books,
            'total_users' => $total_users,
            'issued_books' => $issued_books,
            'overdue_books' => $overdue_books,
            'active_reservations' => $active_reservations,
            'total_fines' => $total_fines
        ];
    }

    public function getAdvancedStats() {
        $books = $this->getAll('books');
        $issued = $this->getAll('issued_books');
        $reservations = $this->getAll('reservations');
        $fines = $this->getAll('fines');
        $users = $this->getAll('users');

        // Monthly book issues (last 12 months)
        $monthly_issues = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $count = count(array_filter($issued, function($item) use ($month) {
                return strpos($item['issue_date'], $month) === 0;
            }));
            $monthly_issues[] = ['month' => $month, 'count' => $count];
        }

        // Popular categories
        $category_stats = [];
        $categories = $this->getAll('categories');
        foreach ($categories as $cat) {
            $cat_books = count(array_filter($books, function($b) use ($cat) { return $b['category_id'] == $cat['id']; }));
            $cat_issued = count(array_filter($issued, function($i) use ($cat, $books) {
                $book = array_filter($books, function($b) use ($i) { return $b['id'] == $i['book_id']; });
                return !empty($book) && reset($book)['category_id'] == $cat['id'];
            }));
            $category_stats[] = [
                'name' => $cat['name'],
                'total_books' => $cat_books,
                'issued_books' => $cat_issued
            ];
        }

        // User activity
        $issued_data = $issued;
        $active_users = count(array_filter($users, function($u) use ($issued_data) {
            $user_issues = count(array_filter($issued_data, function($i) use ($u) { return $i['user_id'] == $u['id']; }));
            return $user_issues > 0;
        }));

        return [
            'monthly_issues' => $monthly_issues,
            'category_stats' => $category_stats,
            'active_users' => $active_users,
            'total_reservations' => count($reservations),
            'total_fines_collected' => array_sum(array_column($fines, 'amount'))
        ];
    }

    public function createReservation($user_id, $book_id, $reservation_date = null) {
        if (!$reservation_date) {
            $reservation_date = date('Y-m-d H:i:s');
        }

        // Check if book is available
        $book = $this->getById('books', $book_id);
        if (!$book || $book['available_quantity'] <= 0) {
            return false;
        }

        // Check if user already has this book issued or reserved
        $existing_issue = $this->getWhere('issued_books', ['user_id' => $user_id, 'book_id' => $book_id, 'status' => 'issued']);
        $existing_reservation = $this->getWhere('reservations', ['user_id' => $user_id, 'book_id' => $book_id, 'status' => 'active']);

        if (!empty($existing_issue) || !empty($existing_reservation)) {
            return false;
        }

        $reservation_id = $this->insert('reservations', [
            'user_id' => $user_id,
            'book_id' => $book_id,
            'reservation_date' => $reservation_date,
            'status' => 'active',
            'expiry_date' => date('Y-m-d H:i:s', strtotime('+7 days')) // 7 days to pick up
        ]);

        // Update available quantity
        $this->update('books', $book_id, ['available_quantity' => $book['available_quantity'] - 1]);

        return $reservation_id;
    }

    public function cancelReservation($reservation_id) {
        $reservation = $this->getById('reservations', $reservation_id);
        if ($reservation && $reservation['status'] == 'active') {
            $this->update('reservations', $reservation_id, ['status' => 'cancelled']);

            // Restore available quantity
            $book = $this->getById('books', $reservation['book_id']);
            if ($book) {
                $this->update('books', $reservation['book_id'], ['available_quantity' => $book['available_quantity'] + 1]);
            }
            return true;
        }
        return false;
    }

    public function issueReservedBook($reservation_id, $issue_date = null) {
        $reservation = $this->getById('reservations', $reservation_id);
        if (!$reservation || $reservation['status'] != 'active') {
            return false;
        }

        if (!$issue_date) {
            $issue_date = date('Y-m-d H:i:s');
        }

        $due_date = date('Y-m-d H:i:s', strtotime('+14 days')); // 14 days loan period

        $issue_id = $this->insert('issued_books', [
            'user_id' => $reservation['user_id'],
            'book_id' => $reservation['book_id'],
            'issue_date' => $issue_date,
            'due_date' => $due_date,
            'status' => 'issued',
            'reservation_id' => $reservation_id
        ]);

        // Mark reservation as completed
        $this->update('reservations', $reservation_id, ['status' => 'completed']);

        return $issue_id;
    }

    public function calculateFine($issue_id) {
        $issue = $this->getById('issued_books', $issue_id);
        if (!$issue || $issue['status'] != 'issued') {
            return 0;
        }

        $due_date = strtotime($issue['due_date']);
        $current_date = time();

        if ($current_date <= $due_date) {
            return 0;
        }

        $days_overdue = ceil(($current_date - $due_date) / (60 * 60 * 24));
        $fine_rate = 0.50; // $0.50 per day
        return $days_overdue * $fine_rate;
    }

    public function createFine($issue_id, $amount, $reason = 'Overdue book') {
        $issue = $this->getById('issued_books', $issue_id);
        if (!$issue) {
            return false;
        }

        // Check if fine already exists for this issue
        $existing_fine = $this->getWhere('fines', ['issue_id' => $issue_id, 'status' => 'unpaid']);
        if (!empty($existing_fine)) {
            return false;
        }

        $fine_id = $this->insert('fines', [
            'user_id' => $issue['user_id'],
            'issue_id' => $issue_id,
            'amount' => $amount,
            'reason' => $reason,
            'status' => 'unpaid',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $fine_id;
    }

    public function payFine($fine_id) {
        $fine = $this->getById('fines', $fine_id);
        if ($fine && $fine['status'] == 'unpaid') {
            $this->update('fines', $fine_id, [
                'status' => 'paid',
                'paid_at' => date('Y-m-d H:i:s')
            ]);
            return true;
        }
        return false;
    }

    public function createNotification($user_id, $title, $message, $type = 'info') {
        return $this->insert('notifications', [
            'user_id' => $user_id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => false,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getUserNotifications($user_id, $unread_only = false) {
        $conditions = ['user_id' => $user_id];
        if ($unread_only) {
            $conditions['is_read'] = false;
        }
        return $this->getWhere('notifications', $conditions);
    }

    public function markNotificationRead($notification_id) {
        return $this->update('notifications', $notification_id, ['is_read' => true]);
    }

    public function checkOverdueBooks() {
        $issued_books = $this->getAll('issued_books');
        $overdue_books = array_filter($issued_books, function($book) {
            return $book['status'] == 'issued' && strtotime($book['due_date']) < time();
        });

        foreach ($overdue_books as $book) {
            // Calculate fine if not already calculated
            $existing_fine = $this->getWhere('fines', ['issue_id' => $book['id'], 'status' => 'unpaid']);
            if (empty($existing_fine)) {
                $fine_amount = $this->calculateFine($book['id']);
                if ($fine_amount > 0) {
                    $this->createFine($book['id'], $fine_amount);

                    // Send notification
                    $this->createNotification(
                        $book['user_id'],
                        'Overdue Book Fine',
                        "You have an overdue book. Fine amount: $" . number_format($fine_amount, 2),
                        'warning'
                    );
                }
            }
        }

        return count($overdue_books);
    }

    public function checkReservationExpiry() {
        $reservations = $this->getAll('reservations');
        $expired_reservations = array_filter($reservations, function($res) {
            return $res['status'] == 'active' && strtotime($res['expiry_date']) < time();
        });

        foreach ($expired_reservations as $res) {
            $this->update('reservations', $res['id'], ['status' => 'expired']);

            // Restore available quantity
            $book = $this->getById('books', $res['book_id']);
            if ($book) {
                $this->update('books', $res['book_id'], ['available_quantity' => $book['available_quantity'] + 1]);
            }

            // Send notification
            $this->createNotification(
                $res['user_id'],
                'Reservation Expired',
                "Your reservation for book ID {$res['book_id']} has expired.",
                'warning'
            );
        }

        return count($expired_reservations);
    }
}

// Global instance
$db = new SimpleDB();
?>