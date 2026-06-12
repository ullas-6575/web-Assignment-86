<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);
$userRole = $_SESSION['role'] ?? 'guest'; // Defaults to guest if not logged in

include 'db.php';

// Fetch all discussions with author info
$disc_q = "SELECT d.id, d.title, d.body, d.created,
                  u.fullname AS author_name, u.username AS author_username, u.role AS author_role
           FROM discussions d
           JOIN users u ON d.user_id = u.id
           ORDER BY d.created DESC";
$disc_result = mysqli_query($conn, $disc_q);

// Fetch all replies keyed by discussion_id 
$reply_q = "SELECT r.id, r.discussion_id, r.body, r.created,
                   u.fullname AS author_name, u.username AS author_username, u.role AS author_role
            FROM replies r
            JOIN users u ON r.user_id = u.id
            ORDER BY r.created ASC";
$reply_result = mysqli_query($conn, $reply_q);

$replies = [];
if ($reply_result) {
    while ($r = mysqli_fetch_assoc($reply_result)) {
        $replies[$r['discussion_id']][] = $r;
    }
    mysqli_free_result($reply_result);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussions - The Innovators</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="discussions.css">
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

    <div class="club-layout" id="discussions" style="margin-top: 40px;">
        <main class="discussion-area">

            <?php if (isset($_GET['error'])): ?>
                <div class="flash-msg flash-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="flash-msg flash-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>

            <section class="new-idea-section">
                <h2>Pitch an Idea to the Club</h2>
                <?php if ($isLoggedIn): ?>
                    <form method="POST" action="post_discussion.php">
                        <input type="text" name="title" placeholder="Idea Title (e.g., A new system for...)" required>
                        <textarea name="body" placeholder="Explain your concept to the members..." required></textarea>
                        <button type="submit" class="post-btn">Pitch Idea</button>
                    </form>
                <?php else: ?>
                    <div style="padding: 15px; border: 1px dashed #ccc; background: #fafafa; border-radius: 6px; text-align: center; color: #555;">
                        Have an innovation to share? <a href="#" onclick="signIn()" style="color: #3498db; font-weight: bold; text-decoration: underline;">Sign in</a> to pitch your idea to the club.
                    </div>
                <?php endif; ?>
            </section>

            <section class="discussion-board" id="board">
                <h2>Member Discussions</h2>

                <?php if (!$disc_result || mysqli_num_rows($disc_result) === 0): ?>
                    <p class="empty-board">No ideas pitched yet. Be the first!</p>
                <?php else: ?>
                    <?php while ($disc = mysqli_fetch_assoc($disc_result)): ?>
                        <div class="idea-card" id="discussion-<?php echo $disc['id']; ?>">
                            <div class="idea-card-header">
                                <div class="member-author">
                                    <?php echo htmlspecialchars($disc['author_name']); ?>
                                    <span class="role-badge <?php echo htmlspecialchars($disc['author_role']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($disc['author_role'])); ?>
                                    </span>
                                </div>
                                <?php if ($userRole === 'moderator' || $userRole === 'president'): ?>
                                    <form method="POST" action="delete_discussion.php" class="delete-form"
                                          onsubmit="return confirm('Delete this discussion and all its replies?');">
                                        <input type="hidden" name="discussion_id" value="<?php echo $disc['id']; ?>">
                                        <button type="submit" class="delete-btn" title="Delete discussion">✕</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <h3><?php echo htmlspecialchars($disc['title']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($disc['body'])); ?></p>
                            <span class="idea-timestamp"><?php echo date('M j, Y \a\t g:i A', strtotime($disc['created'])); ?></span>

                            <div class="replies">
                                <?php if (isset($replies[$disc['id']])): ?>
                                    <?php foreach ($replies[$disc['id']] as $reply): ?>
                                        <div class="reply-item">
                                            <strong>
                                                <?php echo htmlspecialchars($reply['author_name']); ?>
                                                <span class="role-badge <?php echo htmlspecialchars($reply['author_role']); ?>">
                                                    <?php echo ucfirst(htmlspecialchars($reply['author_role'])); ?>
                                                </span>
                                            </strong>
                                            <p><?php echo nl2br(htmlspecialchars($reply['body'])); ?></p>
                                            <span class="reply-timestamp"><?php echo date('M j, Y \a\t g:i A', strtotime($reply['created'])); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="no-replies">No replies yet.</p>
                                <?php endif; ?>

                                <?php if ($isLoggedIn): ?>
                                    <form method="POST" action="post_reply.php" class="reply-form">
                                        <input type="hidden" name="discussion_id" value="<?php echo $disc['id']; ?>">
                                        <input type="text" name="body" placeholder="Write a reply..." required class="reply-input">
                                        <button type="submit" class="reply-btn">Reply</button>
                                    </form>
                                <?php else: ?>
                                    <p style="font-size: 0.85rem; color: #777; margin-top: 10px; border-top: 1px solid #eee; padding-top: 8px;">
                                        Please <a href="#" onclick="signIn()" style="color: #3498db; text-decoration: underline;">sign in</a> to participate in the thread.
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <?php mysqli_free_result($disc_result); ?>
                <?php endif; ?>

            </section>
        </main>
    </div>

    <script src="script.js"></script>
</body>

</html>
<?php
if (isset($conn) && $conn) {
    mysqli_close($conn);
}
?>