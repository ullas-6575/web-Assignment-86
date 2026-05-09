<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussions - The Innovators</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo">The Innovators</div>
        <ul class="nav-links">
            <li><a href="index.php#home">Home</a></li>
            <li><a href="index.php#about">About Us</a></li>
            <li><a href="discussions.php">Discussions</a></li>
            <li><a href="events.php">Events</a></li>
            <li id="nav-user-info" style="display: flex; align-items: center; gap: 10px;">
                <span id="nav-user-name" class="nav-username"><?php echo "Welcome, " . $_SESSION['fullname']; ?></span>
                <a href="logout.php"><button class="logout-btn">Logout</button></a>
            </li>
        </ul>
    </nav>

    <div class="club-layout" id="discussions" style="margin-top: 40px;">
        <main class="discussion-area">
            <section class="new-idea-section">
                <h2>Pitch an Idea to the Club</h2>
                <input type="text" id="idea-title" placeholder="Idea Title (e.g., A new system for )">
                <textarea id="idea-desc" placeholder="Explain your concept to the members..."></textarea>
                <button onclick="postIdea()" class="post-btn">Pitch Idea</button>
            </section>

            <section class="discussion-board" id="board">
                <h2>Member Discussions</h2>
                <div class="idea-card">
                    <div class="member-author">Ullas</div>
                    <h3>Weekly Coding Workshops</h3>
                    <p>our club will launch in September. and we will start with weekly sessions to help beginners learn
                        HTML and CSS.
                    </p>
                    <div class="replies">
                        <p><strong>Reply from Mr Shontu:</strong> I'm in! Let's use the main clubhouse room.</p>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="script.js"></script>
</body>

</html>
