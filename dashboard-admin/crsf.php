// Contoh CSRF Protection
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Contoh validasi input
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Contoh prepared statement
$stmt = $pdo->prepare("INSERT INTO transactions (item_id, type, quantity) VALUES (:item_id, :type, :quantity)");
$stmt->execute([
    ':item_id' => $itemId,
    ':type' => $type,
    ':quantity' => $quantity
]);