<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

$post_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $sql = "UPDATE posts SET title = ?, content = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi', $title, $content, $post_id);

    if ($stmt->execute()) {
        echo "Post updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    $sql = "SELECT * FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
}
?>

<form method="POST" action="">
    Title: <input type="text" name="title" value="<?php echo $post['title']; ?>" required><br>
    Content: <textarea name="content" required><?php echo $post['content']; ?></textarea><br>
    <button type="submit">Update Post</button>
</form>
