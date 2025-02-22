<?php
require_once '../../includes/auth-check.php';
checkRole(['admin']);

// CRUD functionality for users
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role]);
}

// Get all users
$users = $pdo->query("SELECT * FROM users")->fetchAll();
?>

<!-- HTML table untuk menampilkan dan mengelola pengguna -->