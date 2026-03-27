<?php
require 'db.php';
session_start();
$error = '';
// this is for the login page,
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "All fields are required.";

    } else {
        // Component 3 and 4 go here
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 4 Password Verification and Session Creation

        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            header('Location: index.php');
            exit;
    }else {
            $error = "Invalid email or password.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Prompt Repository</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">

    <h1>Welcome Back</h1>
    <p>Login to access the Prompt Repository</p>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">

        <div class="form-group">
            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                required
            >
        </div>

        <button type="submit">Login</button>

    </form>

    <p class="redirect-link">
        Don't have an account?
        <a href="register.php">Register here</a>
    </p>

</div>

</body>
</html>