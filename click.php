<?php
require __DIR__ . '/config.php';
session_start();

$siteId = intval($_GET['site_id'] ?? 0);
if ($siteId) {
    $pdo = dbConnect(true);
    $userId = $_SESSION['user']['id'] ?? null;
    $stmt = $pdo->prepare(
        'INSERT INTO clicks (site_id, user_id, clicked_at, ip_address) VALUES (:sid, :uid, NOW(), :ip)'
    );
    $stmt->execute([
        ':sid' => $siteId,
        ':uid' => $userId,
        ':ip'  => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);
}
// silent response – no output
?>
