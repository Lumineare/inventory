<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

// Filter parameters
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$type = $_GET['type'] ?? 'all';

// Query dasar
$query = "SELECT t.*, i.name AS item_name, u.username 
          FROM transactions t
          JOIN items i ON t.item_id = i.id
          JOIN users u ON t.user_id = u.id
          WHERE transaction_date BETWEEN ? AND ?";

$params = [$start_date, $end_date];

// Filter tipe transaksi
if ($type !== 'all') {
    $query .= " AND type = ?";
    $params[] = $type;
}

$query .= " ORDER BY transaction_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$transactions = $stmt->fetchAll();
?>

<div class="container-fluid">
    <h2 class="mb-4">Laporan Transaksi</h2>
    
    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="start_date" 
                           class="form-control" 
                           value="<?= $start_date ?>">
                </div>
                <div class="col-md-3">
                    <label>Tanggal Akhir</label>
                    <input type="date" name="end_date" 
                           class="form-control" 
                           value="<?= $end_date ?>">
                </div>
                <div class="col-md-3">
                    <label>Tipe Transaksi</label>
                    <select name="type" class="form-select">
                        <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>Semua Tipe</option>
                        <option value="masuk" <?= $type === 'masuk' ? 'selected' : '' ?>>Barang Masuk</option>
                        <option value="keluar" <?= $type === 'keluar' ? 'selected' : '' ?>>Barang Keluar</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Export Button -->
    <div class="mb-4">
        <a href="export/transactions.php?<?= $_SERVER['QUERY_STRING'] ?>" 
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
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>User</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $t): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($t['transaction_date'])) ?></td>
                            <td>
                                <span class="badge bg-<?= $t['type'] === 'masuk' ? 'success' : 'danger' ?>">
                                    <?= strtoupper($t['type']) ?>
                                </span>
                            </td>
                            <td><?= $t['item_name'] ?></td>
                            <td><?= $t['quantity'] ?></td>
                            <td><?= $t['username'] ?></td>
                            <td><?= $t['notes'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>