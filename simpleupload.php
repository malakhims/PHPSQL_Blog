<?php
require_once 'auth.php';
check_login(); // Ensure user is logged in

// ===== Configuration =====
$upload_dir = 'uploads/';
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_size = 2 * 1024 * 1024; // 2MB

if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];

    // Validate type
    if (!in_array($file['type'], $allowed_types)) {
        echo json_encode(['error' => 'Only JPG, PNG, GIF, and WEBP allowed.']);
        exit;
    }

    // Validate size
    if ($file['size'] > $max_size) {
        echo json_encode(['error' => 'File too large (max 2MB).']);
        exit;
    }

    // Sanitize filename
    $filename = preg_replace('/[^a-z0-9_.-]/i', '_', basename($file['name']));
    $destination = $upload_dir . $filename;

    // Handle duplicates
    $counter = 1;
    while (file_exists($destination)) {
        $filename = pathinfo($file['name'], PATHINFO_FILENAME) . "_$counter." . pathinfo($file['name'], PATHINFO_EXTENSION);
        $destination = $upload_dir . $filename;
        $counter++;
    }

    // Move the uploaded file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Return JSON with location for editor
        echo json_encode(['location' => $destination]);
    } else {
        echo json_encode(['error' => 'Failed to move uploaded file.']);
    }

} else {
    echo json_encode(['error' => 'No file uploaded.']);
}
