<?php
include 'db.php';

if (isset($_POST['submit'])) {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($fullname) || empty($username) || empty($email) || empty($password)) {
        die("All fields are required.");
    }
    if (strlen($password) < 8) {
        die("Password must be at least 8 characters.");
    }

    // Check if username or email already exists
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' OR email='$email'");
    if (mysqli_num_rows($check) > 0) {
        die("Username or email already taken.");
    }

    // Hash password before storing
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $q = "INSERT INTO users (fullname, username, email, password)
          VALUES ('$fullname', '$username', '$email', '$hashed')";

    if (mysqli_query($conn, $q)) {
        header("location: login.php?registered=1");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
