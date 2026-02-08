<?php
require_once __DIR__ . '/includes/session_config.php';
require_once __DIR__ . '/includes/csrf.php';

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['email'])) {
    header('Location: index.php'); // Redirect to login page if no email session exists
    exit(); // Stop script execution after redirection
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="assets/css/upload.css"> <!-- Link to the stylesheet for the upload page -->
    <title>Upload Photos</title> <!-- Title of the page -->
</head>
<body>
<form action="includes/upload.inc.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
    <p id="heading">Post your photo</p> <!-- Heading of the form -->
    <?php

    if (true) { // This condition is always true; intended to demonstrate user login status check
        // Display the logged-in user's email
        echo "Logged in as: " . htmlspecialchars($_SESSION['email']);
    }
    ?>

    <label for="file" class="custom-file-upload" id="clickableIcon">
        <!-- Custom file upload button -->
        <div class="icon">
            <!-- SVG icon for the upload button -->
            <svg viewBox="0 0 24 24" fill="" xmlns="http://www.w3.org/2000/svg">
                <!-- SVG content omitted for brevity -->
            </svg>
        </div>
        <div class="text">
            <span>Click to upload image</span> <!-- Text displayed on the upload button -->
        </div>
        <input type="file" name="fileToUpload" id="fileToUpload"> <!-- Hidden file input -->
    </label>
    <div class="form-control">
        <input type="text" name="caption" required> <!-- Input field for image caption -->
        <label>
            <!-- Animated label for the caption input -->
            <span style="transition-delay:0ms">C</span><span style="transition-delay:50ms">a</span><span
                    style="transition-delay:100ms">p</span><span style="transition-delay:150ms">t</span><span
                    style="transition-delay:200ms">i</span><span style="transition-delay:250ms">o</span><span
                    style="transition-delay:300ms">n</span>
        </label>
    </div>
    <button type="submit" class="button3" name="submit">Upload Image</button> <!-- Submit button for the form -->
</form>
<script>
    // ensure DOM is fully loaded
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('clickableIcon').addEventListener('click', function () {
            document.getElementById('fileToUpload').click(); // Trigger file input click on custom button click
        });
        document.getElementById("fileToUpload").onchange = function () {
            document.getElementById("heading").innerHTML = "Image selected"; // Update heading text when a file is selected
            document.getElementById("clickableIcon").style.display = "none"; // Hide the custom upload button after file selection
        };
    });
</script>
</body>
</html>
