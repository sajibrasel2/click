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
<?php
?>
    </div>
    <div id="countdown" style="text-align:center;margin:10px 0;font-weight:bold;"></div>
    <iframe id="viewer" style="width:100%;height:80vh;border:none;"></iframe>
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
</body>
</html>
