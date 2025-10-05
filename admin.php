<?php
require_once 'auth.php';
check_login(); // This will show login form if not logged in

?>

<?php
session_start();

// ===== Configuration =====
$upload_dir = 'uploads/';
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
// ========================

// Initialize database connection
include 'config.php'; // <-- THIS WAS MISSING!

// Limit how many posts show in dropdown
$limit = 10;

try {
    $stmt = $pdo->query("
        SELECT id, title, visible, post_date 
        FROM logs 
        ORDER BY post_date DESC 
        LIMIT $limit
    ");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count hidden posts for info
    $totalPosts = $pdo->query("SELECT COUNT(*) FROM logs")->fetchColumn();
    $hiddenCount = max(0, $totalPosts - $limit);
} catch (PDOException $e) {
    $posts = [];
    $hiddenCount = 0;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    
    // Validate
    if (!in_array($file['type'], $allowed_types)) {
        $error = 'Only JPG, PNG, and GIF allowed.';
    } elseif ($file['size'] > $max_size) {
        $error = 'File too large (max 2MB).';
    } else {
        // Preserve original filename (sanitized)
        $filename = preg_replace('/[^a-z0-9_.-]/i', '_', basename($file['name']));
        $destination = $upload_dir . $filename;
        
        // Handle duplicates
        $counter = 1;
        while (file_exists($destination)) {
            $filename = pathinfo($file['name'], PATHINFO_FILENAME) . "_$counter." . pathinfo($file['name'], PATHINFO_EXTENSION);
            $destination = $upload_dir . $filename;
            $counter++;
        }
        
        // Compress while preserving name
        if ($file['type'] === 'image/jpeg') {
            $image = imagecreatefromjpeg($file['tmp_name']);
            imagejpeg($image, $destination, 85); // 85% quality
        } elseif ($file['type'] === 'image/png') {
            $image = imagecreatefrompng($file['tmp_name']);
            imagepng($image, $destination, 8); // 80% compression
        } else {
            move_uploaded_file($file['tmp_name'], $destination);
        }
        
        if (isset($image)) imagedestroy($image);
        $image_url = $destination;
    }

}


//===============================

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    // ... [keep your existing image upload code] ...
}

// Handle post submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    try {
        $content = $_POST['content'];
        $post_date = !empty($_POST['post_date']) ? $_POST['post_date'] : date('Y-m-d H:i:s');
        $edit_id = $_POST['edit_id'] ?? '';

        if (!empty($edit_id)) {
            // --- Update existing post ---
            $stmt = $pdo->prepare("
                UPDATE logs
                SET title = ?, content = ?, post_date = ?, category = ?, tags = ?, anchor_name = ?, visible = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $_POST['title'],
                $content,
                $post_date,
                $_POST['category'] ?? 'Uncategorized',
                $_POST['tags'] ?? '',
                $_POST['anchor_name'] ?? '',
                $_POST['visible'] ?? 'n',
                $edit_id
            ]);

            // Redirect to avoid TinyMCE reset
            header('Location: admin.php?success=updated');
            exit;
        } else {
            // --- Create new post ---
            $stmt = $pdo->prepare("
                INSERT INTO logs (title, content, post_date, category, tags, anchor_name, visible)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_POST['title'],
                $content,
                $post_date,
                $_POST['category'] ?? 'Uncategorized',
                $_POST['tags'] ?? '',
                $_POST['anchor_name'] ?? '',
                $_POST['visible'] ?? 'n'
            ]);

            // Redirect to avoid TinyMCE reset
            header('Location: admin.php?success=published');
            exit;
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}


?>



<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
     <link rel="stylesheet" href="style.css">
    <style>
        
        body { 
            font-family: Arial, sans-serif; 
            max-width: 800px; 
            margin: 0 auto;  }
            
        textarea { 
            width: 96%; 
            height: 300px; 
            
        }
        
        .image-preview  { 
        max-width: 200px; 
        display: block; 
        margin: 10px 0; 
            
        }
        
        .container {
        background-color: white;
        padding: 8px;
        background-repeat:repeat-y;
        min-height: 100vh;
        }
        .
    </style>
</head>

