<?php
session_start();

if (isset($_SESSION['email'])) {
    // User is logged in
    echo "Logged in as: " . htmlspecialchars($_SESSION['email']);
    echo '<form action="includes/logout.inc.php" method="post">
            <button type="submit">Logout</button>
          </form>';
} else {
    // User is not logged in
    echo '<form action="index.php" method="get">
            <button type="submit">Login</button>
          </form>';
}
