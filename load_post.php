<?php
require 'config.php';

// Basic security check
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT id, title, content, post_date, tags, visible FROM logs WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    http_response_code(404);
    echo json_encode(['error' => 'Post not found']);
    exit;
}

// Ensure correct datetime-local format for HTML inputs
if (!empty($post['post_date'])) {
    $post['post_date'] = date('Y-m-d\TH:i', strtotime($post['post_date']));
}

header('Content-Type: application/json');
echo json_encode($post);
