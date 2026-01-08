<?php
session_start();

header('Content-Type: application/json');

$input = $_POST['captcha'] ?? '';
$stored = $_SESSION['captcha_code'] ?? '';

if (empty($input)) {
    echo json_encode(['success' => false, 'message' => 'Please enter the captcha.']);
    exit;
}

// Case insensitive comparison
if (strtolower($input) === strtolower($stored)) {
    echo json_encode(['success' => true, 'message' => 'Captcha Matched!']);
    // Clear session to prevent replay
    unset($_SESSION['captcha_code']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Captcha. Try again.']);
}
