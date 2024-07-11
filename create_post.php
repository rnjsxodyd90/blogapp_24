<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category'];
    $tags = explode(',', $_POST['tags']);
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO posts (user_id, title, content, category_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issi', $user_id, $title, $content, $category_id);
    
    if ($stmt->execute()) {
        $post_id = $stmt->insert_id;
        
        foreach ($tags as $tag) {
            $tag = trim($tag);
            $sql = "INSERT INTO tags (name) VALUES (?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $tag);
            $stmt->execute();
            $tag_id = $stmt->insert_id;

            $sql = "INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $post_id, $tag_id);
            $stmt->execute();
        }
        
        echo "Post created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

$sql = "SELECT * FROM categories";
$result = $conn->query($sql);
$categories = $result->fetch_all(MYSQLI_ASSOC);
?>

<form method="POST" action="">
    Title: <input type="text" name="title" required><br>
    Content: <textarea name="content" required></textarea><br>
    Category: 
    <select name="category" required>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
        <?php endforeach; ?>
    </select><br>
    Tags (comma-separated): <input type="text" name="tags" placeholder="e.g., php, mysql, tutorial"><br>
    <button type="submit">Create Post</button>
</form>
