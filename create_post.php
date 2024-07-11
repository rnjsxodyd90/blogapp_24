<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$sql = "SELECT * FROM categories";
$result = $conn->query($sql);
$categories = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO posts (user_id, title, content, category_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issi', $user_id, $title, $content, $category_id);

    if ($stmt->execute()) {
        echo "Post created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
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
    <button type="submit">Create Post</button>
</form>
