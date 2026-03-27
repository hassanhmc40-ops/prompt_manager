<?php
//setup
require 'db.php';
session_start();



if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
//Fetch Categories 

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")
                  ->fetchAll(PDO::FETCH_ASSOC);

$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;

//Fetch Prompts with INNER JOIN to link between the tables that i creat 
if ($category_id) {
    $stmt = $pdo->prepare("
        SELECT 
            prompts.id,
            prompts.title,
            prompts.content,
            prompts.created_at,
            prompts.user_id,
            users.username,
            categories.name AS category_name
        FROM prompts
        INNER JOIN users      ON prompts.user_id     = users.id
        INNER JOIN categories ON prompts.category_id = categories.id
        WHERE prompts.category_id = ?
        ORDER BY prompts.created_at DESC
    ");
    $stmt->execute([$category_id]);

} else {
    $stmt = $pdo->prepare("
        SELECT 
            prompts.id,
            prompts.title,
            prompts.content,
            prompts.created_at,
            prompts.user_id,
            users.username,
            categories.name AS category_name
        FROM prompts
        INNER JOIN users      ON prompts.user_id     = users.id
        INNER JOIN categories ON prompts.category_id = categories.id
        ORDER BY prompts.created_at DESC
    ");
    $stmt->execute();
}

$prompts = $stmt->fetchAll(PDO::FETCH_ASSOC);                 
?>
<!-- -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prompt Repository</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<nav class="navbar">

    <div class="navbar-brand">
        <a href="index.php">Prompt Repository</a>
    </div>

    <div class="navbar-links">
        <span class="welcome-text">
            Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
        </span>
        <a href="add_prompt.php" class="btn-add">+ Add Prompt</a>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>

</nav>

<!-- Component 4 continued — Filter Bar comes here -->
<div class="filter-bar">
    <form method="GET" action="">
        <select name="category_id">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option 
                    value="<?= $cat['id'] ?>"
                    <?= ($category_id == $cat['id']) ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filter</button>
    </form>
    <a href="index.php" class="clear-filter">Clear Filter</a>
</div>

<!-- Component 5 — Prompt Cards comes here         -->
<div class="container">

    <h2 class="section-title">
        <?php if ($category_id): ?>
            Prompts filtered by category
        <?php else: ?>
            All Prompts
        <?php endif; ?>
    </h2>

    <?php if (empty($prompts)): ?>

        <div class="empty-state">
            <p>No prompts found.</p>
            <a href="add_prompt.php">Be the first to add one</a>
        </div>

    <?php else: ?>

        <div class="prompts-grid">

            <?php foreach ($prompts as $prompt): ?>

                <div class="prompt-card">

                    <div class="card-header">
                        <h3 class="card-title">
                            <?= htmlspecialchars($prompt['title']) ?>
                        </h3>
                        <span class="card-category">
                            <?= htmlspecialchars($prompt['category_name']) ?>
                        </span>
                    </div>

                    <div class="card-content">
                        <p><?= htmlspecialchars($prompt['content']) ?></p>
                    </div>

                    <div class="card-footer">
                        <div class="card-meta">
                            <span>By <?= htmlspecialchars($prompt['username']) ?></span>
                            <span>·</span>
                            <span><?= date('M d Y', strtotime($prompt['created_at'])) ?></span>
                        </div>

                        <?php if ($prompt['user_id'] === $_SESSION['user_id']): ?>
                            <div class="card-actions">
                                <a href="edit_prompt.php?id=<?= $prompt['id'] ?>" 
                                   class="btn-edit">Edit</a>
                                <a href="delete_prompt.php?id=<?= $prompt['id'] ?>" 
                                   class="btn-delete"
                                   onclick="return confirm('Are you sure you want to delete this prompt?')">
                                    Delete
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>
</body>
</html>