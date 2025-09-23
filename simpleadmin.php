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
            INSERT INTO logs (title, content, post_date, category, tags, anchor_name) 
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
        
<script>
// Mini WYSIWYG editor with image insertion
(function() {
    const textarea = document.getElementById('editor');
    textarea.style.display = 'none'; // hide original textarea

    // Create editable div
    const editorDiv = document.createElement('div');
    editorDiv.contentEditable = true;
    editorDiv.id = 'mini-editor';
    editorDiv.style.minHeight = '300px';
    editorDiv.style.border = '1px solid #ccc';
    editorDiv.style.padding = '8px';
    editorDiv.style.marginBottom = '10px';
    editorDiv.innerHTML = textarea.value;

    // Insert editor after textarea
    textarea.parentNode.insertBefore(editorDiv, textarea.nextSibling);

    // Sync back to textarea on submit
    textarea.form.addEventListener('submit', () => {
        textarea.value = editorDiv.innerHTML;
    });

    // Toolbar
    const toolbar = document.createElement('div');
    toolbar.className = 'mini-toolbar';
    toolbar.style.marginBottom = '5px';

    // Buttons
    const buttons = [
        { cmd: 'bold', label: 'B' },
        { cmd: 'italic', label: 'I' },
        { cmd: 'underline', label: 'U' },
        { cmd: 'link', label: 'Link' },
        { cmd: 'image', label: 'Image' } // New image button
    ];

    buttons.forEach(b => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.innerHTML = b.label;
        btn.style.marginRight = '4px';

        btn.addEventListener('click', () => {
            if (b.cmd === 'link') {
                const url = prompt('Enter URL:');
                if (url) document.execCommand('createLink', false, url);
            } else if (b.cmd === 'image') {
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.accept = 'image/';
                fileInput.onchange = async () => {
                    const file = fileInput.files[0];
                    if (!file) return;
                    const formData = new FormData();
                    formData.append('image', file);

                    try {
                        const res = await fetch('simpleupload.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await res.json();
                        if (data.location) {
                            // Insert as HTML tag text, not as rendered image
                            const html = `<img src="${data.location}" />`;
                            // Insert at cursor position
                            document.execCommand('insertHTML', false, html);
                        } else {
                            alert('Upload failed');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Upload error');
                    }
                };
                fileInput.click();

            } else {
                document.execCommand(b.cmd, false, null);
            }
        });

        toolbar.appendChild(btn);
    });

    

    editorDiv.parentNode.insertBefore(toolbar, editorDiv);
})();
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