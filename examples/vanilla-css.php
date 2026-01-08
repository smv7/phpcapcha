<?php
session_start();
$msg = '';
$msgClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST['captcha'] ?? '';
    $stored = $_SESSION['captcha_code'] ?? '';
    
    if (strtolower($input) === strtolower($stored)) {
        $msg = 'Verification Successful!';
        $msgClass = 'success';
        unset($_SESSION['captcha_code']); // Regenerate/Clear
    } else {
        $msg = 'Incorrect Captcha code. Please try again.';
        $msgClass = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vanilla CSS Captcha Example</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; color: #333; margin-top: 0; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: #666; }
        input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-size: 16px; }
        .captcha-container { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; border: 1px solid #eee; padding: 5px; border-radius: 4px; }
        .captcha-img { display: block; height: 60px; }
        .refresh-btn { background: none; border: none; font-size: 24px; cursor: pointer; color: #666; padding: 0 10px; transition: transform 0.2s; }
        .refresh-btn:hover { color: #333; transform: rotate(180deg); }
        button[type="submit"] { width: 100%; background: #007bff; color: white; border: none; padding: 12px; border-radius: 4px; cursor: pointer; font-size: 16px; transition: background 0.3s; }
        button[type="submit"]:hover { background: #0056b3; }
        .alert { padding: 10px; margin-bottom: 1rem; border-radius: 4px; text-align: center; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Security Check</h2>
        
        <?php if($msg): ?>
            <div class="alert <?= $msgClass ?>"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Enter Captcha Word*</label>
                <div class="captcha-container">
                    <input type="text" name="captcha" placeholder="Type characters" required autocomplete="off">
                    <img src="captcha-endpoint.php" alt="Captcha" class="captcha-img" id="captchaImage">
                    <button type="button" class="refresh-btn" onclick="refreshCaptcha()">&#x21bb;</button>
                </div>
            </div>
            <button type="submit">Verify</button>
        </form>
    </div>

    <script>
        function refreshCaptcha() {
            document.getElementById('captchaImage').src = 'captcha-endpoint.php?' + Date.now();
        }
    </script>
</body>
</html>
