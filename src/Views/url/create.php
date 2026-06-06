<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/assets/css/shorten.css">
    <title>Shorten Url</title>
</head>
<body>

<div class="login-box">
    <form action="/includes/shortener.inc.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="user-box">
            <input type="url" name="longUrl" required>
            <label>Long Url</label>
        </div>
        <div style="text-align: center;">
            <button type="submit">Shorten URL</button>
        </div>
    </form>
</div>

</body>
</html>
