<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

// Filter parameters
$category = isset($_GET['category']) ? (int)$_GET['category'] : null;
$stock_status = $_GET['stock_status'] ?? 'all';

// Query dasar
$query = "SELECT items.*, categories.name AS category_name 
          FROM items 
          LEFT JOIN categories ON items.category_id = categories.id";

$where = [];
$params = [];

// Filter kategori
if ($category) {
    $where[] = "category_id = ?";
    $params[] = $category;
}

// Filter stok
switch ($stock_status) {
    case 'low':
        $where[] = "stock < 10";
        break;
    case 'medium':
        $where[] = "stock BETWEEN 10 AND 50";
        break;
    case 'high':
        $where[] = "stock > 50";
        break;
}

// Gabungkan where clause
if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

// Eksekusi query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$items = $stmt->fetchAll();

// Get categories for filter dropdown
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<div class="container-fluid">
    <h2 class="mb-4">Laporan Stok Barang</h2>
    
    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label>Kategori</label>
                    <select name="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                            <?= $cat['name'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Status Stok</label>
                    <select name="stock_status" class="form-select">
                        <option value="all" <?= $stock_status === 'all' ? 'selected' : '' ?>>Semua Stok</option>
                        <option value="low" <?= $stock_status === 'low' ? 'selected' : '' ?>>Stok Rendah (<10)</option>
                        <option value="medium" <?= $stock_status === 'medium' ? 'selected' : '' ?>>Stok Menengah (10-50)</option>
                        <option value="high" <?= $stock_status === 'high' ? 'selected' : '' ?>>Stok Tinggi (>50)</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Export Button -->
    <div class="mb-4">
        <a href="export/stock.php?<?= $_SERVER['QUERY_STRING'] ?>" 
           class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i>Export ke Excel
        </a>
    </div>

    <!-- Results Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Harga</th>
                            <th>Lokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= $item['name'] ?></td>
                            <td><?= $item['category_name'] ?></td>
                            <td class="<?= $item['stock'] < 10 ? 'text-danger fw-bold' : '' ?>">
                                <?= $item['stock'] ?>
                            </td>
                            <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                            <td><?= $item['location'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>