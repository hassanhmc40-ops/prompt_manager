<?php
require 'db.php';
session_start();

$error   = '';
$success = '';
if ($_SERVER["REQUEST_METHOD"]==="POST"){
    // 1.this step is to colllect and clean the input
$username = trim($_POST["username"]);
$email = trim($_POST["email"]);
$password = $_POST["password"];
// 2. this chek if the imput is empty
 if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";}
        // 3. this step is to validate the email format
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";}
        // 4 we write this in order to check email length
         elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";}
        // 5 and this one also for the length of the user name 
        elseif (strlen($username) < 3) {
        $error = "Username must be at least 3 characters.";}
         else {
        // this one is for  Password Hashing my therd component
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);}

        // now we gonna move to : Database Insert
         try {

            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) 
                                   VALUES (?, ?, ?)
            ");

            $stmt->execute([$username, $email, $hashed_password]);

            $success = "Account created successfully! You can now log in.";

        } catch (PDOException $e) {

            if (str_contains($e->getMessage(), 'username')) {
                $error = "This username is already taken.";
            } else {
                $error = "This email is already registered.";
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Prompt Repository</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">

    <h1>Create an Account</h1>
    <p>Join the Prompt Repository</p>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
            <a href="login.php">Login here</a>
        </div>
    <?php endif; ?>

    <form method="POST" action="">

        <div class="form-group">
            <label for="username">Username</label>
            <input 
                type="text" 
                id="username"
                name="username" 
                placeholder="Enter your username"
                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                required
            >
        </div>

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
                placeholder="Minimum 6 characters"
                required
            >
        </div>

        <button type="submit">Create Account</button>

    </form>

    <p class="redirect-link">
        Already have an account? 
        <a href="login.php">Login here</a>
    </p>

</div>

</body>
</html>
        
    

