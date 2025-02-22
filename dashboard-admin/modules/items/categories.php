<?php
require_once '../../includes/auth-check.php';
checkRole(['admin']);

// CRUD Kategori
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$name]);
    logActivity("Menambahkan kategori: $name");
    header("Location: categories.php");
    exit();
}

// Hapus Kategori
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    logActivity("Menghapus kategori ID: $id");
    header("Location: categories.php");
    exit();
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2>Manajemen Kategori</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-plus me-2"></i>Tambah Kategori
        </button>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama Kategori</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
            <tr>
                <td><?= $category['name'] ?></td>
                <td>
                    <a href="?delete=<?= $category['id'] ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Hapus kategori ini?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="addCategoryModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Kategori</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>