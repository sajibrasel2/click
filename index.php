<?php
require __DIR__ . '/config.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'দয়া করে ব্যবহারকারীর নাম ও পাসওয়ার্ড দিন।';
    } else {
        try {
            $pdo = dbConnect(true);
            $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'fullname' => $user['fullname'],
                ];
                header('Location: dashboard.php');
                exit;
            }

            $error = 'আপনার লগইন তথ্য সঠিক নয়।';
        } catch (PDOException $e) {
            $error = 'ডাটাবেসে সংযোগ ব্যর্থ: ' . htmlspecialchars($e->getMessage());
        }
    }
}
?><!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8" />
    <title>লগইন | সাইট ক্লিক ট্র্যাকার</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <div class="container">
        <h1>সাইট ক্লিক ট্র্যাকার</h1>
        <p>৫টি সাইটের বিজ্ঞাপন ক্লিক গণনা করার জন্য লগইন করুন।</p>

        <?php if ($error): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="index.php">
            <label>ব্যবহারকারীর নাম</label>
            <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required />

            <label>পাসওয়ার্ড</label>
            <input type="password" name="password" required />

            <button type="submit">লগইন</button>
        </form>

    </div>
<script src="auto_redirect.js"></script>
</body>
</html>
