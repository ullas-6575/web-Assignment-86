<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Innovators</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <nav class="navbar">
        <div class="logo">The Innovators</div>
        <ul class="nav-links">
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About Us</a></li>
            <?php if ($isLoggedIn): ?>
                <li><a href="discussions.php">Discussions</a></li>
                <li><a href="events.php">Events</a></li>
                <li id="nav-user-info" style="display: flex; align-items: center; gap: 10px;">
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

    <header class="header-section" id="home">
        <div class="hero-box">
            <h1>Welcome to our community.</h1>
            <h1 class="huge-text">
                .THINK<br>
                <span>.CREATE.</span><br>
                .INNOVATE
            </h1>
            <h3>Every great innovation starts with a single idea.</h3>
            <p>A space where ideas are not judged, but nurtured. Share your thoughts, challenge perspectives, and
                collaborate with fellow innovators to turn imagination into impact.</p>
        </div>
    </header>

    <div style="max-width: 1000px; margin: 40px auto; padding: 0 20px; display: flex; gap: 20px;">
        <div class="sidebar-widget">
            <h3>Upcoming Club Events</h3>
            <ul>
                <li><strong>Friday, 8PM:</strong> Monthly meetup</li>
                <li><strong>Sunday, 10AM:</strong> Pitch Review</li>
            </ul>
        </div>
        <aside class="sidebar-widget">
            <h3>Members</h3>
            <ul class="member-list">
                <?php
                include 'db.php';
                $members_q = "SELECT fullname, role FROM users ORDER BY FIELD(role, 'president','moderator','member'), fullname";
                $members_r = mysqli_query($conn, $members_q);
                while ($m = mysqli_fetch_assoc($members_r)):
                ?>
                    <li>
                        <?php echo htmlspecialchars($m['fullname']); ?>
                        <span class="role-badge <?php echo $m['role']; ?>"><?php echo ucfirst($m['role']); ?></span>
                    </li>
                <?php endwhile; ?>
            </ul>
        </aside>

        <aside class="sidebar-widget">
            <h3>Upcoming Club Events</h3>
            <ul class="member-list">
                <?php
                $events_q = "SELECT title, event_date FROM events WHERE event_date >= NOW() ORDER BY event_date ASC LIMIT 5";
                $events_r = mysqli_query($conn, $events_q);
                if ($events_r && mysqli_num_rows($events_r) > 0):
                    while ($event = mysqli_fetch_assoc($events_r)):
                ?>
                    <li>
                        <strong><?php echo htmlspecialchars(date('D, M j', strtotime($event['event_date']))); ?>:</strong>
                        <?php echo htmlspecialchars($event['title']); ?>
                    </li>
                <?php
                    endwhile;
                else:
                ?>
                    <li>No upcoming events are scheduled.</li>
                <?php endif; ?>
                <?php mysqli_close($conn); ?>
            </ul>
        </aside>
    </div>

    <section class="about-section" id="about">
        <div class="about-box">
            <h2>About Us</h2>
            <p>Welcome to <strong>The Innovators</strong>! We are a community driven by creativity and the desire to
                build new things. Our club provides a safe and supportive space for members to pitch ideas, collaborate
                on projects, and host events that foster learning and growth.</p>
            <p>Whether you're a beginner or an experienced developer, there is a place for you here. Join us and be part
                of the next big idea.</p>
        </div>
    </section>

    <script src="script.js"></script>
</body>

</html>