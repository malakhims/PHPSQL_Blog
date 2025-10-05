<?php
include 'config.php';

// Get the selected month from URL or use current month
$current_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

// 🟢 Add visibility filter
$visible_filter = "visible = 'y'";

// Posts for tag or month (only visible)
if (isset($_GET['tag'])) {
    $tag = $_GET['tag'];
    $posts = $pdo->prepare("
        SELECT * FROM logs 
        WHERE FIND_IN_SET(:tag, tags) AND $visible_filter
        ORDER BY post_date DESC
    ");
    $posts->execute([':tag' => $tag]);
} else {
    $posts = $pdo->prepare("
        SELECT * FROM logs 
        WHERE DATE_FORMAT(post_date, '%Y-%m') = :month AND $visible_filter
        ORDER BY post_date DESC
    ");
    $posts->execute([':month' => $current_month]);
}
$posts = $posts->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>blog title</title>
    <meta name="description" content="my bloggies">
    <link rel="stylesheet" href="journal.css">
    <link rel="script" href="calendar.js">
</head>
<body>
<div id="layout">
    
    <!-- Header -->
    <header id="header">
        <h1><a href="/">wow a header againn</a></h1>
        <p class="subtitle">WHAT THE HELL</p>
    </header>
    
    <div id="content-wrapper">
        
        <!-- Sidebar LEFT -->
        <aside id="sidebar">
            <div class="profile-card">
                <img src="images/scantyicon.gif" alt="profile" class="profile-img">
                <p>you can put something here</p>
                <p><em>or not</em></p>
                <p><a href="#">maybe a link here idk</a></p>
            </div>

            <div class="sidebar-widget">
                <h3>Latest Journals</h3>
                <?php 
                // 🟢 Only visible posts
                $recent_posts_stmt = $pdo->query("
                    SELECT id, title, post_date 
                    FROM logs 
                    WHERE $visible_filter
                    ORDER BY post_date DESC 
                    LIMIT 5
                ");
                $recent_posts = $recent_posts_stmt->fetchAll();

                foreach ($recent_posts as $post): 
                    $month = date('Y-m', strtotime($post['post_date']));
                    $day = date('j', strtotime($post['post_date']));
                ?>
                    <a href="?month=<?= $month ?>&day=<?= $day ?>#post-<?= $post['id'] ?>">
                        <?= htmlspecialchars($post['title']) ?><br>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="sidebar-widget">
                <h3>Calendar</h3>
                <div class="calendar-box">
                    <ul>
                    <?php
                    // 🟢 Only months with visible posts
                    $months = $pdo->query("
                        SELECT DISTINCT DATE_FORMAT(post_date, '%Y-%m') AS month 
                        FROM logs 
                        WHERE $visible_filter
                        ORDER BY month DESC
                    ")->fetchAll();
                    
                    foreach ($months as $m): ?>
                        <li>
                            <a href="?month=<?= $m['month'] ?>">
                                <?= date('F Y', strtotime($m['month'] . '-01')) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="sidebar-widget">
                <h3>Tags</h3>
                <ul>
                    <?php
                    // 🟢 Only visible posts count for tags
                    $tags_raw = $pdo->query("SELECT tags FROM logs WHERE tags != '' AND $visible_filter")->fetchAll();
                    $all_tags = [];

                    foreach ($tags_raw as $row) {
                        $tags = explode(',', $row['tags']);
                        foreach ($tags as $tag) {
                            $clean_tag = trim($tag);
                            if (!empty($clean_tag)) {
                                $all_tags[$clean_tag] = true;
                            }
                        }
                    }

                    foreach (array_keys($all_tags) as $tag): ?>
                        <li><a href="?tag=<?= urlencode($tag) ?>"><?= htmlspecialchars($tag) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="sidebar-widget">
                <h3>Monthly Archive</h3>
                <ul>
                    <?php foreach ($months as $m): ?>
                        <li>
                            <a href="?month=<?= $m['month'] ?>">
                                <?= date('F Y', strtotime($m['month'] . '-01')) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
        
        <!-- Main posts RIGHT -->
        <main id="main">
            <?php foreach ($posts as $post): ?>
                <article class="entry" id="post-<?= $post['id'] ?>">
                    <h2 class="entry-title"><?= htmlspecialchars($post['title']) ?></h2>
                    <div class="entry-meta">Posted on <?= $post['post_date'] ?></div>
                    <div class="entry-content">
                        <?= nl2br(htmlspecialchars_decode($post['content'])) ?>
                    </div>
                    <?php if (!empty($post['tags'])): ?>
                        <div class="entry-tags">
                            Tags:
                            <?php foreach (explode(',', $post['tags']) as $tag): ?>
                                <a href="?tag=<?= urlencode(trim($tag)) ?>">#<?= htmlspecialchars(trim($tag)) ?></a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="entry-divider"></div>
                </article>
            <?php endforeach; ?>
        </main>
    </div>
    
    <!-- Footer -->
    <footer id="footer">
        <p>footer for blog that will last forever</p>
    </footer>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_GET['day'])): ?>
        setTimeout(function() {
            const anchor = window.location.hash;
            if (anchor) {
                const element = document.querySelector(anchor);
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth' });
                    element.classList.add('highlight-entry');
                    setTimeout(() => {
                        element.classList.remove('highlight-entry');
                    }, 2000);
                }
            }
        }, 100);
    <?php endif; ?>
});
</script>

</body>
</html>
