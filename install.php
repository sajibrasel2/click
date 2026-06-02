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

    // অপ্রয়োজনীয় ডিফল্ট ইউজার ১-৪ ডাটাবেস থেকে মুছে ফেলা
    $pdo->exec("DELETE FROM users WHERE username IN ('user1', 'user2', 'user3', 'user4')");

    // আপনার দেওয়া নির্দিষ্ট ৩ জন ব্যবহারকারী ডাটাবেসে না থাকলে তা নিশ্চিতভাবে ইনসার্ট করবে
    $usersToInsert = [
        ['raselsajib25@gmail.com', '12345Sajibs6@', 'Rasel Sajib', 'user'],
        ['jishuchowdhury78@gmail.com', '12345@', 'Jishu Chowdhury', 'user'],
        ['nusratjahanhabiba1212@gmail.com', '12345@', 'Nusrat Jahan Habiba', 'user'],
    ];
    $stmtCheck = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
    $stmtInsert = $pdo->prepare('INSERT INTO users (username, password, fullname, role) VALUES (?, ?, ?, ?)');
    foreach ($usersToInsert as $u) {
        $stmtCheck->execute([$u[0]]);
        if ($stmtCheck->fetchColumn() == 0) {
            $stmtInsert->execute([$u[0], password_hash($u[1], PASSWORD_DEFAULT), $u[2], $u[3]]);
        }
    }

    $existingSites = $pdo->query('SELECT COUNT(*) as total FROM sites')->fetchColumn();
    if ($existingSites == 0) {
        $stmt = $pdo->prepare('INSERT INTO sites (name, url, ad_url) VALUES (?, ?, ?)');
        $stmt->execute(['সাইট ১', 'https://techandclick.site/movies.php', 'https://techandclick.site/movies.php']);
        $stmt->execute(['সাইট ২', 'https://rewardtojishu.blogspot.com/2026/05/blog-post.html?m=1', 'https://rewardtojishu.blogspot.com/2026/05/blog-post.html?m=1']);
        $stmt->execute(['সাইট ৩', 'https://rewardtomunna.blogspot.com/2026/05/reward-center.html', 'https://rewardtomunna.blogspot.com/2026/05/reward-center.html']);
        $stmt->execute(['সাইট ৪', 'https://example.com/site4', 'https://example.com/site4']);
        $stmt->execute(['সাইট ৫', 'https://example.com/site5', 'https://example.com/site5']);
    }

    // Ensure Site 1 points to correct movies page
    $updateSite1 = $pdo->prepare('UPDATE sites SET url = ?, ad_url = ? WHERE id = ?');
    $updateSite1->execute(['https://techandclick.site/movies.php', 'https://techandclick.site/movies.php', 1]);
    // Keep existing update for Site 2
    $updateSite2 = $pdo->prepare('UPDATE sites SET url = ?, ad_url = ? WHERE id = ?');
    $updateSite2->execute(['https://rewardtojishu.blogspot.com/2026/05/blog-post.html', 'https://rewardjojishu.blogspot.com/2026/05/blog-post.html', 2]);

    echo "ইনস্টলেশন সফল হয়েছে। এখন আপনি index.php এ যান এবং ব্যবহারকারী লগইন করে দেখুন।";
} catch (PDOException $e) {
    echo "ইনস্টলেশন ব্যর্থ হয়েছে: " . htmlspecialchars($e->getMessage());
}
