<?php
include 'includes/db.php';
session_start();

$post_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iis', $post_id, $user_id, $comment);

    if ($stmt->execute()) {
        echo "Comment added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

$sql = "SELECT * FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

echo "<h2>" . $post['title'] . "</h2>";
echo "<p>" . $post['content'] . "</p>";

if (isset($_SESSION['user_id'])) {
    echo "<form method='POST' action=''>
        <textarea name='comment' required></textarea><br>
        <button type='submit'>Add Comment</button>
    </form>";
}

$sql = "SELECT comments.comment, users.username, comments.created_at FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? ORDER BY comments.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$comments = $stmt->get_result();

while ($comment = $comments->fetch_assoc()) {
    echo "<p><strong>" . $comment['username'] . "</strong> (" . $comment['created_at'] . ")</p>";
    echo "<p>" . $comment['comment'] . "</p>";
}
?>