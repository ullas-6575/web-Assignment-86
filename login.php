<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Sign Up - The Innovators</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">The Innovators</div>
        <ul class="nav-links">
            <li><a href="index.php#home">Home</a></li>
            <li><a href="index.php#about">About Us</a></li>
            <li><a href="discussions.php">Discussions</a></li>
            <li><a href="index.php#events">Events</a></li>
        </ul>
    </nav>

    <div class="auth-container">
        <!-- Login Form -->
        <div class="auth-box" id="login-box">
            <h2>Sign In</h2>
            <?php
            $hasMessage = isset($_GET['registered']) || isset($_GET['error']);
            ?>
            <div class="auth-message" id="login-message" <?php if ($hasMessage) echo 'style="display: block;"'; ?>>
                <?php if (isset($_GET['registered']) && $_GET['registered'] == 1): ?>
                    <span style="color: #27ae60;">Registration successful! Please sign in.</span>
                <?php elseif (isset($_GET['error']) && $_GET['error'] == 'invalid'): ?>
                    <span style="color: #e74c3c;">Incorrect password. Please try again.</span>
                <?php elseif (isset($_GET['error']) && $_GET['error'] == 'notfound'): ?>
                    <span style="color: #e74c3c;">User not found. Check your username or email.</span>
                <?php endif; ?>
            </div>
            <form id="login-form" action="login_process.php" method="POST">
                <input type="text" name="identifier" placeholder="Username or Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <label style="display: flex; align-items: center; gap: 6px; margin: 10px 0; font-size: 0.9rem; color: #666; cursor: pointer;">
                    <input type="checkbox" name="remember" style="width: auto; margin: 0;"> Remember me
                </label>
                <button type="submit" name="submit" class="auth-submit-btn">Login</button>
            </form>
            <p>Don't have an account? <a href="#" onclick="toggleAuth('signup')">Sign Up</a></p>
        </div>

        <!-- Sign Up Form -->
        <div class="auth-box" id="signup-box" style="display: none;">
            <h2>Sign Up</h2>
            <div class="auth-message" id="signup-message"></div>
            <form id="signup-form" action="register_process.php" method="POST">
                <input type="text" name="fullname" placeholder="Full Name" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="submit" class="auth-submit-btn">Create Account</button>
            </form>
            <p>Already have an account? <a href="#" onclick="toggleAuth('login')">Sign In</a></p>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
