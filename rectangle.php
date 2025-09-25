<?php
include 'config.php';

// Get the selected month from URL or use current month
$current_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

if (isset($_GET['tag'])) {
    $tag = $_GET['tag'];
    $posts = $pdo->prepare("
        SELECT * FROM logs 
        WHERE FIND_IN_SET(:tag, tags) 
        ORDER BY post_date DESC
    ");
    $posts->execute([':tag' => $tag]);
} else {
    $posts = $pdo->prepare("
        SELECT * FROM logs 
        WHERE DATE_FORMAT(post_date, '%Y-%m') = :month 
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
    <link rel="stylesheet" href="rectangle.css">
</head>
<body>
    

    <div id="layout">
        <div id="header">
            <div class="site_title"><a href="/">omg a title</a></div>
        </div>

<div id="content-wrapper">   
         <div id="main">
          <?php foreach ($posts as $post): ?>
             <table class="entry_table" id="post-<?= $post['id'] ?>">
                <tr>
                    <td class="entry_bg">
                        <div class="entry_title"><?= htmlspecialchars($post['title']) ?></div>
                        <div class="entry_state">
                            Posted on: <?= $post['post_date'] ?>
                        </div>
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
        
        
          <!-- NEW SIDEBAR -->
            <div id="sidebar">
                <!-- Profile Section -->
                <div class="sidebar-widget">
                    <div class="profile-icon">
                        <img src="images/scantyicon.gif" height="148px">
                        <span>put something here if you want
                        <br>
                        or remove i guess
                        </span>
                    </div>
                </div>

                <!-- Latest Journals -->
                 
                <div class="sidebar-widget">
                <h3>Latest Journals</h3>
                <?php 
                // Get 5 most recent posts overall (not just for current month)
                $recent_posts_stmt = $pdo->query("
                    SELECT id, title, post_date 
                    FROM logs 
                    ORDER BY post_date DESC 
                    LIMIT 5
                ");
                $recent_posts = $recent_posts_stmt->fetchAll();

                foreach ($recent_posts as $post): 
                    $month = date('Y-m', strtotime($post['post_date']));
                    $day = date('j', strtotime($post['post_date']));
                    ?>
                        <a href="?month=<?= $month ?>&day=<?= $day ?>#post-<?= $post['id'] ?>">
                            <?= htmlspecialchars($post['title']) ?>                    <br>
                        </a>

                <?php endforeach; ?>
                </div>



                <!-- Calendar -->
               <div class="sidebar-widget">
                    <h3>Calendar</h3>
                    <div id="calendar">
                        <?= date('F Y', strtotime($current_month . '-01')) ?>
                        <?php
                        $date = new DateTime($current_month . '-01');
                        $days_in_month = $date->format('t');
                        $first_day = $date->format('w'); // 0 (Sun) to 6 (Sat)
                        
                        // Get all post dates for this month with their anchor links
                        $post_dates = [];
                        foreach ($posts as $post) {
                            $post_day = date('j', strtotime($post['post_date']));
                            $post_dates[$post_day] = 'post-' . $post['id'];
                        }
                        
                        // Get the day from URL if set
                        $selected_day = isset($_GET['day']) ? (int)$_GET['day'] : null;
                        $current_day = (date('Y-m') == $current_month) ? date('j') : null;
                        
                        echo '<table class="mini-calendar">';
                        echo '<tr><th>Su</th><th>Mo</th><th>Tu</th><th>We</th><th>Th</th><th>Fr</th><th>Sa</th></tr>';
                        echo '<tr>';
                        
                        // Blank cells for first week
                        for ($i = 0; $i < $first_day; $i++) {
                            echo '<td></td>';
                        }
                        
                        // Days of month
                        for ($day = 1; $day <= $days_in_month; $day++) {
                            if (($day + $first_day - 1) % 7 == 0 && $day != 1) {
                                echo '</tr><tr>';
                            }
                            
                            $classes = [];
                            if ($day == $current_day) $classes[] = 'current-day';
                            if ($day == $selected_day) $classes[] = 'selected-day';
                            if (isset($post_dates[$day])) $classes[] = 'has-post';
                            
                            echo '<td class="' . implode(' ', $classes) . '">';
                            
                            if (isset($post_dates[$day])) {
                                // Link to exact post on this day
                                echo '<a href="?month=' . $current_month . '&day=' . $day . '#' . $post_dates[$day] . '">' . $day . '</a>';
                            } else {
                                // Find the closest post to this day
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


                <!--Tags-->
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

                <!-- Monthly Archive -->
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

            <!---End Divs--->
               
                </div>


            </div>



            <!-- END SIDEBAR -->

        
        <div id="footer">    
            footer for cute girls
        </div>
        
</div>

    </div>
    
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scroll to anchor if day is selected
        <?php if (isset($_GET['day'])): ?>
            setTimeout(function() {
                const anchor = window.location.hash;
                if (anchor) {
                    const element = document.querySelector(anchor);
                    if (element) {
                        element.scrollIntoView({ behavior: 'smooth' });
                        
                        // Highlight the entry
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
