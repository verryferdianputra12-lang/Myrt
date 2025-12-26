<?php
require_once __DIR__.'/../../config/config.php';
$pdo = db();

$id   = (int)($_GET['id'] ?? 0);
$data = ['nama' => '', 'lokasi' => '', 'stok' => 0, 'keterangan' => ''];
$editing = false;

if ($id) {
  $st = $pdo->prepare("SELECT * FROM fasilitas WHERE id=?");
  $st->execute([$id]);
  if ($r = $st->fetch()) {
    $data = $r;
    $editing = true;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id         = (int)($_POST['id'] ?? 0);
  $nama       = trim($_POST['nama'] ?? '');
  $lokasi     = trim($_POST['lokasi'] ?? '');
  $stok       = (int)($_POST['stok'] ?? 0);
  $keterangan = trim($_POST['keterangan'] ?? '');

  if ($nama === '') {
    $err = 'Nama fasilitas wajib diisi';
  } else {
    if ($id) {
      $st = $pdo->prepare("
        UPDATE fasilitas 
        SET nama=?, lokasi=?, stok=?, keterangan=? 
        WHERE id=?
      ");
      $st->execute([$nama, $lokasi, $stok, $keterangan, $id]);
    } else {
      $st = $pdo->prepare("
        INSERT INTO fasilitas (nama, lokasi, stok, keterangan) 
        VALUES (?,?,?,?)
      ");
      $st->execute([$nama, $lokasi, $stok, $keterangan]);
    }
    redirect('index.php');
  }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title><?= $editing ? 'Edit' : 'Tambah' ?> Fasilitas</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-primary shadow-sm">
  <div class="container">
    <span class="navbar-brand fw-bold">
      <?= $editing ? 'Edit' : 'Tambah' ?> Fasilitas
    </span>
    <a href="index.php" class="btn btn-sm btn-light">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>
</nav>

<div class="container my-4">

  <div class="card shadow-sm">
    <div class="card-body">

      <?php if (!empty($err)): ?>
        <div class="alert alert-danger">
          <?= h($err) ?>
        </div>
      <?php endif; ?>

      <form method="post">
        <input type="hidden" name="id" value="<?= (int)($data['id'] ?? 0) ?>">

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nama Fasilitas</label>
            <input class="form-control" name="nama"
                   value="<?= h($data['nama']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Lokasi Penyimpanan</label>
            <input class="form-control" name="lokasi"
                   value="<?= h($data['lokasi']) ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">Stok</label>
            <input class="form-control" type="number" name="stok"
                   min="0" value="<?= (int)$data['stok'] ?>">
          </div>

          <div class="col-md-8">
            <label class="form-label">Keterangan</label>
            <textarea class="form-control" name="keterangan" rows="3"><?= h($data['keterangan'] ?? '') ?></textarea>
          </div>
        </div>

        <div class="d-flex gap-2 mt-4 flex-wrap">
          <button class="btn btn-primary">
            <i class="bi bi-save"></i> Simpan
          </button>

          <a href="index.php" class="btn btn-secondary">
            Batal
          </a>

          <?php if ($editing): ?>
            <a href="hapus.php?id=<?= (int)$data['id'] ?>"
               onclick="return confirm('Hapus fasilitas ini?')"
               class="btn btn-danger ms-auto">
              <i class="bi bi-trash"></i> Hapus
            </a>
          <?php endif; ?>
        </div>

      </form>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
