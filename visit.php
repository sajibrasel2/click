<?php
require __DIR__ . '/config.php';
require_login();

$siteId = filter_input(INPUT_GET, 'site_id', FILTER_VALIDATE_INT);
if (!$siteId) {
    header('Location: dashboard.php');
    exit;
}

try {
    $pdo = dbConnect(true);
    $stmt = $pdo->prepare('SELECT id, ad_url FROM sites WHERE id = ? LIMIT 1');
    $stmt->execute([$siteId]);
    $site = $stmt->fetch();

    if (!$site) {
        header('Location: dashboard.php');
        exit;
    }

    // Safely handle missing or deleted user
    $userId = current_user()['id'] ?? null;
    $stmt = $pdo->prepare('INSERT INTO clicks (site_id, user_id, clicked_at, ip_address) VALUES (?, ?, NOW(), ?)');
    $stmt->execute([$siteId, $userId, $_SERVER['REMOTE_ADDR'] ?? null]);

    header('Location: ' . $site['ad_url']);
    exit;
} catch (PDOException $e) {
    die('সার্ভার ত্রুটি: ' . htmlspecialchars($e->getMessage()));
}
