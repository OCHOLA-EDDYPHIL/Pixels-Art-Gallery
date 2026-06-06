<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/assets/css/upload.css">
    <title>Upload Photos</title>
</head>
<body>
<form action="/upload" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
    <p id="heading">Post your photo</p>
    Logged in as: <?php echo htmlspecialchars((string) $email, ENT_QUOTES, 'UTF-8'); ?>

    <label for="fileToUpload" class="custom-file-upload" id="clickableIcon">
        <div class="icon">
            <svg viewBox="0 0 24 24" fill="" xmlns="http://www.w3.org/2000/svg"></svg>
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
