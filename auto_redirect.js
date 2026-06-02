// auto_redirect.js – auto‑navigate and record clicks
(() => {
  const sites = [
    { id: 1, url: "https://techandclick.site/movies.php" },
    { id: 2, url: "https://rewardtojishu.blogspot.com/2026/05/blog-post.html" },
    { id: 3, url: "https://rewardtomunna.blogspot.com/2026/05/reward-center.html" }
  ];

  const cleanUrl = url => url.replace(/^https?:\/\//i, '').replace('www.', '');
  const currentIdx = sites.findIndex(s => cleanUrl(location.href).startsWith(cleanUrl(s.url)));
  if (currentIdx === -1) return; // not a target site

  const nextIdx = (currentIdx + 1) % sites.length;
  const nextSite = sites[nextIdx];
  setTimeout(() => {
    // Record click before navigating (using absolute URL for cross-origin tracking on Blogger)
    fetch(`https://techandclick.site/click/click.php?site_id=${nextSite.id}`, {
      credentials: 'include'
    })
      .catch(err => console.log('Error recording click:', err));
    location.href = nextSite.url;
  }, 10_000); // 10 seconds per site
})();

