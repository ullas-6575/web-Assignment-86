<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: events.php');
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'president') {
    header('Location: events.php');
    exit();
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    header('Location: events.php');
    exit();
}

// Use prepared statement to prevent SQL injection
$stmt = mysqli_prepare($conn, "DELETE FROM events WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header('Location: events.php?success=deleted');
    exit();
}

$err = mysqli_stmt_error($stmt) ?: 'Could not delete event.';
mysqli_stmt_close($stmt);
mysqli_close($conn);
header('Location: events.php?error=' . urlencode($err));
exit();
