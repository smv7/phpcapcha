<?php
session_start();
$msg = '';
$msgColor = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST['captcha'] ?? '';
    $stored = $_SESSION['captcha_code'] ?? '';
    
    if (strtolower($input) === strtolower($stored)) {
        $msg = 'Successfully verified!';
        $msgColor = 'green';
        unset($_SESSION['captcha_code']);
    } else {
        $msg = 'Invalid code provided.';
        $msgColor = 'red';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailwind CSS Captcha</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-sm bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-indigo-600 p-4">
            <h2 class="text-white text-xl font-bold text-center">Authentication</h2>
        </div>
        
        <div class="p-6">
            <?php if($msg): ?>
                <div class="mb-4 p-3 rounded text-center <?= $msgColor === 'green' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Security Check</label>
                    <div class="flex items-center border border-indigo-200 rounded-md overflow-hidden bg-gray-50 p-1">
                        <input type="text" name="captcha" 
                               class="bg-transparent border-none w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none" 
                               placeholder="Enter code" required>
                        
                        <div class="border-l border-gray-300 pl-2 flex items-center gap-2">
                             <img src="captcha-endpoint.php?type=alpha" alt="Captcha" id="captImg" class="h-10 rounded">
                             <button type="button" onclick="document.getElementById('captImg').src='captcha-endpoint.php?type=alpha&'+Date.now()" 
                                     class="text-indigo-500 hover:text-indigo-800 focus:outline-none p-1 transition">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                             </button>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Please enter the characters shown in the image.</p>
                </div>
                
                <button type="submit" 
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 transform hover:-translate-y-0.5">
                    Verify Identity
                </button>
            </form>
        </div>
    </div>

</body>
</html>
