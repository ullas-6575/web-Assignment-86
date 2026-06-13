<?php
session_start();
include 'db.php';

$members = [];
$query = "SELECT fullname, role FROM users ORDER BY FIELD(role, 'president','moderator','member'), fullname";
$res = mysqli_query($conn, $query);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $members[] = $row;
    }
    mysqli_free_result($res);
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Members — The Innovators</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">The Innovators</div>
        <ul class="nav-links">
            <li><a href="index.php#home">Home</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="discussions.php">Discussions</a></li>
            <li><a href="index.php">Back</a></li>
        </ul>
    </nav>

    <div class="events-page">
        <h1 class="events-page-title">All Members</h1>
        <div class="sidebar-widget">
            <ul class="member-list">
                <?php if (count($members) === 0): ?>
                    <li>No members yet.</li>
                <?php else: ?>
                    <?php foreach ($members as $m): ?>
                        <li>
                            <?php echo htmlspecialchars($m['fullname']); ?>
                            <span class="role-badge <?php echo htmlspecialchars($m['role']); ?>"><?php echo ucfirst(htmlspecialchars($m['role'])); ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>

</body>
</html>
