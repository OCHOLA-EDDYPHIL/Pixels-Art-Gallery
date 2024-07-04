<?php
session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<div class="cont <?php echo !empty($_SESSION['signup_errors']) ? 's--signup' : ''; ?>">
    <div class="form sign-in">
        <!-- Display error messages if they exist -->
        <?php if (!empty($_SESSION['login_errors'])): ?>
            <?php foreach ($_SESSION['login_errors'] as $error): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['login_errors']); ?>
        <?php endif; ?>
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
            <div class="img__btn">
                <span class="m--up">Sign Up</span>
                <span class="m--in">Sign In</span>
            </div>
        </div>
        <div class="form sign-up">
            <!-- Display error messages if they exist -->
            <?php if (!empty($_SESSION['signup_errors'])): ?>
                <?php foreach ($_SESSION['signup_errors'] as $error): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['signup_errors']); ?>
            <?php endif; ?>
            <form action="includes/signup.inc.php" method="post">
                <h2>Create your Account</h2>
                <label>
                    <span>Email</span>
                    <input type="email" name="email"/>
                </label>
                <label>
                    <span>Password</span>
                    <input type="password" name="pwd"/>
                </label>
                <button type="submit" class="submit">Sign Up</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // This ensures the DOM is fully loaded before attempting to access elements
        let hasSignupErrors = <?php echo !empty($_SESSION['signup_errors']) ? 'true' : 'false'; ?>;

        if (hasSignupErrors) {
            document.querySelector('.cont').classList.add('s--signup');
        }

        document.querySelector('.img__btn').addEventListener('click', function () {
            document.querySelector('.cont').classList.toggle('s--signup');
        });
    });</script>
</body>
</html>