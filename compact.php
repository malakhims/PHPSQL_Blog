<?php
include 'config.php';

$current_month = $_GET['month'] ?? date('Y-m');

if (isset($_GET['tag']) && $_GET['tag'] !== '') {

    $stmt = $pdo->prepare("
        SELECT * FROM logs
        WHERE visible = 'y'
          AND FIND_IN_SET(:tag, tags)
        ORDER BY post_date DESC
    ");
    $stmt->execute([':tag' => $_GET['tag']]);
    $posts = $stmt->fetchAll();

} else {

    // STEP ONE IS REQUEST THE MONTH
    $stmt = $pdo->prepare("
        SELECT * FROM logs
        WHERE visible = 'y'
          AND DATE_FORMAT(post_date, '%Y-%m') = :month
        ORDER BY post_date DESC
    ");
    $stmt->execute([':month' => $current_month]);
    $posts = $stmt->fetchAll();

    // 2. Fallback if empty
    if (empty($posts)) {

        $stmt = $pdo->prepare("
            SELECT * FROM logs
            WHERE visible = 'y'
              AND DATE_FORMAT(post_date, '%Y-%m') = (
                  SELECT DATE_FORMAT(MAX(post_date), '%Y-%m')
                  FROM logs
                  WHERE visible = 'y'
              )
            ORDER BY post_date DESC
        ");
        $stmt->execute();
        $posts = $stmt->fetchAll();
    }
}
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
    
	<!---
	⁺໒꒱ིྀ༝   ໒꒱ྀི𓊆ྀི♡𓊇ྀི꒰ঌ໒꒱ 
	this contains the "header" of your blog.
	it's just a DIV so you can like.. add images or text or whatever.
	--->
    <header id="header">
        <h1><a href="/">wow a header againn</a></h1>
        <p class="subtitle">WHAT THE HELL</p>
    </header>
    
    <div id="content-wrapper">
        
        <!---
		⁺໒꒱ིྀ༝   ໒꒱ྀི𓊆ྀི♡𓊇ྀི꒰ঌ໒꒱ 
		this is the sidebar
		--->
		
		<!---THIS CONTAINS A TINY BIT OF STUFF FOR YOU TO PUT
		You can also just delete it if you want I guess it's very Tumblr-->
		
        <aside id="sidebar">
            <div class="profile-card">
                <img src="images/scantyicon.gif" alt="profile" class="profile-img">
                <p>you can put something here</p>
                <p><em>or not</em></p>
                <p><a href="#">maybe a link here idk</a></p>
            </div>

        		
		<!---
		⁺໒꒱ིྀ༝   ໒꒱ྀི𓊆ྀི♡𓊇ྀི꒰ঌ໒꒱ 
		This holds the latest journals
		THE PHP CANNOT BE TOUCHED 
		But everything around it can BE
		-->
        <div class="sidebar-widget">
            <h3>Latest Journals</h3>
			<?php
			$recent_posts = $pdo->query("
				SELECT id, title, post_date 
				FROM logs 
				WHERE visible = 'y'
				ORDER BY post_date DESC 
				LIMIT 5
			")->fetchAll();

			foreach ($recent_posts as $post): 
				$month = date('Y-m', strtotime($post['post_date']));
				$day = date('j', strtotime($post['post_date']));
			?>
				<a href="?month=<?= $month ?>&day=<?= $day ?>#post-<?= $post['id'] ?>">
					<?= htmlspecialchars($post['title']) ?><br>
				</a>
			<?php endforeach; ?>
        </div>

		<!---
		⁺໒꒱ིྀ༝   ໒꒱ྀི𓊆ྀི♡𓊇ྀི꒰ঌ໒꒱ 
		This is the calendar
		This exists because I like this feature FC2
		-->
            <div class="sidebar-widget">
                <h3>Calendar</h3>
                <div class="calendar-box">
                    <ul>
						<?php
						$months = $pdo->query("
							SELECT DISTINCT DATE_FORMAT(post_date, '%Y-%m') AS month 
							FROM logs 
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

			<!---
			⁺໒꒱ིྀ༝   ໒꒱ྀི𓊆ྀི♡𓊇ྀི꒰ঌ໒꒱ 
			This will list every single tag
			and works as a form of navigation
			-->
            <div class="sidebar-widget">
                <h3>Tags</h3>
                <ul>
					<?php
					$tags_raw = $pdo->query("SELECT tags FROM logs WHERE tags != ''")->fetchAll();
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
			
			<!---
			⁺໒꒱ིྀ༝   ໒꒱ྀི𓊆ྀི♡𓊇ྀི꒰ঌ໒꒱ 
			This lists all the months you've updated your blog
			-->

            <div class="sidebar-widget">
                <h3>Monthly Archive</h3>
                <ul>
					<?php
					$months = $pdo->query("
						SELECT DISTINCT DATE_FORMAT(post_date, '%Y-%m') AS month 
						FROM logs 
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
        </aside>
        
		        
		<!---
		sidebar ends
		--->
		
		
		<!---
		⁺໒꒱ིྀ༝   ໒꒱ྀི𓊆ྀི♡𓊇ྀི꒰ঌ໒꒱ 
		This is the "main" section. Your blog. Your logs. Your alogs. 
		You get it. Very important.
		-->

		<div id="main">
			<?php foreach ($posts as $post): ?>
				<table class="entry_table" id="post-<?= $post['id'] ?>">
					<tr>
						<td class="entry_bg">
							<div class="entry_title"><?= htmlspecialchars($post['title']) ?></div>
							<div class="entry_state">Posted on: <?= $post['post_date'] ?></div>
							<div class="entry_text">
								<?php if (!empty($post['tags'])): ?>
									<div class="entry_tags">
										Tags:
										<?php 
										$tags = explode(',', $post['tags']);
										foreach ($tags as $tag): 
											$tag = trim($tag);
										?>
											<a href="?tag=<?= urlencode($tag) ?>"><?= htmlspecialchars($tag) ?></a>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
								<?= nl2br(htmlspecialchars_decode($post['content'])) ?>
							</div>
						</td>
					</tr>
				</table>
			<?php endforeach; ?>
		</div>
		</div>
    
	
	<!---
	⁺໒꒱ིྀ༝   ໒꒱ྀི𓊆ྀི♡𓊇ྀི꒰ঌ໒꒱ 
	This is a footer. I just like them.
	-->

    <footer id="footer">
        <p>footer for blog that will last forever</p>
    </footer>
	
</div>

<!---
⁺໒꒱ིྀ༝   ໒꒱ྀི𓊆ྀི♡𓊇ྀི꒰ঌ໒꒱ 
Handles smooth navigation lol
-->

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
