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
    body {
        font-family: system-ui, sans-serif;
        background: #121212;
        color: #eee;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }
    .auto-container {
        max-width: 960px;
        width: 100%;
        padding: 1rem;
        text-align: center;
    }
    .msg {
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }
    #countdown {
        margin: 0.5rem 0 1rem;
        font-weight: 600;
        font-size: 1rem;
    }
    #viewer {
        width: 100%;
        height: 70vh;
        border: none;
        border-radius: 12px;
        background: #1e293b;
    }
    a {color:#38bdf8;}
    @media (max-width: 600px) {
        #viewer {height: 55vh;}
        .msg {font-size: 1rem;}
    }
</style>
</head>
<body>
    <?php include 'nav.php'; ?><div class="auto-container">
    <div class="msg">
        <p>সাইটগুলোতে স্বয়ংক্রিয়ভাবে ভিজিট করা হচ্ছে…</p>
        <p>যদি রিফ্রেস করতে চান <a href="dashboard.php">ড্যাশবোর্ড</a> এ ফিরে যান।</p>
    </div>
    <div id="countdown"></div>
    <iframe id="viewer"></iframe>
</div>
<script>
        const sites = <?php echo json_encode($sites, JSON_UNESCAPED_SLASHES); ?>;
        const siteList = sites.map(s => ({id: s.id, url: s.url}));
        let idx = 0;
        const iframe = document.getElementById('viewer');
        const countdownEl = document.getElementById('countdown');
        function updateCountdown(seconds) {
            countdownEl.textContent = `পরের সাইটে যাওয়ার বাকি সময়: ${seconds}s`;
        }
        function visitNext(){
            if(idx >= siteList.length){
                window.location.href = 'dashboard.php';
                return;
            }
            const site = siteList[idx];
            fetch(`https://techandclick.site/click/click.php?site_id=${site.id}`, {credentials:'include'})
                .catch(e=>console.error('Click record error',e));
            iframe.src = site.url;
            let remaining = 10;
            updateCountdown(remaining);
            const timer = setInterval(() => {
                remaining--;
                if (remaining <= 0) {
                    clearInterval(timer);
                    idx++;
                    visitNext();
                } else {
                    updateCountdown(remaining);
                }
            }, 1000);
        }
        // Start when page loads
        visitNext();
    </script>
    <script src="nav.js"></script>
</body>
</html>
