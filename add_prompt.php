<?php
//setup
require 'db.php';
session_start();

$error   = '';
$success = '';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
// : Fetch Categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")
                  ->fetchAll(PDO::FETCH_ASSOC);
//Validation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = trim($_POST['title']);
    $content     = trim($_POST['content']);
    $category_id = (int)$_POST['category_id'];

    if (empty($title) || empty($content) || empty($category_id)) {
        $error = "All fields are required.";

    } elseif (strlen($title) < 3) {
        $error = "Title must be at least 3 characters.";

    } 
    //Database Insert
     else {
        try {

            $stmt = $pdo->prepare("
                INSERT INTO prompts (title, content, user_id, category_id)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $title,
                $content,
                $_SESSION['user_id'],
                $category_id
            ]);

            $success = "Prompt saved successfully!";

        } catch (PDOException $e) {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>
<form method="POST" action="">

    <div class="form-group">
        <label for="title">Prompt Title</label>
        <input
            type="text"
            id="title"
            name="title"
            placeholder="e.g. Generate a REST API in Laravel"
            value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
            required
        >
    </div>

    <div class="form-group">
        <label for="content">Prompt Content</label>
        <textarea
            id="content"
            name="content"
            rows="6"
            placeholder="Write your full prompt here..."
            required
        ><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <option value="">Select a category</option>
            <?php foreach ($categories as $cat): ?>
                <option
                    value="<?= $cat['id'] ?>"
                    <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit">Save Prompt</button>

</form>