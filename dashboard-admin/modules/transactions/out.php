<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = (int)$_POST['item_id'];
    $quantity = (int)$_POST['quantity'];
    $notes = sanitizeInput($_POST['notes']);
    
    try {
        $pdo->beginTransaction();
        
        // Cek stok cukup
        $stmt = $pdo->prepare("SELECT stock FROM items WHERE id = ? FOR UPDATE");
        $stmt->execute([$item_id]);
        $current_stock = $stmt->fetchColumn();
        
        if ($current_stock < $quantity) {
            throw new Exception("Stok tidak mencukupi. Stok tersedia: $current_stock");
        }
        
        // Update stok
        $stmt = $pdo->prepare("UPDATE items SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$quantity, $item_id]);
        
        // Catat transaksi
        $stmt = $pdo->prepare("INSERT INTO transactions 
            (item_id, user_id, type, quantity, notes)
            VALUES (?, ?, 'keluar', ?, ?)");
        $stmt->execute([
            $item_id,
            $_SESSION['user_id'],
            $quantity,
            $notes
        ]);
        
        $pdo->commit();
        logActivity("Barang keluar: Item ID $item_id, Jumlah $quantity");
        header("Location: out.php?success=1");
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}

$items = $pdo->query("SELECT id, name, stock FROM items")->fetchAll();
?>

<div class="container-fluid">
    <h2 class="mb-4">Barang Keluar</h2>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Pilih Barang</label>
                        <select name="item_id" class="form-select" required>
                            <?php foreach ($items as $item): ?>
                            <option value="<?= $item['id'] ?>" data-stock="<?= $item['stock'] ?>">
                                <?= $item['name'] ?> (Stok: <?= $item['stock'] ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Jumlah</label>
                        <input type="number" name="quantity" 
                               class="form-control" min="1" required
                               max="<?= $items[0]['stock'] ?? 0 ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label>Keterangan</label>
                    <textarea name="notes" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-danger">Simpan Transaksi Keluar</button>
            </form>
        </div>
    </div>
</div>

<script>
// Update max quantity when item selection changes
document.querySelector('select[name="item_id"]').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const maxStock = selectedOption.dataset.stock;
    document.querySelector('input[name="quantity"]').max = maxStock;
});
</script>