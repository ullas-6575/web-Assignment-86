<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['submit'])) {
    header('Location: events.php');
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'president') {
    header('Location: events.php?error=Only+the+president+can+add+events.');
    exit();
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$event_date_raw = trim($_POST['event_date'] ?? '');

if ($title === '' || $description === '' || $event_date_raw === '') {
    header('Location: events.php?error=All+fields+are+required.');
    exit();
}

$eventDate = DateTime::createFromFormat('Y-m-d\TH:i', $event_date_raw);
if (!$eventDate) {
    header('Location: events.php?error=Invalid+date+format.');
    exit();
}

$event_date = $eventDate->format('Y-m-d H:i:s');

$titleEscaped = mysqli_real_escape_string($conn, $title);
$descriptionEscaped = mysqli_real_escape_string($conn, $description);
$createdBy = intval($_SESSION['user_id'] ?? 0);

$insertQuery = "INSERT INTO events (title, description, event_date, created_by) VALUES ('$titleEscaped', '$descriptionEscaped', '$event_date', $createdBy)";

if (mysqli_query($conn, $insertQuery)) {
    mysqli_close($conn);
    header('Location: events.php?success=1');
    exit();
}

$error = mysqli_error($conn) ?: 'Could not save event.';
mysqli_close($conn);
header('Location: events.php?error=' . urlencode($error));
exit();
