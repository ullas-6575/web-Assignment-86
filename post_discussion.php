<?php
// post_discussion.php — Insert a new idea/discussion into the database

session_start();

// Auth check
if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit();
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("location: discussions.php");
    exit();
}

include 'db.php';

$title = trim($_POST['title'] ?? '');
$body  = trim($_POST['body'] ?? '');

// Validation
if (empty($title) || empty($body)) {
    header("location: discussions.php?error=Title+and+description+are+required.");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$title   = mysqli_real_escape_string($conn, $title);
$body    = mysqli_real_escape_string($conn, $body);

$q = "INSERT INTO discussions (user_id, title, body) VALUES ($user_id, '$title', '$body')";

if (mysqli_query($conn, $q)) {
    mysqli_close($conn);
    header("location: discussions.php?success=Idea+posted!");
    exit();
} else {
    mysqli_close($conn);
    header("location: discussions.php?error=Could+not+post+idea.+Try+again.");
    exit();
}
?>
