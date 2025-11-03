<?php
header('Content-Type: application/json');

// === CONFIG ===
$uploadDir = __DIR__ . '/products';
$baseUrl   = 'https://host.adoteam.dev/inventory/products';

// Ensure directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true);
}

// === VALIDATION ===
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["error" => "No file uploaded or upload error"]);
    exit;
}

$fileTmp  = $_FILES['file']['tmp_name'];
$fileSize = $_FILES['file']['size'];
$fileType = mime_content_type($fileTmp);

// Allow only PNG/JPEG
$allowedTypes = ['image/png' => 'png', 'image/jpeg' => 'jpg'];
if (!array_key_exists($fileType, $allowedTypes)) {
    echo json_encode(["error" => "Invalid file type"]);
    exit;
}

// === AUTO-RENAME ===
$ext = $allowedTypes[$fileType];
$uniqueName = uniqid('img_', true) . '.' . $ext;
$targetFile = $uploadDir . '/' . $uniqueName;
$publicUrl  = $baseUrl . '/' . $uniqueName;

// === MOVE FILE ===
if (!move_uploaded_file($fileTmp, $targetFile)) {
    echo json_encode(["error" => "Upload failed"]);
    exit;
}

// === RESPONSE ===
echo json_encode([
    "imageUrl" => $publicUrl,
    "fileSize" => $fileSize,
    "mimeType" => $fileType
]);