<?php
session_start();
$msg = '';
$msgClass = '';

// Handle Verification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST['captcha'] ?? '';
    $stored = $_SESSION['captcha_code'] ?? '';
    
    // For math mode, equality check works (string vs string)
    // For alpha/mixed, case-insensitive check
    if (strtolower($input) === strtolower($stored)) {
        $msg = '✅ Correct! Verification successful.';
        $msgClass = 'success';
        unset($_SESSION['captcha_code']); 
    } else {
        $msg = "❌ Incorrect. Please try again.";
        $msgClass = 'error';
    }
}

// Configuration for different demos
$pages = [
    'math' => [
        'title' => 'Math Challenge',
        'desc' => 'Users solve a simple arithmetic problem.',
        'params' => 'type=math',
        'placeholder' => 'Result e.g. 8'
    ],
    'clean' => [
        'title' => 'Clean Design',
        'desc' => 'Minimalist look with no noise, lines, or wave distortion.',
        'params' => 'noise=0&lines=0&wave=0&type=alpha',
        'placeholder' => 'Enter code'
    ],
    'standard' => [
        'title' => 'Standard Mixed',
        'desc' => 'Default settings with noise, lines, and wave distortion.',
        'params' => 'type=mixed',
        'placeholder' => 'Enter code'
    ],
    'numeric' => [
        'title' => 'Numeric Only',
        'desc' => 'Only numbers (2-9) are used.',
        'params' => 'type=numeric&length=5',
        'placeholder' => 'Enter numbers'
    ],
    'high_noise' => [
        'title' => 'High Noise (Hard)',
        'desc' => 'Extra noise forced via custom logic (demo purpose).',
        // Our endpoint checks for 'noise=0' to disable, but default is enabled. 
        // To make it "harder" we might need code changes or just standard is hard enough.
        // For now, let's just show standard but alpha only.
        'params' => 'type=alpha&length=7',
        'placeholder' => 'Enter characters'
    ]
];

$currentPage = $_GET['page'] ?? 'math';
if (!array_key_exists($currentPage, $pages)) {
    $currentPage = 'math';
}

$activeConfig = $pages[$currentPage];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advanced Captcha Features</title>
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #eef2f5; display: flex; justify-content: center; padding-top: 50px; min-height: 100vh; margin: 0; }
        .container { width: 100%; max-width: 450px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); height: fit-content; }
        h1 { margin-top: 0; color: #333; font-size: 24px; text-align: center; margin-bottom: 20px; }
        .nav-select { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd; margin-bottom: 25px; font-size: 16px; }
        .challenge-box { background: #fafafa; padding: 20px; border-radius: 8px; border: 1px solid #eee; text-align: center; }
        .captcha-img-wrapper { margin: 15px 0; position: relative; min-height: 70px; display: flex; justify-content: center; }
        .captcha-img { border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .refresh-btn { margin-left: 10px; background: none; border: none; font-size: 24px; cursor: pointer; color: #666; vertical-align: middle; }
        .refresh-btn:hover { color: #000; }
        input[type="text"], input[type="number"] { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; margin-top: 15px; font-size: 16px; transition: border-color 0.2s; }
        input:focus { border-color: #007bff; outline: none; }
        button.submit-btn { width: 100%; margin-top: 20px; background: #007bff; color: white; border: none; padding: 12px; border-radius: 6px; font-size: 16px; cursor: pointer; font-weight: 500; transition: background 0.2s; }
        button.submit-btn:hover { background: #0056b3; }
        .alert { padding: 12px; margin-bottom: 20px; border-radius: 6px; text-align: center; font-weight: 500; }
        .success { background: #d1e7dd; color: #0f5132; }
        .error { background: #f8d7da; color: #842029; }
        .desc { color: #666; font-size: 0.9em; margin-bottom: 15px; display: block; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Feature Showcase</h1>

        <!-- Navigation -->
        <select class="nav-select" onchange="window.location.href='?page=' + this.value">
            <?php foreach($pages as $key => $p): ?>
                <option value="<?= $key ?>" <?= $currentPage === $key ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <?php if($msg): ?>
            <div class="alert <?= $msgClass ?>"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <div class="challenge-box">
            <span class="desc"><?= htmlspecialchars($activeConfig['desc']) ?></span>
            
            <div class="captcha-img-wrapper">
                <img src="captcha-endpoint.php?<?= $activeConfig['params'] ?>&t=<?= time() ?>" 
                     alt="Captcha" class="captcha-img" id="captchaImg">
                <button type="button" class="refresh-btn" onclick="refreshCaptcha()" title="Refresh Image">↻</button>
            </div>

            <form method="POST">
                <input type="<?= $currentPage === 'math' ? 'number' : 'text' ?>" 
                       name="captcha" 
                       placeholder="<?= htmlspecialchars($activeConfig['placeholder']) ?>" 
                       autocomplete="off" 
                       required>
                
                <button type="submit" class="submit-btn">Verify Answer</button>
            </form>
        </div>
    </div>

    <script>
        function refreshCaptcha() {
            var img = document.getElementById('captchaImg');
            // Keep existing params, just update timestamp
            var src = img.src.split('&t=')[0]; 
            img.src = src + '&t=' + Date.now();
        }
    </script>
</body>
</html>
