<?php
require 'db.php';
session_start();

// Debug temporaire (tu peux enlever après)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = '';
$success = '';

// Vérifier si user connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Si role n'existe pas encore dans la session, on le met par défaut à user
if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'user';
}

// Récupérer l'id du prompt
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Récupérer le prompt
if ($_SESSION['role'] === 'admin') {
    $stmt = $pdo->prepare("SELECT * FROM prompts WHERE id = ?");
    $stmt->execute([$id]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM prompts WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
}

$prompt = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prompt) {
    $error = "Prompt introuvable ou accès refusé.";
}

// Récupérer les catégories
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")
                  ->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $prompt) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category_id = isset($_POST['category_id']) ? (int) $_POST['category_id'] : 0;

    if ($title === '' || $content === '' || $category_id === 0) {
        $error = "All fields are required.";
    } elseif (strlen($title) < 3) {
        $error = "Title must be at least 3 characters.";
    } else {
        try {
            if ($_SESSION['role'] === 'admin') {
                $stmt = $pdo->prepare("
                    UPDATE prompts
                    SET title = ?, content = ?, category_id = ?
                    WHERE id = ?
                ");
                $stmt->execute([$title, $content, $category_id, $id]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE prompts
                    SET title = ?, content = ?, category_id = ?
                    WHERE id = ? AND user_id = ?
                ");
                $stmt->execute([$title, $content, $category_id, $id, $_SESSION['user_id']]);
            }

            $success = "Prompt updated successfully!";

            // Mettre à jour les valeurs affichées dans le formulaire
            $prompt['title'] = $title;
            $prompt['content'] = $content;
            $prompt['category_id'] = $category_id;

        } catch (PDOException $e) {
            $error = "Something went wrong: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Prompt</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">
        <a href="index.php">Prompt Repository</a>
    </div>

    <div class="navbar-links">
        <span class="welcome-text">
            Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
        </span>
        <a href="add_prompt.php" class="btn-add">+ Add Prompt</a>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>
</nav>

<div class="container">
    <h2>Edit Prompt</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($prompt): ?>
        <form method="POST" action="">

            <div class="form-group">
                <label for="title">Prompt Title</label>
                <input
                    id="title"
                    type="text"
                    name="title"
                    value="<?= htmlspecialchars($prompt['title']) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="content">Prompt Content</label>
                <textarea
                    id="content"
                    name="content"
                    rows="6"
                    required
                ><?= htmlspecialchars($prompt['content']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option
                            value="<?= $cat['id'] ?>"
                            <?= ((int)$cat['id'] === (int)$prompt['category_id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit">Save Changes</button>
            <a href="index.php" class="btn-cancel">Cancel</a>
        </form>
    <?php endif; ?>
</div>

</body>
</html>