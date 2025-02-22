<?php
require_once '../../includes/auth-check.php';

// CRUD functionality for items
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    
    $stmt = $pdo->prepare("INSERT INTO items (name, category_id, stock, price) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $category, $stock, $price]);
}

// Get all items
$items = $pdo->query("
    SELECT items.*, categories.name AS category_name 
    FROM items
    LEFT JOIN categories ON items.category_id = categories.id
")->fetchAll();
?>

<!-- HTML form dan tabel untuk manajemen barang -->
<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb-4">
            <h2>Manajemen Barang</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="fas fa-plus me-2"></i>Tambah Barang
            </button>
        </div>

        <!-- Search Bar -->
        <div class="mb-4">
            <input type="text" class="form-control" placeholder="Cari barang...">
        </div>

        <!-- Items Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>BRG001</td>
                                <td>Baju Hitam</td>
                                <td>Pakaian</td>
                                <td><span class="badge bg-warning">15</span></td>
                                <td>Rp 150.000</td>
                                <td>
                                    <button class="btn btn-sm btn-warning me-2">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Barang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label>Nama Barang</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Kategori</label>
                        <select class="form-select">
                            <option>Pakaian</option>
                            <option>Elektronik</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Stok</label>
                            <input type="number" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Harga</label>
                            <input type="number" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = '';
$params = [];

if (!empty($search)) {
  $where = " WHERE items.name LIKE ?";
  $params[] = "%$search%";
}

// Get total items
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM items $where");
$totalStmt->execute($params);
$totalItems = $totalStmt->fetchColumn();
$totalPages = ceil($totalItems / $limit);

// Get items
$stmt = $pdo->prepare("
  SELECT items.*, categories.name AS category_name 
  FROM items 
  LEFT JOIN categories ON items.category_id = categories.id
  $where
  ORDER BY items.created_at DESC
  LIMIT $limit OFFSET $offset
");
$stmt->execute($params);
$items = $stmt->fetchAll();

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Validation
  $errors = [];
  $name = trim($_POST['name']);
  $category = (int)$_POST['category'];
  $stock = (int)$_POST['stock'];
  $price = (float)$_POST['price'];

  if (empty($name)) $errors['name'] = 'Nama barang harus diisi';
  if ($stock < 0) $errors['stock'] = 'Stok tidak valid';
  if ($price <= 0) $errors['price'] = 'Harga tidak valid';

  if (empty($errors)) {
    try {
      $stmt = $pdo->prepare("
        INSERT INTO items (name, category_id, stock, price)
        VALUES (?, ?, ?, ?)
      ");
      $stmt->execute([$name, $category, $stock, $price]);
      header("Location: manage.php?success=1");
      exit();
    } catch (PDOException $e) {
      $error = "Gagal menambahkan barang: " . $e->getMessage();
    }
  }
}
?>

<!-- HTML -->
<div class="main-content">
  <div class="container-fluid">
    <!-- Search Form -->
    <form method="get" class="mb-4">
      <div class="input-group">
        <input type="text" name="search" class="form-control" 
               placeholder="Cari barang..." value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-outline-secondary" type="submit">
          <i class="fas fa-search"></i>
        </button>
      </div>
    </form>

    <!-- Items Table -->
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover" id="itemsTable">
            <thead>
              <tr>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Harga</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $item): ?>
              <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= htmlspecialchars($item['category_name']) ?></td>
                <td>
                  <?php if($item['stock'] < 10): ?>
                    <span class="badge badge-low-stock"><?= $item['stock'] ?></span>
                  <?php else: ?>
                    <span class="badge badge-medium-stock"><?= $item['stock'] ?></span>
                  <?php endif; ?>
                </td>
                <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                <td>
                  <a href="edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i>
                  </a>
                  <button class="btn btn-sm btn-danger delete-btn" 
                          data-id="<?= $item['id'] ?>">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <nav>
          <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>">
                <?= $i ?>
              </a>
            </li>
            <?php endfor; ?>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</div>

<!-- Form Modal -->
<div class="modal fade" id="addItemModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" class="needs-validation" novalidate>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Barang Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Barang</label>
            <input type="text" name="name" class="form-control" required>
            <div class="form-error" id="nameError"></div>
          </div>
          <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="category" class="form-select" required>
              <?php 
              $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
              foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Stok</label>
              <input type="number" name="stock" class="form-control" required min="0">
              <div class="form-error" id="stockError"></div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Harga</label>
              <input type="number" name="price" class="form-control" required min="1000">
              <div class="form-error" id="priceError"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Delete Confirmation
document.querySelectorAll('.delete-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
      const itemId = this.dataset.id;
      fetch(`delete.php?id=${itemId}`, { method: 'POST' })
        .then(response => {
          if (response.ok) location.reload();
        });
    }
  });
});

// AJAX Search
document.getElementById('searchInput').addEventListener('input', function(e) {
  const searchTerm = e.target.value;
  const params = new URLSearchParams({
    search: searchTerm,
    ajax: 1
  });

  fetch(`api/items.php?${params}`)
    .then(response => response.json())
    .then(data => {
      updateTable(data.items);
      updatePagination(data.totalPages);
    });
});

function updateTable(items) {
  const tbody = document.querySelector('#itemsTable tbody');
  tbody.innerHTML = items.map(item => `
    <tr>
      <td>${item.name}</td>
      <td>${item.category_name}</td>
      <td>
        ${item.stock < 10 ? 
          `<span class="badge badge-low-stock">${item.stock}</span>` : 
          `<span class="badge badge-medium-stock">${item.stock}</span>`}
      </td>
      <td>Rp ${new Intl.NumberFormat().format(item.price)}</td>
      <td>
        <!-- Action buttons -->
      </td>
    </tr>
  `).join('');
}
</script>