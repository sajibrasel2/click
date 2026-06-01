<?php
require __DIR__ . '/config.php';
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
$pdo = dbConnect(true);
// Get list of sites
$sites = $pdo->query('SELECT id, name, url FROM sites')->fetchAll(PDO::FETCH_ASSOC);
// Clicks per site
$clicksBySite = $pdo->query('SELECT s.name, COUNT(c.id) AS cnt FROM clicks c JOIN sites s ON c.site_id = s.id GROUP BY s.id')->fetchAll(PDO::FETCH_KEY_PAIR);
// Clicks per user
$clicksByUser = $pdo->query('SELECT u.username, COUNT(c.id) AS cnt FROM clicks c JOIN users u ON c.user_id = u.id GROUP BY u.id')->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>ড্যাশবোর্ড – ক্লিক পরিসংখ্যান</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#f5f5f5;padding:20px;}
        table{border-collapse:collapse;width:100%;margin-top:20px;}
        th,td{border:1px solid #ddd;padding:8px;text-align:left;}
        th{background:#2c3e50;color:#fff;}
    </style>
</head>
<body>
    <h1>স্বাগতম, <?=htmlspecialchars($_SESSION['user']['fullname'])?></h1>
    <h2>সাইটের অনুযায়ী ক্লিক সংখ্যা</h2>
    <table>
        <tr><th>সাইট</th><th>ক্লিক সংখ্যা</th></tr>
        <?php foreach ($sites as $s): ?>
            <tr>
                <td><?=htmlspecialchars($s['name'])?></td>
                <td><?= $clicksBySite[$s['name']] ?? 0 ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <h2>ব্যবহারকারীর অনুযায়ী ক্লিক সংখ্যা</h2>
    <table>
        <tr><th>ইউজার</th><th>ক্লিক সংখ্যা</th></tr>
        <?php foreach ($clicksByUser as $user => $cnt): ?>
            <tr><td><?=htmlspecialchars($user)?></td><td><?= $cnt ?></td></tr>
        <?php endforeach; ?>
    </table>
    <script src="auto_redirect.js"></script>
</body>
</html>
