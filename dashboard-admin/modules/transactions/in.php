<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = (int)$_POST['item_id'];
    $quantity = (int)$_POST['quantity'];
    $notes = sanitizeInput($_POST['notes']);
    
    // Update stok
    $stmt = $pdo->prepare("UPDATE items SET stock = stock + ? WHERE id = ?");
    $stmt->execute([$quantity, $item_id]);
    
    // Catat transaksi
    $stmt = $pdo->prepare("INSERT INTO transactions 
        (item_id, user_id, type, quantity, notes)
        VALUES (?, ?, 'masuk', ?, ?)");
    $stmt->execute([
        $item_id,
        $_SESSION['user_id'],
        $quantity,
        $notes
    ]);
    
    logActivity("Barang masuk: Item ID $item_id, Jumlah $quantity");
    header("Location: in.php");
    exit();
}

$items = $pdo->query("SELECT id, name FROM items")->fetchAll();
?>

<div class="container-fluid">
    <h2 class="mb-4">Barang Masuk</h2>
    
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Pilih Barang</label>
                        <select name="item_id" class="form-select" required>
                            <?php foreach ($items as $item): ?>
                            <option value="<?= $item['id'] ?>"><?= $item['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Jumlah</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Keterangan</label>
                    <textarea name="notes" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
            </form>
        </div>
    </div>
</div>