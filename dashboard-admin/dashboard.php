<?php
require_once 'includes/header.php';

// Get statistics
$total_items = $pdo->query("SELECT COUNT(*) FROM items")->fetchColumn();
$total_transactions = $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
$low_stock = $pdo->query("SELECT COUNT(*) FROM items WHERE stock < 10")->fetchColumn();

// Recent transactions
$recent_transactions = $pdo->query("
    SELECT t.*, i.name AS item_name, u.username 
    FROM transactions t
    JOIN items i ON t.item_id = i.id
    JOIN users u ON t.user_id = u.id
    ORDER BY transaction_date DESC 
    LIMIT 5
")->fetchAll();

// Stock chart data
$stock_data = $pdo->query("
    SELECT categories.name AS category, SUM(items.stock) AS total 
    FROM items
    JOIN categories ON items.category_id = categories.id
    GROUP BY categories.name
")->fetchAll();
?>

<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Total Barang</h5>
                    <h1><?= $total_items ?></h1>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5>Stok Rendah</h5>
                    <h1><?= $low_stock ?></h1>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Total Transaksi</h5>
                    <h1><?= $total_transactions ?></h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Distribusi Stok per Kategori</h5>
                </div>
                <div class="card-body">
                    <canvas id="stockChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Aktivitas Transaksi Terakhir</h5>
                </div>
                <div class="card-body">
                    <canvas id="transactionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card">
        <div class="card-header">
            <h5>Transaksi Terakhir</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_transactions as $t): ?>
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
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Stock Chart
const stockCtx = document.getElementById('stockChart').getContext('2d');
new Chart(stockCtx, {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_column($stock_data, 'category')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($stock_data, 'total')) ?>,
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', 
                '#4BC0C0', '#9966FF', '#FF9F40'
            ]
        }]
    }
});

// Transaction Chart
const transCtx = document.getElementById('transactionChart').getContext('2d');
new Chart(transCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($recent_transactions, 'transaction_date')) ?>,
        datasets: [{
            label: 'Jumlah Transaksi',
            data: <?= json_encode(array_column($recent_transactions, 'quantity')) ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1,
            fill: true,
            tension: 0.1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>