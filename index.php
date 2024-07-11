<?php
include 'includes/db.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blog App</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Welcome to the Blog App</h1>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="create_post.php">Create Post</a> | 
            <a href="logout.php">Logout</a> | 
            <a href="profile.php">Profile</a>
        <?php else: ?>
            <a href="login.php">Login</a> | 
            <a href="register.php">Register</a>
        <?php endif; ?>
    </header>

    <main>
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search posts...">
            <button type="submit">Search</button>
        </form>
        
        <?php
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        // Pagination setup
        $posts_per_page = 5;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $posts_per_page;

        // Count total posts for pagination
        $sql = "SELECT COUNT(*) AS total_posts FROM posts WHERE title LIKE ? OR content LIKE ?";
        $stmt = $conn->prepare($sql);
        $like_search = '%' . $search . '%';
        $stmt->bind_param('ss', $like_search, $like_search);
        $stmt->execute();
        $result = $stmt->get_result();
        $total_posts = $result->fetch_assoc()['total_posts'];

        $total_pages = ceil($total_posts / $posts_per_page);

        // Fetch posts with limit and offset
        $sql = "SELECT posts.id, posts.title, posts.content, users.username, posts.created_at 
                FROM posts 
                JOIN users ON posts.user_id = users.id 
                WHERE posts.title LIKE ? OR posts.content LIKE ? 
                ORDER BY posts.created_at DESC 
                LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssii', $like_search, $like_search, $posts_per_page, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($post = $result->fetch_assoc()) {
            echo "<h2>" . $post['title'] . "</h2>";
            echo "<p>by " . $post['username'] . " on " . $post['created_at'] . "</p>";
            echo "<p>" . $post['content'] . "</p>";
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']) {
                echo "<a href='edit_post.php?id=" . $post['id'] . "'>Edit</a> | ";
                echo "<a href='delete_post.php?id=" . $post['id'] . "'>Delete</a>";
            }
            echo "<hr>";
        }

        // Display pagination
        echo "<div class='pagination'>";
        for ($i = 1; $i <= $total_pages; $i++) {
            echo "<a href='?search=$search&page=$i'>$i</a> ";
        }
        echo "</div>";
        ?>
    </main>
</body>
</html>
