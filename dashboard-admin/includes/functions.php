<?php
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function logActivity($action) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO activity_logs 
        (user_id, action, ip_address, user_agent) 
        VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $action,
        $_SERVER['REMOTE_ADDR'],
        $_SERVER['HTTP_USER_AGENT']
    ]);
}

function checkLowStock() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM items WHERE stock < 10");
    return $stmt->fetchAll();
}