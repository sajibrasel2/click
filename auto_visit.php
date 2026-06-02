<?php
require __DIR__ . '/config.php';
require_login();
// Fetch site list (first 3 sites) from DB
$pdo = dbConnect(true);
$stmt = $pdo->prepare('SELECT id, url FROM sites WHERE id IN (1,2,3) ORDER BY id ASC');
$stmt->execute();
$sites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অটো ভিজিট – ক্লিক ট্র্যাকার</title>
    <style>
        body {font-family: system-ui, sans-serif; background:#121212; color:#eee; display:flex; align-items:center; justify-content:center; height:100vh; margin:0;}
        .msg {text-align:center;}
        a {color:#38bdf8;}
    </style>
</head>
<body>
    <div class="msg">
        <p>সাইটগুলোতে স্বয়ংক্রিয়ভাবে ভিজিট করা হচ্ছে…</p>
        <p>যদি রিফ্রেশ করতে চান <a href="dashboard.php">ড্যাশবোর্ড</a> এ ফিরে যান।</p>
    </div>
    <script>
        const sites = <?php echo json_encode($sites, JSON_UNESCAPED_SLASHES); ?>;
        // Convert to array of {id, url}
        const siteList = sites.map(s => ({id: s.id, url: s.url}));
        let idx = 0;
        function visitNext() {
            if (idx >= siteList.length) {
                // All done – return to dashboard
                window.location.href = 'dashboard.php';
                return;
            }
            const site = siteList[idx];
            // Record click first
            fetch(`https://techandclick.site/click/click.php?site_id=${site.id}`, {
                credentials: 'include'
            }).catch(e => console.error('Click record error', e));
            // After short delay navigate
            setTimeout(() => {
                window.location.href = site.url;
            }, 2000); // 2 seconds before leaving current page
        }
        // Start the chain when this page loads
        visitNext();
    </script>
</body>
</html>
