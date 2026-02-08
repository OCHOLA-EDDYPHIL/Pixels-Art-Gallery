<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/session_config.php';
require_once __DIR__ . '/includes/csrf.php';

use App\Container;

$pdo = Container::db();
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Beautiful</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
    <link rel="stylesheet" href="assets/css/main.css"/>
    <noscript>
        <link rel="stylesheet" href="assets/css/noscript.css"/>
    </noscript>
</head>
<body class="is-preload-0 is-preload-1 is-preload-2">
<style>
    .upload-link {
        display: inline-block;
        background-color: #4CAF50; /* Green background */
        color: white; /* White text */
        padding: 10px 20px; /* Some padding */
        text-align: center; /* Centered text */
        text-decoration: none; /* No underline */
        font-weight: bold; /* Make the text bold */
        border-radius: 5px; /* Rounded corners */
        margin-top: 10px; /* Some space from the top */
    }
</style>
<!-- Main -->
<div id="main">

    <!-- Header -->
    <header id="header">
        <h1>Pixels</h1>
        <p>Welcome to the art show</p>
        <?php
        if (isset($_SESSION['email'])) {
            echo "Logged in as: " . htmlspecialchars($_SESSION['email']);
            echo '<form action="includes/logout.inc.php" method="post">
            <input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCsrfToken()) . '"/>
            <button type="submit">Logout</button>
          </form>
        <a href="upload.php" class="upload-link">Upload Photo</a>';
        } else {
            echo '<form action="index.php" method="get">
            <button type="submit">Login</button>
          </form>';
        }
        ?>
    </header>
    <!-- Thumbnail -->
    <section id="thumbnails">
        <?php
        try {
            $stmt = $pdo->query("SELECT filename, caption, user_id FROM photos"); // Ensure this matches your actual table and columns
            $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($images as $image) {
                echo '<article class="image-container">';
                echo '<a class="thumbnail" href="uploads/' . htmlspecialchars($image['filename']) . '"><img src="uploads/' . htmlspecialchars($image['filename']) . '" alt="' . htmlspecialchars($image['caption']) . '"/></a>';
                echo '<h2>' . htmlspecialchars($image['user_id']) . '</h2>';
                echo '<p>' . htmlspecialchars($image['caption']) . '</p>';
                // Check if the logged-in user is the uploader of the image
                if (isset($_SESSION['email']) && $_SESSION['email'] === $image['user_id']) {
                    echo '<form action="includes/delete_image.inc.php" method="post">
                    <input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCsrfToken()) . '"/>
                    <input type="hidden" name="filename" value="' . htmlspecialchars($image['filename']) . '"/>
                    <button type="submit">Delete</button>
                  </form>';
                }
                echo '</article>';
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
    </section>

    <!-- Footer -->
    <footer id="footer">
        <ul class="copyright">
            <li>&copy; Untitled.</li>
            <li>Design: OCHOLA</li>
        </ul>
    </footer>

</div>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Select all elements with the class 'image-container'
        const containers = document.querySelectorAll('.image-container');
        containers.forEach(container => {
            let maxHeight = 0; // Initialize maxHeight to 0
            const images = container.querySelectorAll('img'); // Select all 'img' elements within the current container
            // Iterate over each image to find the maximum height
            images.forEach(img => {
                if (img.offsetHeight > maxHeight) {
                    maxHeight = img.offsetHeight; // Update maxHeight if the current image's height is greater
                }
            });
            // Set the height of all images to maxHeight to ensure uniformity
            images.forEach(img => {
                img.style.height = `${maxHeight}px`;
            });
        });
    });
    // This script ensures that all images within each 'image-container' have the same height,
    // improving the visual consistency of the gallery.
</script>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/browser.min.js"></script>
<script src="assets/js/breakpoints.min.js"></script>
<script src="assets/js/main.js"></script>

</body>
</html>
