<?php
session_start();
echo "Session started<br>";
session_unset();
echo "Session unset<br>";
session_destroy();
echo "Session destroyed<br>";

header("Location: ../main.php");
exit("Redirecting to main.php");