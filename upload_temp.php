<?php
session_start();
include_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['pdfFile'])) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded.']);
    exit;
}

$pdfFile = $_FILES['pdfFile'];

if (!in_array($pdfFile['type'], ALLOWED_FILE_TYPES) || $pdfFile['size'] > MAX_FILE_SIZE) {
    echo json_encode(['success' => false, 'error' => 'Invalid file type or size exceeds limit.']);
    exit;
}

// Validate PDF content
$tmpFile = $pdfFile['tmp_name'];
if (!is_uploaded_file($tmpFile) || !is_readable($tmpFile)) {
    echo json_encode(['success' => false, 'error' => 'Invalid file upload.']);
    exit;
}

// Generate secure filename
$extension = '.pdf';
$filename = bin2hex(random_bytes(16)) . $extension;
$uploadPath = FILES_PATH . '/' . $filename;

// Move file to upload directory
if (!move_uploaded_file($tmpFile, $uploadPath)) {
    echo json_encode(['success' => false, 'error' => 'Failed to upload file.']);
    exit;
}

// Store file path in session with a token
$token = bin2hex(random_bytes(8));
$_SESSION['uploads'][$token] = $uploadPath;
$_SESSION['upload_times'][$token] = time();

echo json_encode(['success' => true, 'token' => $token]);