<?php
include 'includes/db.php';

$sql = "SELECT posts.id, posts.title, posts.content, users.username, posts.created_at FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC";
$result = $conn->query($sql);

while ($post = $result->fetch_assoc()) {
    echo "<h2>" . $post['title'] . "</h2>";
    echo "<p>by " . $post['username'] . " on " . $post['created_at'] . "</p>";
    echo "<p>" . $post['content'] . "</p>";
    echo "<a href='edit_post.php?id=" . $post['id'] . "'>Edit</a> | ";
    echo "<a href='delete_post.php?id=" . $post['id'] . "'>Delete</a>";
    echo "<hr>";
}
?>
