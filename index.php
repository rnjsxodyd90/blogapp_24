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
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a> | 
            <a href="register.php">Register</a>
        <?php endif; ?>
    </header>

    <main>
        <?php include 'view_post.php'; ?>
    </main>
</body>
</html>
