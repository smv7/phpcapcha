<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ajax Captcha Example</title>
    <style>
        body { font-family: sans-serif; padding: 2rem; background: #fff8f0; }
        .ajax-form { max-width: 400px; margin: 0 auto; background: white; padding: 20px; border: 2px solid #333; box-shadow: 5px 5px 0 #333; }
        .input-group { margin-bottom: 15px; }
        input { width: 100%; padding: 10px; border: 2px solid #ccc; box-sizing: border-box; }
        button { background: #333; color: white; border: none; padding: 10px 20px; cursor: pointer; font-weight: bold; width: 100%; }
        button:hover { background: #555; }
        .captcha-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 15px; }
        #status { margin-top: 10px; font-weight: bold; text-align: center; min-height: 20px; }
        .spin { animation: spin 1s linear infinite; }
        @keyframes spin { 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>

    <div class="ajax-form">
        <h2 style="margin-top:0">Ajax Validation</h2>
        <form id="ajaxForm">
            <div class="input-group">
                <input type="text" placeholder="Username" required>
            </div>
            
            <div class="captcha-row">
                <input type="text" id="captchaInput" placeholder="Captcha" style="width: 50%;" required>
                <div style="position: relative;">
                    <img src="captcha-endpoint.php" id="ajaxCaptcha" style="border: 1px solid #ccc; display: block;">
                    <a href="#" id="reloadLink" style="position: absolute; right: -25px; top: 50%; transform: translateY(-50%); text-decoration: none; font-size: 20px;">&#8635;</a>
                </div>
            </div>

            <button type="submit">Submit</button>
        </form>
        <div id="status"></div>
    </div>

    <script>
        const form = document.getElementById('ajaxForm');
        const status = document.getElementById('status');
        const captchaImg = document.getElementById('ajaxCaptcha');
        const reloadLink = document.getElementById('reloadLink');

        reloadLink.addEventListener('click', (e) => {
            e.preventDefault();
            reloadCaptcha();
        });

        function reloadCaptcha() {
            captchaImg.src = 'captcha-endpoint.php?' + Date.now();
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            status.textContent = 'Verifying...';
            status.style.color = 'blue';

            const captchaVal = document.getElementById('captchaInput').value;
            const formData = new FormData();
            formData.append('captcha', captchaVal);

            try {
                const response = await fetch('validate.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    status.textContent = data.message;
                    status.style.color = 'green';
                    // Maybe redirect or show success UI
                } else {
                    status.textContent = data.message;
                    status.style.color = 'red';
                    // Reload captcha on failure for security
                    reloadCaptcha();
                    document.getElementById('captchaInput').value = '';
                }

            } catch (error) {
                status.textContent = 'An error occurred.';
                status.style.color = 'red';
            }
        });
    </script>
</body>
</html>
