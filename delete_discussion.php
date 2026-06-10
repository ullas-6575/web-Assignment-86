<?php
// delete_discussion.php — Delete a discussion (moderator / president only)

session_start();

// Auth check
if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit();
}

// Role gate: only moderator or president can delete
$role = $_SESSION['role'] ?? 'member';
if ($role !== 'moderator' && $role !== 'president') {
    header("location: discussions.php?error=You+do+not+have+permission+to+delete+posts.");
    exit();
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("location: discussions.php");
    exit();
}

include 'db.php';

$discussion_id = (int) ($_POST['discussion_id'] ?? 0);

if ($discussion_id <= 0) {
    header("location: discussions.php?error=Invalid+discussion.");
    exit();
}

// Delete the discussion (CASCADE will remove replies too)
$q = "DELETE FROM discussions WHERE id = $discussion_id";

if (mysqli_query($conn, $q)) {
    mysqli_close($conn);
    header("location: discussions.php?success=Discussion+deleted.");
    exit();
} else {
    mysqli_close($conn);
    header("location: discussions.php?error=Could+not+delete+discussion.");
    exit();
}
?>
