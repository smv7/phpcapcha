<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Olakunlevpn\Captcha\Captcha;

// Allow customization via query params for demonstration
$type = $_GET['type'] ?? 'mixed';
$length = $_GET['length'] ?? 6;

try {
    $captcha = new Captcha([
        'font' => __DIR__ . '/../fonts/Monaco.ttf', 
        'length' => (int)$length,
        'type' => $type
    ]);

    // Optional config from query params for advanced demos
    if (isset($_GET['noise']) && $_GET['noise'] === '0') {
        $captcha->setNoise(false);
    }
    if (isset($_GET['lines']) && $_GET['lines'] === '0') {
        $captcha->setLines(false);
    }
    if (isset($_GET['wave']) && $_GET['wave'] === '0') {
        $captcha->setDistortion(false);
    }

    // Set colors (reddish text like the example)
    $captcha->setTextColor(200, 50, 50)
            ->setBackgroundColor(255, 255, 255)
            ->create(200, 70);

    // Store code in session for validation
    $_SESSION['captcha_code'] = $captcha->getCode();

    $captcha->output();
    
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
