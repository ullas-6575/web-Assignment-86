<?php
// post_reply.php — Insert a reply to an existing discussion

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

$discussion_id = (int) ($_POST['discussion_id'] ?? 0);
$body          = trim($_POST['body'] ?? '');

// Validation
if ($discussion_id <= 0 || empty($body)) {
    header("location: discussions.php?error=Reply+cannot+be+empty.");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$body    = mysqli_real_escape_string($conn, $body);

$q = "INSERT INTO replies (discussion_id, user_id, body) VALUES ($discussion_id, $user_id, '$body')";

if (mysqli_query($conn, $q)) {
    mysqli_close($conn);
    header("location: discussions.php?success=Reply+posted!#discussion-$discussion_id");
    exit();
} else {
    mysqli_close($conn);
    header("location: discussions.php?error=Could+not+post+reply.+Try+again.");
    exit();
}
?>
