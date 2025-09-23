<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/keys/auth.php';
check_login(); // This will show login form if not logged in

?>

<?php
session_start();

// ===== Configuration =====
$upload_dir = 'uploads/';
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
// ========================

// Initialize database connection
include 'config.php'; // <-- DO NOT TOUCH

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
        
        $stmt = $pdo->prepare("
            INSERT INTO posts (title, content, post_date, category, tags, anchor_name) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_POST['title'],
            $content,
            $_POST['post_date'] ?? date('Y-m-d H:i:s'),
            $_POST['category'] ?? 'Uncategorized',
            $_POST['tags'] ?? '',
            $_POST['anchor_name'] ?? ''
        ]);
        
        echo '<div class="success">Post published! <a href="index.php">View Blog</a></div>';
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage()); // Show errors
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
            
         <a href="upload.php" target="_blank">Upload Image Love</a>
        
        <form method="post" enctype="multipart/form-data">
            <p>
                <label>Title:<br>
                <input type="text" name="title" required style="width:96%">
                </label>
            </p>
            
            <p>
                <label>Content (HTML):<br>
                <textarea id="editor" name="content"></textarea>
                </label>
            </p>
            
            </center>
            
            <p>
                <label>Date:<br>
                <input type="datetime-local" name="post_date">
                </label>
            </p>
            
            <p>
                <label>Tags (comma-separated):<br>
                <input type="text" name="tags" placeholder="personal, diary, memories">
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
        
        <script src="https://cdn.tiny.cloud/1/9lr4jbj4uwtlwjr2ihwq9rtkp8668s2ctc5jszpmf5xitce1/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
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
