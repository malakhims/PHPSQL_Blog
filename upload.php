<?php
session_start();

// ===== Configuration =====// === Configuration ===
$upload_dir = 'uploads/';
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_size = 2 * 1024 * 1024; // 2MB
// ====================
// ========================

// Authentication
if (!isset($_SESSION['logged_in'])) {
    header('Location: admin.php');
    exit;
}



// Check login
if (!isset($_SESSION['logged_in'])) {
    die('Error: Not logged in');
}

// Create uploads directory if missing
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Process upload
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // Validate
    if (!in_array($file['type'], $allowed_types)) {
        die('Error: Invalid file type');
    }
    
    if ($file['size'] > $max_size) {
        die('Error: File too large');
    }
    
    // Generate unique filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $destination = $upload_dir . $filename;
    
    // Move file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        echo json_encode(['location' => $destination]);
    } else {
        die('Error: Upload failed');
    }
}
?>