<body>
    
    <div class="container">
    <?php if (!empty($_GET['success'])): ?>
    <div class="success">
        <?php 
        if ($_GET['success'] === 'published') echo '✅ Post published!';
        elseif ($_GET['success'] === 'updated') echo '✅ Post updated!';
        ?>
    </div>
        <?php endif; ?>


        <?php
        // Fetch posts
        $stmt = $pdo->query("SELECT id, title, visible, post_date FROM logs ORDER BY post_date DESC");
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        
        <?php
        $editing = false;
        $edit_post = null;

        if (isset($_GET['edit'])) {
            $editing = true;
            $id = (int)$_GET['edit'];
            $stmt = $pdo->prepare("SELECT * FROM logs WHERE id = ?");
            $stmt->execute([$id]);
            $edit_post = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        ?>


        <form method="post" enctype="multipart/form-data">

        <p>

            <label>Edit existing post:<br>
                <select name="edit_id" onchange="loadPost(this.value)">
                <option value="">— New Post —</option>
                <optgroup label="Drafts">
                    <?php foreach ($posts as $p): ?>
                    <?php if ($p['visible'] == 'n'): ?>
                        <option value="<?= htmlspecialchars($p['id']) ?>">
                        <?= htmlspecialchars($p['title']) ?> (<?= htmlspecialchars($p['post_date']) ?>)
                        </option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </optgroup>
                    <optgroup label="Published">
                        <?php foreach ($posts as $p): ?>
                            <?php if ($p['visible'] == 'y'): ?>
                                <option value="<?= htmlspecialchars($p['id']) ?>">
                                    <?= htmlspecialchars($p['title']) ?> (<?= htmlspecialchars($p['post_date']) ?>)
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if ($hiddenCount > 0): ?>
                            <option disabled>+<?= $hiddenCount ?> more posts...</option>
                        <?php endif; ?>
                    </optgroup>
                </select>
            </label>
            </p>
            <p>
                <label>Title:<br>
                <input type="text" name="title" required style="width:96%"
                    value="<?= htmlspecialchars($edit_post['title'] ?? '') ?>">
                </label>
            </p>
            
            <p>
                <label>Content (HTML):<br>
                <textarea id="editor" name="content"><?= htmlspecialchars($edit_post['content'] ?? '') ?></textarea>
                </label>
            </p>
            
            </center>
            
            <p>
                <label>Date:<br>
                <input type="datetime-local" name="post_date"
       value="<?= isset($edit_post['post_date']) ? date('Y-m-d\TH:i', strtotime($edit_post['post_date'])) : '' ?>">
                </label>
            </p>
            
            <p>
                <label>Tags (comma-separated):<br>
                <input type="text" name="tags" placeholder="personal, diary, memories"
       value="<?= htmlspecialchars($edit_post['tags'] ?? '') ?>">
                </label>
            </p>

            <p>

            <label>Status:<br>
                <select name="visible">
                 <option value="n" <?= (isset($edit_post['visible']) && $edit_post['visible'] === 'n') ? 'selected' : '' ?>>Draft (Not Visible)</option>
                 <option value="y" <?= (isset($edit_post['visible']) && $edit_post['visible'] === 'y') ? 'selected' : '' ?>>Published (Visible)</option>
                </select>
            </label>
            </p>
            
            <button type="submit">Publish</button>
        </form>
    </div>

    
    
        <script>
        function insertImage() {
            const url = prompt("Paste image URL:");
            if (url) {
                document.querySelector('textarea').value += \n<img src="${url}">\n;
            }
        }
        </script>
        
        <?php include "tinycme.js"?>
<script>
  tinymce.init({
    selector: '#editor',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    images_upload_url: 'upload.php', // Your image upload endpoint
    automatic_uploads: true,
    images_upload_handler: function (blobInfo, progress) {
      return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', 'upload.php');
        
        xhr.upload.onprogress = function (e) {
          progress(e.loaded / e.total * 100);
        };
        
        xhr.onload = function() {
          if (xhr.status < 200 || xhr.status >= 300) {
            reject('HTTP Error: ' + xhr.status);
            return;
          }
          
          const json = JSON.parse(xhr.responseText);
          if (!json || typeof json.location != 'string') {
            reject('Invalid JSON: ' + xhr.responseText);
            return;
          }
          
          resolve(json.location);
        };
        
        xhr.onerror = function () {
          reject('Image upload failed due to a XHR Transport error. Status: ' + xhr.status);
        };
        
        const formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());
        
        xhr.send(formData);
      });
    }
  });
</script>
<script>
    function loadPost(id) {
    const title = document.querySelector('[name="title"]');
    const editor = tinymce.get('editor');
    const date = document.querySelector('[name="post_date"]');
    const tags = document.querySelector('[name="tags"]');
    const visible = document.querySelector('[name="visible"]');

    if (!id) {
        // 🧹 clear everything for a new post
        title.value = '';
        editor.setContent('');
        date.value = '';
        tags.value = '';
        visible.value = 'n';
        return;
    }

    // otherwise load existing post
    fetch('load_post.php?id=' + id)
        .then(r => r.json())
        .then(post => {
        title.value = post.title;
        editor.setContent(post.content);
        date.value = post.post_date.replace(' ', 'T');
        tags.value = post.tags;
        visible.value = post.visible;
        });
    }

</script>

<script>
window.onbeforeunload = function (e) {
    e = e || window.event;

    // For IE and Firefox prior to version 4
    if (e) {
        e.returnValue = 'Sure?';
    }

    // For Safari
    return 'Sure?';
};
</script>

</body>
</html>