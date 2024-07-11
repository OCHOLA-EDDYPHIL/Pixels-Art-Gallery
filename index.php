<?php
// Start or resume a session
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags for responsive design and character set -->
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome</title>
    <!-- Link to external CSS -->
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<!-- Dynamic class addition based on session errors for signup -->
<div class="cont <?php echo !empty($_SESSION['signup_errors']) ? 's--signup' : ''; ?>">
    <div class="form sign-in">
        <!-- Display login error messages if they exist -->
        <?php if (!empty($_SESSION['login_errors'])): ?>
            <?php foreach ($_SESSION['login_errors'] as $error): ?>
                <div class="error-message">
                    <!-- Escape output to prevent XSS(Cross-Site Scripting) -->
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endforeach; ?>
            <!-- Clear login errors after displaying them -->
            <?php unset($_SESSION['login_errors']); ?>
        <?php endif; ?>
        <!-- Login form -->
        <form action="includes/login.inc.php" method="post">
            <h2>Welcome</h2>
            <label>
                <span>Email</span>
                <input type="email" name="email">
            </label>
            <label>
                <span>Password</span>
                <input type="password" name="pwd">
            </label>
            <p class="forgot-pass">Forgot password?</p>
            <button type="submit" class="submit">LOGIN</button>
        </form>
    </div>
    <div class="sub-cont">
        <div class="img">
            <div class="img__text m--up">

                <h3>Don't have an account? Please Sign up!</h3>
            </div>
            <div class="img__text m--in">

                <h3>If you already have an account, just sign in.</h3>
            </div>
            <!-- Button to toggle between sign up and sign in views -->
            <div class="img__btn">
                <span class="m--up">Sign Up</span>
                <span class="m--in">Sign In</span>
            </div>
        </div>
        <div class="form sign-up">
            <!-- Display signup error messages if they exist -->
            <?php if (!empty($_SESSION['signup_errors'])): ?>
                <?php foreach ($_SESSION['signup_errors'] as $error): ?>
                    <div class="error-message">
                        <!-- Escape output to prevent XSS(Cross-Site Scripting) -->
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endforeach; ?>
                <!-- Clear signup errors after displaying them -->
                <?php unset($_SESSION['signup_errors']); ?>
            <?php endif; ?>
            <!-- Signup form -->
            <form action="includes/signup.inc.php" method="post" id="signupForm">
                <h2>Create your Account</h2>
                <label>
                    <span>Email</span>
                    <input type="email" name="email"/>
                </label>
                <label>
                    <span>Password</span>
                    <input type="password" name="pwd" id="pwd"/>
                </label>
                <button type="submit" class="submit">Sign Up</button>
            </form>
        </div>
    </div>
</div>

<script>
    // This ensures the DOM is fully loaded before attempting to access elements
    document.addEventListener('DOMContentLoaded', function () {
        // Add a click event listener to the button that toggles the sign-up/sign-in view
        document.querySelector('.img__btn').addEventListener('click', function () {
            // Toggle the class that switches between sign-up and login views
            document.querySelector('.cont').classList.toggle('s--signup');
        });

        // Add a submit event listener to the sign-up form to validate the password before submission
        document.getElementById('signupForm').addEventListener('submit', function (event) {
            // Retrieve the value of the password input field
            let password = document.getElementById('pwd').value;
            // Check if the password meets the requirements: more than five characters and includes numbers
            if (password.length <= 5 || !/\d/.test(password)) {
                // Alert the user if the password does not meet the requirements
                alert('Password must be more than five characters and include numbers.');
                // Prevent the form from being submitted if the password does not meet the requirements
                event.preventDefault();
            }
        });
    });
</script>
</body>
</html>