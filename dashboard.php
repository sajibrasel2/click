<?php
require __DIR__ . '/config.php';

// Verify session is active, if not redirect to index.php
if (!is_logged_in()) {
    header('Location: index.php');
    exit;
}

$pdo = dbConnect(true);

// 1. Get list of sites (limit to first 3 sites as requested: 1-3)
$sites = $pdo->query('SELECT id, name, url FROM sites WHERE id IN (1, 2, 3) ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);

// 2. Get clicks per site by the currently logged-in user
$stmtUser = $pdo->prepare('
    SELECT site_id, COUNT(id) AS cnt 
    FROM clicks 
    WHERE user_id = ? 
    GROUP BY site_id
');
$stmtUser->execute([$_SESSION['user']['id']]);
$userClicksBySite = $stmtUser->fetchAll(PDO::FETCH_KEY_PAIR);

// 3. Get total clicks per site by all users
$totalClicksBySite = $pdo->query('
    SELECT site_id, COUNT(id) AS cnt 
    FROM clicks 
    GROUP BY site_id
')->fetchAll(PDO::FETCH_KEY_PAIR);

// 4. Get total clicks per user
$clicksByUser = $pdo->query('
    SELECT u.fullname, COUNT(c.id) AS cnt 
    FROM users u 
    LEFT JOIN clicks c ON u.id = c.user_id 
    GROUP BY u.id
    ORDER BY cnt DESC
')->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ড্যাসবোর্ড – ক্লিক ট্র্যাকার</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .table-container {
            margin-top: 32px;
            overflow-x: auto;
            border-radius: 20px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(15, 23, 42, 0.6);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        th, td {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        }
        th {
            background: rgba(15, 23, 42, 0.85);
            color: #ffffff;
            font-weight: 700;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .section-title {
            margin: 40px 0 20px;
            font-size: 1.6rem;
            color: #ffffff;
            border-left: 4px solid #38bdf8;
            padding-left: 12px;
        }
        .site-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .site-card .button {
            margin-top: 16px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <h1>ড্যাসবোর্ড</h1>
                <p>স্বাগতম, <strong><?= htmlspecialchars($_SESSION['user']['fullname']) ?></strong> (<?= htmlspecialchars($_SESSION['user']['username']) ?>)</p>
            </div>
            <div class="header-actions">
                <a href="auto_visit.php" class="button">অটো ভিজিট শুরু করুন (১-৩)</a>
                <a href="logout.php" class="button-secondary">লগআউট</a>
            </div>
        </header>

        <section class="summary">
            <h2>পরিসংখ্যান সারাংশ</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <span>আপনার মোট ক্লিক</span>
                    <strong><?= array_sum($userClicksBySite) ?></strong>
                </div>
                <div class="stat-card">
                    <span>সকলের মোট ক্লিক</span>
                    <strong><?= array_sum($totalClicksBySite) ?></strong>
                </div>
            </div>
        </section>

        <h2 class="section-title">সাইটের তালিকা এবং ট্র্যাকিং (১-৩)</h2>
        <div class="site-grid">
            <?php foreach ($sites as $s): ?>
                <div class="site-card">
                    <div>
                        <span class="card-badge">সাইট আইডি: <?= $s['id'] ?></span>
                        <h3><?= htmlspecialchars($s['name']) ?></h3>
                        <p style="margin-top: 12px; font-size: 0.9rem; word-break: break-all;">
                            ইউআরএল: <br>
                            <a href="<?= htmlspecialchars($s['url']) ?>" target="_blank" class="site-link"><?= htmlspecialchars($s['url']) ?></a>
                        </p>
                        <p style="margin-top: 8px;">আপনার ক্লিক: <strong><?= $userClicksBySite[$s['id']] ?? 0 ?></strong></p>
                        <p>মোট ক্লিক: <strong><?= $totalClicksBySite[$s['id']] ?? 0 ?></strong></p>
                    </div>
                    <a href="visit.php?site_id=<?= $s['id'] ?>" class="button">ভিজিট করুন</a>
                </div>
            <?php endforeach; ?>
        </div>

        <h2 class="section-title">ইউজার ভিত্তিক মোট ক্লিক সংখ্যা</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ইউজার (নাম)</th>
                        <th>মোট ক্লিক সংখ্যা</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clicksByUser as $name => $cnt): ?>
                        <tr>
                            <td><?= htmlspecialchars($name) ?></td>
                            <td><strong><?= $cnt ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
