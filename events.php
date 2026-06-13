<?php
session_start();
include 'db.php';

$isLoggedIn = isset($_SESSION['username']);
$userRole = $_SESSION['role'] ?? null;

$now = date('Y-m-d H:i:s');
$upcomingEvents = [];
$recentEvents = [];

$eventsQuery = "SELECT * FROM events ORDER BY event_date ASC";
$eventsResult = mysqli_query($conn, $eventsQuery);
if ($eventsResult) {
    while ($event = mysqli_fetch_assoc($eventsResult)) {
        if ($event['event_date'] >= $now) {
            $upcomingEvents[] = $event;
        } else {
            array_unshift($recentEvents, $event);
        }
    }
    mysqli_free_result($eventsResult);
}

$editingEvent = null;
$editId = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
if ($editId > 0 && $userRole === 'president') {
    $stmt = mysqli_prepare($conn, "SELECT * FROM events WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $editId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $editingEvent = $row;
    }
    mysqli_stmt_close($stmt);
}

$successMessage = '';
$errorMessage = '';
if (isset($_GET['success'])) {
    $status = htmlspecialchars($_GET['success']);
    $successMessage = $status === '1'
        ? 'Event created successfully.'
        : "Event $status successfully.";
} elseif (isset($_GET['error'])) {
    $errorMessage = 'Error: ' . htmlspecialchars($_GET['error']);
}

function formatEventDate($dateTime)
{
    return date('l, F j — g:i A', strtotime($dateTime));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - The Innovators</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo">The Innovators</div>
        <ul class="nav-links">
            <li><a href="index.php#home">Home</a></li>
            <li><a href="index.php#about">About Us</a></li>
            <?php if ($isLoggedIn): ?>
                <li><a href="discussions.php">Discussions</a></li>
                <li><a href="events.php">Events</a></li>
                <li id="nav-user-info" class="nav-user-info">
                    <span id="nav-user-name" class="nav-username"><?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
                    <a href="logout.php"><button class="logout-btn">Logout</button></a>
                </li>
            <?php else: ?>
                <li><a href="discussions.php">Discussions</a></li>
                <li><a href="events.php">Events</a></li>
                <li><button class="join-btn" id="nav-join-btn" onclick="joinClub()">Join Club</button></li>
                <li><button class="sign-in-btn" id="nav-signin-btn" onclick="signIn()">Sign In</button></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="events-page">

        <h1 class="events-page-title">Club Events</h1>

        <?php if ($userRole === 'president'): ?>
            <section class="events-section event-form-section">
                <?php if ($editingEvent): ?>
                    <h2 class="events-section-title">Edit Event</h2>
                    <form class="event-form" method="POST" action="edit_event.php">
                        <input type="hidden" name="id" value="<?php echo intval($editingEvent['id']); ?>">
                        <label>
                            Title
                            <input type="text" name="title" required placeholder="Event title" value="<?php echo htmlspecialchars($editingEvent['title']); ?>">
                        </label>
                        <label>
                            Description
                            <textarea name="description" required placeholder="Event description"><?php echo htmlspecialchars($editingEvent['description']); ?></textarea>
                        </label>
                        <label>
                            Date & Time
                            <input type="datetime-local" name="event_date" required value="<?php $dt = DateTime::createFromFormat('Y-m-d H:i:s', $editingEvent['event_date']); echo $dt ? htmlspecialchars($dt->format('Y-m-d\TH:i')) : ''; ?>">
                        </label>
                        <button type="submit" name="submit" class="post-btn">Update Event</button>
                        <a href="events.php" class="cancel-link">Cancel</a>
                    </form>
                <?php else: ?>
                    <h2 class="events-section-title">Create New Event</h2>
                    <form class="event-form" method="POST" action="post_event.php">
                        <label>
                            Title
                            <input type="text" name="title" required placeholder="Event title">
                        </label>
                        <label>
                            Description
                            <textarea name="description" required placeholder="Event description"></textarea>
                        </label>
                        <label>
                            Date & Time
                            <input type="datetime-local" name="event_date" required>
                        </label>
                        <button type="submit" name="submit" class="post-btn">Save Event</button>
                    </form>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php if ($successMessage): ?>
            <div class="flash-msg flash-success">
                <?php echo $successMessage; ?>
            </div>
        <?php elseif ($errorMessage): ?>
            <div class="flash-msg flash-error">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <section class="events-section" id="future-events">
            <h2 class="events-section-title">Upcoming Events</h2>
            <div class="events-grid">
                <?php if (count($upcomingEvents) === 0): ?>
                    <div class="event-card">
                        <p>No upcoming events are scheduled yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($upcomingEvents as $event): ?>
                        <div class="event-card future">
                            <div class="event-date"><?php echo htmlspecialchars(formatEventDate($event['event_date'])); ?></div>
                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                            <span class="event-badge upcoming">Upcoming</span>
                            <?php if ($userRole === 'president'): ?>
                                <div class="event-actions">
                                    <a href="events.php?edit=<?php echo intval($event['id']); ?>" class="event-actions-link">Edit</a>
                                    <span class="event-actions-separator">|</span>
                                    <form method="POST" action="delete_event.php" class="event-actions-form">
                                        <input type="hidden" name="id" value="<?php echo intval($event['id']); ?>">
                                        <button type="submit" class="event-action-button delete-button" onclick="return confirm('Delete this event?')">Delete</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="events-section" id="recent-events">
            <h2 class="events-section-title">Recent Events</h2>
            <div class="events-grid">
                <?php if (count($recentEvents) === 0): ?>
                    <div class="event-card">
                        <p>No recent events have been completed yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($recentEvents as $event): ?>
                        <div class="event-card past">
                            <div class="event-date"><?php echo htmlspecialchars(formatEventDate($event['event_date'])); ?></div>
                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                            <span class="event-badge past">Completed</span>
                            <?php if ($userRole === 'president'): ?>
                                <div class="event-actions">
                                    <a href="events.php?edit=<?php echo intval($event['id']); ?>" class="event-actions-link">Edit</a>
                                    <span class="event-actions-separator">|</span>
                                    <form method="POST" action="delete_event.php" class="event-actions-form">
                                        <input type="hidden" name="id" value="<?php echo intval($event['id']); ?>">
                                        <button type="submit" class="event-action-button delete-button" onclick="return confirm('Delete this event?')">Delete</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

            </div>
        </section>

    </div>

    <script src="script.js"></script>
</body>

</html>