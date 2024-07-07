<?php
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="assets/css/upload.css">
    <title>Upload Photos</title>
</head>
<body>
<form action="includes/upload.inc.php" method="post" enctype="multipart/form-data">
    <p id="heading">Post your photo</p>
    <?php

    if (true) {
        // User is logged in
        echo "Logged in as: " . htmlspecialchars($_SESSION['email']);
    }
    ?>

    <label for="file" class="custom-file-upload" id="clickableIcon">
        <div class="icon">
            <svg viewBox="0 0 24 24" fill="" xmlns="http://www.w3.org/2000/svg">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M10 1C9.73478 1 9.48043 1.10536 9.29289 1.29289L3.29289 7.29289C3.10536 7.48043 3 7.73478 3 8V20C3 21.6569 4.34315 23 6 23H7C7.55228 23 8 22.5523 8 22C8 21.4477 7.55228 21 7 21H6C5.44772 21 5 20.5523 5 20V9H10C10.5523 9 11 8.55228 11 8V3H18C18.5523 3 19 3.44772 19 4V9C19 9.55228 19.4477 10 20 10C20.5523 10 21 9.55228 21 9V4C21 2.34315 19.6569 1 18 1H10ZM9 7H6.41421L9 4.41421V7ZM14 15.5C14 14.1193 15.1193 13 16.5 13C17.8807 13 19 14.1193 19 15.5V16V17H20C21.1046 17 22 17.8954 22 19C22 20.1046 21.1046 21 20 21H13C11.8954 21 11 20.1046 11 19C11 17.8954 11.8954 17 13 17H14V16V15.5ZM16.5 11C14.142 11 12.2076 12.8136 12.0156 15.122C10.2825 15.5606 9 17.1305 9 19C9 21.2091 10.7909 23 13 23H20C22.2091 23 24 21.2091 24 19C24 17.1305 22.7175 15.5606 20.9844 15.122C20.7924 12.8136 18.858 11 16.5 11Z"
                          fill=""></path>
                </g>
            </svg>
        </div>
        <div class="text">
            <span>Click to upload image</span>
        </div>
        <input type="file" name="fileToUpload" id="fileToUpload">
    </label>
    <div class="form-control">
        <input type="text" name="caption" required>
        <label>
            <span style="transition-delay:0ms">C</span><span style="transition-delay:50ms">a</span><span
                    style="transition-delay:100ms">p</span><span style="transition-delay:150ms">t</span><span
                    style="transition-delay:200ms">i</span><span style="transition-delay:250ms">o</span><span
                    style="transition-delay:300ms">n</span>
        </label>
    </div>
    <button type="submit" class="button3" name="submit">Upload Image</button>
</form>
<script>
    // ensure DOM is fully loaded
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('clickableIcon').addEventListener('click', function () {
            document.getElementById('fileToUpload').click();
        });
        document.getElementById("fileToUpload").onchange = function () {
            document.getElementById("heading").innerHTML = "Image selected";
            document.getElementById("clickableIcon").style.display = "none";
        };
    });
</script>
</body>
</html>