<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            background: #2c3e50;
            color: white;
            position: fixed;
            width: 250px;
            transition: all 0.3s;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .nav-link {
            color: white !important;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            background: #34495e;
        }
        
        .dashboard-card {
            transition: transform 0.3s;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h3 class="text-center mb-4">Inventory System</h3>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="dashboard.php">
                    <i class="fas fa-home me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="items/manage.php">
                    <i class="fas fa-box me-2"></i> Manajemen Barang
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="transactions/in.php">
                    <i class="fas fa-arrow-down me-2"></i> Barang Masuk
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="transactions/out.php">
                    <i class="fas fa-arrow-up me-2"></i> Barang Keluar
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="users/manage.php">
                    <i class="fas fa-users me-2"></i> Manajemen Pengguna
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="reports/stock.php">
                    <i class="fas fa-chart-bar me-2"></i> Laporan
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="d-flex align-items-center">
                    <div class="dropdown me-3">
                        <a class="btn btn-link" href="#" role="button" id="notifDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="badge bg-danger">3</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Stok Baju Hitam rendah (5)</a>
                            <a class="dropdown-item" href="#">Transaksi besar terdeteksi</a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <a class="btn btn-link" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle fa-2x"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Profil</a>
                            <a class="dropdown-item" href="logout.php">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card dashboard-card bg-primary text-white">
                    <div class="card-body">
                        <h5>Total Barang</h5>
                        <h2>1,234</h2>
                        <small>Update 5 menit lalu</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card bg-warning text-dark">
                    <div class="card-body">
                        <h5>Stok Rendah</h5>
                        <h2>23</h2>
                        <small>Barang perlu restock</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card bg-success text-white">
                    <div class="card-body">
                        <h5>Transaksi Hari Ini</h5>
                        <h2>45</h2>
                        <small>Total: Rp 12.345.000</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Transaksi Terakhir</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Pengguna</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2023-10-05 14:30</td>
                                <td><span class="badge bg-success">Masuk</span></td>
                                <td>Baju Hitam</td>
                                <td>50</td>
                                <td>Admin</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Grafik Stok Barang</h5>
            </div>
            <div class="card-body">
                <div id="stockChart" style="height: 300px;"></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Contoh inisialisasi chart
        const ctx = document.getElementById('stockChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Baju', 'Celana', 'Aksesoris'],
                datasets: [{
                    label: 'Stok Tersedia',
                    data: [120, 80, 45],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)'
                    ]
                }]
            }
        });
    </script>
</body>
</html>