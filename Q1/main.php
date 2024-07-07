<?php
session_start();
require_once 'Classes/Databasehandler.php';

$dbHandler = Databasehandler::getInstance();
$pdo = $dbHandler->connect();

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

<!-- Main -->
<div id="main">

    <!-- Header -->
    <header id="header">
        <h1>Gallery</h1>
        <p>Welcome to the art show</p>
        <?php
        if (isset($_SESSION['email'])) {
            echo "Logged in as: " . htmlspecialchars($_SESSION['email']);
            echo '<form action="includes/logout.inc.php" method="post">
            <button type="submit">Logout</button>
          </form>';
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
                echo '<article>';
                echo '<a class="thumbnail" href="uploads/' . htmlspecialchars($image['filename']) . '"><img src="uploads/' . htmlspecialchars($image['filename']) . '" alt="' . htmlspecialchars($image['caption']) . '"/></a>';
                echo '<h2>' . htmlspecialchars($image['user_id']) . '</h2>';
                echo '<p>' . htmlspecialchars($image['caption']) . '</p>';
                // Check if the logged-in user is the uploader of the image
                if (isset($_SESSION['email']) && $_SESSION['email'] === $image['user_id']) {
                    echo '<form action="includes/delete_image.inc.php" method="post">
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
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/browser.min.js"></script>
<script src="assets/js/breakpoints.min.js"></script>
<script src="assets/js/main.js"></script>

</body>
</html>