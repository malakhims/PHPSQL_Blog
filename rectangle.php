<?php

//˚₊‧꒰ა ☆ ໒꒱ ‧₊˚ GENERALLY YOU SHOULD NEVER ALTER THIS SECTION 
// though that's not completely true
// like you may end up moving the config file eventually
// but most of of this section shouldn't be touched
// "DO NOT TOUCH THIS" is more of a recommendation

// this contains a lot of info to access your database and login
include 'config.php';

// CURRENT MONTH | DO NOT TOUCH THIS
$current_month = $_GET['month'] ?? date('Y-m');


// TAG PREPARATION FOR TAG FILTERING | DO NOT TOUCH THIS
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

    // requesting the month
    $stmt = $pdo->prepare("
        SELECT * FROM logs
        WHERE visible = 'y'
          AND DATE_FORMAT(post_date, '%Y-%m') = :month
        ORDER BY post_date DESC
    ");
    $stmt->execute([':month' => $current_month]);
    $posts = $stmt->fetchAll();

    // fallback if empty
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


<!-- 

⠀⠀⣀⣀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣀⢀⡀⠀
⣴⠛⠉⠉⠱⢦⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⡴⠞⠉⠉⠙⣦
⣧⠀⠀⠀⠀⠀⠙⢦⣄⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣠⡴⠋⠀⠀⠀⠀⠀⣼
⠹⣄⠀⠀⠀⠀⠀⠀⠈⠙⠲⠦⣄⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣠⡴⠖⠋⠀⠀⠀⠀⠀⠀⠀⣠⠏
⠀⠙⢶⣄⡀⠀⠀⠀⠀⠀⠀⠀⠈⠙⢦⡀⠀⠀⠀⠀⠀⢀⡴⠋⠁⠀⠀⠀⠀⠀⠀⠀⣀⣠⡾⠋⠀
⠀⠀⡼⠋⠉⠀⠀⠀⠀⠀⠀⠀⢀⡀⠀⢹⡄⠀⠀⠀⢠⡟⠀⢀⡀⠀⠀⠀⠀⠀⠀⠀⠉⠙⢧⠀⠀
⠀⠈⢧⡀⠀⠀⠀⠀⠀⢀⠀⣴⠋⡉⢳⡄⣷⠀⠀⠀⣾⢠⡞⠉⠙⣦⠀⠀⢀⠀⠀⠀⠀⢀⡼⠀⠀
⠀⠀⠈⠙⠒⢲⡟⠀⠀⠀⠀⢻⣄⠙⠛⣱⠇⠀⠀⠀⠸⣎⠛⠋⣠⡟⠀⠀⠈⠀⢻⡗⠒⠋⠁⠀⠀
⠀⠀⠀⠀⠀⠈⠷⣄⣀⣀⣀⣤⠟⠛⠛⠁⠀⠀⠀⠀⠀⠈⠛⠛⠻⣤⣀⣀⣀⣤⠾⠁⠀⠀⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⠈⠁⠉⠈⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠁⠉⠈⠀⠀⠀⠀⠀⠀⠀⠀

this section should be more familiar to you because
it is a combination of php and html/css
DO NOT BE AFRAID TO ALTER IT AS MUCH, JUST MAKE BACKUPS
use CONTROL F TO find things like header and footer
---->

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>blog title</title>
    <meta name="description" content="my bloggies">
    <link rel="stylesheet" href="rectangle.css">
    <link rel="script" href="calendar.js">
</head>
<body>

<div id="layout">
	<!---
	⁺໒꒱ིྀ༝   ໒꒱ྀི𓊆ྀི♡𓊇ྀི꒰ঌ໒꒱ 
	this contains the "header" of your blog.
	it's just a DIV so you can like.. add images or text or whatever.
	--->
    <div id="header">
        <div class="site_title"><a href="/">omg a title</a></div>
    </div>


	<!---
	⁺໒꒱ིྀ༝   ໒꒱ྀི𓊆ྀི♡𓊇ྀི꒰ঌ໒꒱ 
	This is the "main" section. Your blog. Your logs. Your alogs. 
	You get it. Very important.
	-->

	<div id="content-wrapper">   
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
		
	<!---
	⁺໒꒱ིྀ༝   ໒꒱ྀི𓊆ྀི♡𓊇ྀི꒰ঌ໒꒱ 
	this is the sidebar
	--->
	
	<!---THIS CONTAINS A TINY BIT OF STUFF FOR YOU TO PUT
	You can also just delete it if you want I guess it's very Tumblr-->
	
    <div id="sidebar">
        <!-- Profile Section -->
        <div class="sidebar-widget">
            <div class="profile-icon">
                <img src="images/scantyicon.gif" height="148px">
                <span>put something here if you want<br>or remove i guess</span>
            </div>
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
            <div id="calendar">
                    <?= date('F Y', strtotime($current_month . '-01')) ?>
                    <?php
                    $date = new DateTime($current_month . '-01');
                    $days_in_month = $date->format('t');
                    $first_day = $date->format('w');

                    $post_dates = [];
                    foreach ($posts as $post) {
                        $post_day = date('j', strtotime($post['post_date']));
                        $post_dates[$post_day] = 'post-' . $post['id'];
                    }

                    $selected_day = isset($_GET['day']) ? (int)$_GET['day'] : null;
                    $current_day = (date('Y-m') == $current_month) ? date('j') : null;

                    echo '<table class="mini-calendar">';
                    echo '<tr><th>Su</th><th>Mo</th><th>Tu</th><th>We</th><th>Th</th><th>Fr</th><th>Sa</th></tr><tr>';

                    for ($i = 0; $i < $first_day; $i++) echo '<td></td>';

                    for ($day = 1; $day <= $days_in_month; $day++) {
                        if (($day + $first_day - 1) % 7 == 0 && $day != 1) echo '</tr><tr>';
                        $classes = [];
                        if ($day == $current_day) $classes[] = 'current-day';
                        if ($day == $selected_day) $classes[] = 'selected-day';
                        if (isset($post_dates[$day])) $classes[] = 'has-post';

                        echo '<td class="' . implode(' ', $classes) . '">';
                        if (isset($post_dates[$day])) {
                            echo '<a href="?month=' . $current_month . '&day=' . $day . '#' . $post_dates[$day] . '">' . $day . '</a>';
                        } else {
                            $closest_post = null;
                            $min_diff = PHP_INT_MAX;
                            foreach ($post_dates as $post_day => $anchor) {
                                $diff = abs($post_day - $day);
                                if ($diff < $min_diff) {
                                    $min_diff = $diff;
                                    $closest_post = $anchor;
                                }
                            }
                            if ($closest_post) {
                                echo '<a href="?month=' . $current_month . '&day=' . $day . '#' . $closest_post . '">' . $day . '</a>';
                            } else {
                                echo $day;
                            }
                        }
                        echo '</td>';
                    }

                    echo '</tr></table>';
                    ?>
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
    </div> <!-- sidebar end -->
</div> <!-- content-wrapper end -->

	
<!---
⁺໒꒱ིྀ༝   ໒꒱ྀི𓊆ྀི♡𓊇ྀི꒰ঌ໒꒱ 
This is a footer. I just like them.
-->

<div id="footer">footer for cute girls</div>
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
                    setTimeout(() => element.classList.remove('highlight-entry'), 2000);
                }
            }
        }, 100);
    <?php endif; ?>
});
</script>

</body>
</html>
