<?php
session_start();
include 'db.php';

if (isset($_POST['submit'])) {
    $identifier = trim($_POST['identifier']); // username or email
    $password   = $_POST['password'];

    // Fetch user by username OR email
    $q   = "SELECT * FROM users WHERE username='$identifier' OR email='$identifier'";
    $res = mysqli_query($conn, $q);

    if (mysqli_num_rows($res) == 1) {
        $user = mysqli_fetch_assoc($res);

        // Verify hashed password
        if (password_verify($password, $user['password'])) {

            // ── SESSION ──
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];

            // ── COOKIE ( "remember me") ──
            if (isset($_POST['remember'])) {
                setcookie('username', $user['username'], time() + (7 * 24 * 3600), '/'); // 7 days
            }

            header("location: index.php");  // redirect to home after login
            exit();
        } else {
            header("location: login.php?error=invalid");
            exit();
        }
    } else {
        header("location: login.php?error=notfound");
        exit();
    }

    mysqli_close($conn);
}
?>
