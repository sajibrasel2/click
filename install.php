<?php
require __DIR__ . '/config.php';

try {
    $pdo = dbConnect(true);

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        fullname VARCHAR(150) NOT NULL,
        role VARCHAR(50) NOT NULL DEFAULT 'user'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS sites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        url VARCHAR(255) NOT NULL,
        ad_url VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS clicks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        site_id INT NOT NULL,
        user_id INT NULL,
        clicked_at DATETIME NOT NULL,
        ip_address VARCHAR(45) NULL,
        FOREIGN KEY (site_id) REFERENCES sites(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $existingUsers = $pdo->query('SELECT COUNT(*) as total FROM users')->fetchColumn();
    if ($existingUsers == 0) {
        $password = password_hash('pass1234', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password, fullname, role) VALUES (?, ?, ?, ?)');
        $stmt->execute(['user1', $password, 'Site 1 Manager', 'user']);
        $stmt->execute(['user2', $password, 'Site 2 Manager', 'user']);
        $stmt->execute(['user3', $password, 'Site 3 Manager', 'user']);
        $stmt->execute(['user4', $password, 'Site 4 Manager', 'user']);
        $stmt->execute(['raselsajib25@gmail.com', password_hash('12345Sajibs6@', PASSWORD_DEFAULT), 'Rasel Sajib', 'user']);
        $stmt->execute(['jishuchowdhury78@gmail.com', password_hash('jishuchowdhury59@#', PASSWORD_DEFAULT), 'Jishu Chowdhury', 'user']);
        $stmt->execute(['nusratjahanhabiba1212@gmail.com', password_hash('MUNNA12@#', PASSWORD_DEFAULT), 'Nusrat Jahan Habiba', 'user']);
    }

    $existingSites = $pdo->query('SELECT COUNT(*) as total FROM sites')->fetchColumn();
    if ($existingSites == 0) {
        $stmt = $pdo->prepare('INSERT INTO sites (name, url, ad_url) VALUES (?, ?, ?)');
        $stmt->execute(['সাইট ১', 'https://techandclick.site/movies.php', 'https://techandclick.site/movies.php']);
        $stmt->execute(['সাইট ২', 'https://rewardtojishu.blogspot.com/2026/05/blog-post.html', 'https://rewardtojishu.blogspot.com/2026/05/blog-post.html']);
        $stmt->execute(['সাইট ৩', 'https://rewardtomunna.blogspot.com/2026/05/reward-center.html', 'https://rewardtomunna.blogspot.com/2026/05/reward-center.html']);
        $stmt->execute(['সাইট ৪', 'https://example.com/site4', 'https://example.com/site4']);
        $stmt->execute(['সাইট ৫', 'https://example.com/site5', 'https://example.com/site5']);
    }

    $updateSite2 = $pdo->prepare('UPDATE sites SET url = ?, ad_url = ? WHERE id = ?');
    $updateSite2->execute(['https://rewardtojishu.blogspot.com/2026/05/blog-post.html', 'https://rewardtojishu.blogspot.com/2026/05/blog-post.html', 2]);

    echo "ইনস্টলেশন সফল হয়েছে। এখন আপনি index.php এ যান এবং ব্যবহারকারী লগইন করে দেখুন।";
} catch (PDOException $e) {
    echo "ইনস্টলেশন ব্যর্থ হয়েছে: " . htmlspecialchars($e->getMessage());
}
