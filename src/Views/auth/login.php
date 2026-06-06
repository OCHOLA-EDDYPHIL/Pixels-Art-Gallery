<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome</title>
    <link rel="stylesheet" href="/assets/css/index.css">
</head>
<body>

<div class="cont <?php echo !empty($signupErrors) ? 's--signup' : ''; ?>">
    <div class="form sign-in">
        <?php if (!empty($loginErrors)): ?>
            <?php foreach ($loginErrors as $error): ?>
                <div class="error-message"><?php echo htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        <form action="/includes/login.inc.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
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
            <?php if (!empty($signupErrors)): ?>
                <?php foreach ($signupErrors as $error): ?>
                    <div class="error-message"><?php echo htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
            <form action="/includes/signup.inc.php" method="post" id="signupForm">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
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
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('.img__btn').addEventListener('click', function () {
            document.querySelector('.cont').classList.toggle('s--signup');
        });

        document.getElementById('signupForm').addEventListener('submit', function (event) {
            let password = document.getElementById('pwd').value;
            if (password.length <= 5 || !/\d/.test(password)) {
                alert('Password must be more than five characters and include numbers.');
                event.preventDefault();
            }
        });
    });
</script>
</body>
</html>
