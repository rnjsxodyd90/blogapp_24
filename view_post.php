<?php
include 'includes/db.php';
session_start();

$post_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['like'])) {
        $is_like = true;
    } elseif (isset($_POST['dislike'])) {
        $is_like = false;
    }
    
    if (isset($is_like)) {
        $user_id = $_SESSION['user_id'];
        
        $sql = "SELECT * FROM likes WHERE post_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $post_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $sql = "UPDATE likes SET is_like = ? WHERE post_id = ? AND user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iii', $is_like, $post_id, $user_id);
        } else {
            $sql = "INSERT INTO likes (post_id, user_id, is_like) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iii', $post_id, $user_id, $is_like);
        }
        
        $stmt->execute();
    }
}

$sql = "SELECT * FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

$sql = "SELECT SUM(is_like) AS likes, SUM(NOT is_like) AS dislikes FROM likes WHERE post_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$result = $stmt->get_result();
$likes = $result->fetch_assoc();

echo "<h2>" . $post['title'] . "</h2>";
echo "<p>" . $post['content'] . "</p>";

if (isset($_SESSION['user_id'])) {
    echo "<form method='POST' action=''>
        <button type='submit' name='like'>Like</button> (" . $likes['likes'] . ") 
        <button type='submit' name='dislike'>Dislike</button> (" . $likes['dislikes'] . ")
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
