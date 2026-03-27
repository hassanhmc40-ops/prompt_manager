<?php
require 'db.php';
session_start();

$error   = '';
$success = '';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
// Get the Prompt ID from the URL
?>