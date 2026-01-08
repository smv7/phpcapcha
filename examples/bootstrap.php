<?php
session_start();
$alert = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST['captcha'] ?? '';
    $stored = $_SESSION['captcha_code'] ?? '';
    if (strtolower($input) === strtolower($stored)) {
        $alert = '<div class="alert alert-success">Correct! You are human.</div>';
        unset($_SESSION['captcha_code']);
    } else {
        $alert = '<div class="alert alert-danger">Incorrect Code. Try again.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap 5 Captcha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-sm" style="width: 400px;">
        <div class="card-header bg-primary text-white text-center">
            <h5 class="mb-0">Bootstrap Form</h5>
        </div>
        <div class="card-body">
            <?= $alert ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" placeholder="name@example.com">
                </div>
                
                <label class="form-label">Captcha Verification</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="captcha" placeholder="Enter code" required>
                    <span class="input-group-text p-0 overflow-hidden">
                        <img src="captcha-endpoint.php?length=5" id="bs-captcha" height="38">
                    </span>
                    <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('bs-captcha').src='captcha-endpoint.php?length=5&'+Date.now()">
                        &#x21bb;
                    </button>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Submit Form</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
