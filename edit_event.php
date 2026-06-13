<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['submit'])) {
    header('Location: events.php');
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'president') {
    header('Location: events.php');
    exit();
}


$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$event_date_raw = trim($_POST['event_date'] ?? '');

if ($id <= 0 || $title === '' || $description === '' || $event_date_raw === '') {
    header('Location: events.php?edit=' . $id . '&error=All+fields+are+required');
    exit();
}

if (strlen($title) > 255) {
    header('Location: events.php?edit=' . $id . '&error=Title+too+long');
    exit();
}

$eventDate = DateTime::createFromFormat('Y-m-d\TH:i', $event_date_raw);
if (!$eventDate) {
    header('Location: events.php?edit=' . $id . '&error=Invalid+date+format');
    exit();
}

$event_date = $eventDate->format('Y-m-d H:i:s');

$stmt = mysqli_prepare($conn, "UPDATE events SET title = ?, description = ?, event_date = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'sssi', $title, $description, $event_date, $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header('Location: events.php?success=updated');
    exit();
}

$err = mysqli_stmt_error($stmt) ?: 'Could not update event.';
mysqli_stmt_close($stmt);
mysqli_close($conn);
header('Location: events.php?edit=' . $id . '&error=' . urlencode($err));
exit();
