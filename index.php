<?php
require __DIR__ . '/config.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    if ($username === '') {
        $error = 'দয়া করে Gmail ইমেইল দিন।';
    } else {
        try {
            $pdo = dbConnect(true);
            $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            // Email-only login: ensure Gmail address and create user if not exists
            if (filter_var($username, FILTER_VALIDATE_EMAIL) && preg_match('/@gmail\.com$/i', $username)) {
                if ($user) {
                    // existing user, log in
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'fullname' => $user['fullname'],
                    ];
                } else {
                    // create new user
                    $stmtInsert = $pdo->prepare('INSERT INTO users (username, password, fullname, role) VALUES (?, ?, ?, ?)');
                    $defaultPasswordHash = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
                    $fullname = explode('@', $username)[0] . " বাট ফিরস্ত";
                    $stmtInsert->execute([$username, $defaultPasswordHash, $fullname, 'user']);
                    $newId = $pdo->lastInsertId();
                    $_SESSION['user'] = [
                        'id' => $newId,
                        'username' => $username,
                        'fullname' => $fullname,
                    ];
                }
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'দয়া করে বৈধ Gmail ইমেইল প্রদান করুন।';
            }
        } catch (PDOException $e) {
            $error = 'ডাটাবেসে সংযোগ ব্যর্থ: ' . htmlspecialchars($e->getMessage());
        }
    }
}
?><!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
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
            <label>ইমেইল (Gmail)</label>
            <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required />

            <!-- Password field removed for email-only login -->

            <button type="submit">লগইন</button>
        </form>

    </div>
<script src="auto_redirect.js"></script>
</body>
</html>
