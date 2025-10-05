<?php
require_once 'auth.php';
check_login(); // require login

session_start();

// ===== Configuration =====
$upload_dir = 'uploads/';
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
// ========================

// Initialize database connection
include 'config.php';

// Limit how many posts show in dropdown
$limit = 20;

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


// ===== Handle post submission =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    try {
        $content = $_POST['content'];
        $post_date = !empty($_POST['post_date']) ? $_POST['post_date'] : date('Y-m-d H:i:s');

        // Use new hidden field to ensure correct update
        $edit_id = $_POST['edit_id_hidden'] ?? '';

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
        header("Location: admin.php?success=updated"); // for updates
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
            header("Location: admin.php?success=1");
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
            margin: 0 auto;
        }
        textarea {
            width: 96%;
            height: 300px;
        }
        .container {
            background-color: white;
            padding: 8px;
            min-height: 100vh;
        }
        .mini-toolbar button {
            padding: 4px 6px;
        }
    </style>
</head>

<body>
<div class="container">
    <?php if (!empty($_GET['success'])): ?>
        <div class="success">
            <?php 
            if ($_GET['success'] === '1') echo '✅ Post published!';
            elseif ($_GET['success'] === 'updated') echo '✅ Post updated!';
            ?>
            <a href="index.php">View Blog</a>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <p>
            <label>Edit existing post:<br>
                <select id="post_select" name="edit_id" onchange="loadPost(this.value)">
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

        <!-- ✅ Hidden field to track the selected post -->
        <input type="hidden" name="edit_id_hidden" id="edit_id_hidden" value="">

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

        <p>
            <label>Status:<br>
                <select name="visible">
                    <option value="n">Draft (Not Visible)</option>
                    <option value="y">Published (Visible)</option>
                </select>
            </label>
        </p>

        <button type="submit">Publish</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const textarea = document.getElementById('editor');
    textarea.style.display = 'none';

    // Create editor
    const editorDiv = document.createElement('div');
    editorDiv.contentEditable = true;
    editorDiv.id = 'mini-editor';
    editorDiv.style.minHeight = '300px';
    editorDiv.style.border = '1px solid #ccc';
    editorDiv.style.padding = '8px';
    editorDiv.style.marginBottom = '10px';
    editorDiv.innerHTML = textarea.value;
    textarea.parentNode.insertBefore(editorDiv, textarea.nextSibling);

    // Toolbar
    const toolbar = document.createElement('div');
    toolbar.className = 'mini-toolbar';
    toolbar.style.marginBottom = '5px';

    const buttons = [
        { cmd: 'bold', label: 'B' },
        { cmd: 'italic', label: 'I' },
        { cmd: 'underline', label: 'U' },
        { cmd: 'link', label: 'Link' },
        { cmd: 'image', label: 'Image' }
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
                fileInput.accept = 'image/*';
                fileInput.onchange = async () => {
                    const file = fileInput.files[0];
                    if (!file) return;
                    const formData = new FormData();
                    formData.append('image', file);

                    try {
                        const res = await fetch('simpleupload.php', { method: 'POST', body: formData });
                        const data = await res.json();
                        if (data.location) {
                            document.execCommand('insertHTML', false, `<img src="${data.location}" />`);
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

    // Sync editor content to textarea
    textarea.form.addEventListener('submit', () => {
        textarea.value = editorDiv.innerHTML;
    });

    // === Load post function ===
    window.loadPost = function(id) {
        const title = document.querySelector('[name="title"]');
        const date = document.querySelector('[name="post_date"]');
        const tags = document.querySelector('[name="tags"]');
        const visible = document.querySelector('[name="visible"]');
        const hiddenEditId = document.getElementById('edit_id_hidden');

        if (!id) {
            title.value = '';
            editorDiv.innerHTML = '';
            date.value = '';
            tags.value = '';
            visible.value = 'n';
            hiddenEditId.value = ''; // new post
            return;
        }

        fetch('load_post.php?id=' + id)
            .then(r => r.json())
            .then(post => {
                title.value = post.title;
                editorDiv.innerHTML = post.content;
                date.value = post.post_date.replace(' ', 'T');
                tags.value = post.tags;
                visible.value = post.visible;
                hiddenEditId.value = id; // ✅ keep ID for updates
                editorDiv.focus();
                document.getSelection().collapse(editorDiv, 0);
            });
    };
});
</script>

</body>
</html>
