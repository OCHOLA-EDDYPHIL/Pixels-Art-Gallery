<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Beautiful</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
    <link rel="stylesheet" href="/assets/css/main.css"/>
    <noscript>
        <link rel="stylesheet" href="/assets/css/noscript.css"/>
    </noscript>
</head>
<body class="is-preload-0 is-preload-1 is-preload-2">
<style>
    .upload-link {
        display: inline-block;
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        font-weight: bold;
        border-radius: 5px;
        margin-top: 10px;
    }
</style>
<div id="main">
    <header id="header">
        <h1>Pixels</h1>
        <p>Welcome to the art show</p>
        <?php if ($currentUserEmail !== null): ?>
            Logged in as: <?php echo htmlspecialchars((string) $currentUserEmail, ENT_QUOTES, 'UTF-8'); ?>
            <form action="/logout" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"/>
                <button type="submit">Logout</button>
            </form>
            <a href="/upload.php" class="upload-link">Upload Photo</a>
        <?php else: ?>
            <form action="/index.php" method="get">
                <button type="submit">Login</button>
            </form>
        <?php endif; ?>
    </header>
    <section id="thumbnails">
        <?php if ($error !== null): ?>
            <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        <?php endif; ?>
        <?php foreach ($images as $image): ?>
            <article class="image-container">
                <a class="thumbnail" href="/uploads/<?php echo htmlspecialchars($image['filename'], ENT_QUOTES, 'UTF-8'); ?>">
                    <img src="/uploads/<?php echo htmlspecialchars($image['filename'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($image['caption'], ENT_QUOTES, 'UTF-8'); ?>"/>
                </a>
                <h2><?php echo htmlspecialchars($image['user_id'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($image['caption'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php if ($currentUserEmail !== null && $currentUserEmail === $image['user_id']): ?>
                    <form action="/images/delete" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"/>
                        <input type="hidden" name="filename" value="<?php echo htmlspecialchars($image['filename'], ENT_QUOTES, 'UTF-8'); ?>"/>
                        <button type="submit">Delete</button>
                    </form>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </section>

    <footer id="footer">
        <ul class="copyright">
            <li>&copy; Untitled.</li>
            <li>Design: OCHOLA</li>
        </ul>
    </footer>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const containers = document.querySelectorAll('.image-container');
        containers.forEach(container => {
            let maxHeight = 0;
            const images = container.querySelectorAll('img');
            images.forEach(img => {
                if (img.offsetHeight > maxHeight) {
                    maxHeight = img.offsetHeight;
                }
            });
            images.forEach(img => {
                img.style.height = `${maxHeight}px`;
            });
        });
    });
</script>
<script src="/assets/js/jquery.min.js"></script>
<script src="/assets/js/browser.min.js"></script>
<script src="/assets/js/breakpoints.min.js"></script>
<script src="/assets/js/main.js"></script>

</body>
</html>
