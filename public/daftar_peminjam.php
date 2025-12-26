<?php
require_once __DIR__ . '/../config/config.php';
$pdo = db();

// Ambil daftar fasilitas untuk dropdown
$fasilitas = $pdo->query("SELECT id, nama, stok FROM fasilitas ORDER BY nama ASC")->fetchAll();

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = trim($_POST['nama'] ?? '');
    $hp      = trim($_POST['hp'] ?? '');
    $fasid   = (int)($_POST['fasilitas_id'] ?? 0);
    $mulai   = $_POST['tanggal_pinjam'] ?? '';
    $selesai = $_POST['tanggal_kembali'] ?? '';

    if ($nama === '' || !$fasid || $mulai === '' || $selesai === '') {
        $error = "Nama, fasilitas, dan tanggal wajib diisi.";
    } else {
        $st = $pdo->prepare("INSERT INTO peminjaman
            (warga_nama, warga_hp, fasilitas_id, tanggal_pinjam, tanggal_kembali, status)
            VALUES (?, ?, ?, ?, ?, 'baru')");
        $st->execute([$nama, $hp, $fasid, $mulai, $selesai]);

        $success = "Pengajuan peminjaman berhasil dikirim. Silakan menunggu konfirmasi dari pengurus RT.";
        $_POST = [];
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Pendaftaran Peminjaman Fasilitas</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/style.css">
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a href="index.php" class="navbar-brand fw-bold">
      <i class="bi bi-arrow-left"></i> MY RT
    </a>
  </div>
</nav>

<!-- CONTENT -->
<div class="container my-4">

  <nav class="mb-3 small">
    <a href="index.php" class="text-decoration-none">Home</a> /
    <span class="text-muted">Pendaftaran Peminjam</span>
  </nav>

  <div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">

      <div class="card shadow-sm">
        <div class="card-body">

          <h4 class="fw-bold mb-3 text-center">
            Pendaftaran Peminjaman Fasilitas RT
          </h4>

          <p class="text-muted small">
            Form ini digunakan untuk <b>mengajukan peminjaman fasilitas</b>.
            Permohonan Anda akan masuk dengan status <b>Baru</b> dan diproses oleh pengurus RT.
          </p>

          <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
              <i class="bi bi-check-circle"></i>
              <?= h($success) ?>
              <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
              <i class="bi bi-exclamation-triangle"></i>
              <?= h($error) ?>
              <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <form method="post">

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" name="nama" required
                       value="<?= h($_POST['nama'] ?? '') ?>">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">No. HP</label>
                <input type="text" class="form-control" name="hp"
                       value="<?= h($_POST['hp'] ?? '') ?>">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Fasilitas yang Dipinjam</label>
              <select class="form-select" name="fasilitas_id" required>
                <option value="">-- pilih fasilitas --</option>
                <?php foreach ($fasilitas as $f): ?>
                  <option value="<?= (int)$f['id'] ?>"
                    <?= (isset($_POST['fasilitas_id']) && (int)$_POST['fasilitas_id'] === (int)$f['id']) ? 'selected' : '' ?>>
                    <?= h($f['nama']) ?> (stok: <?= (int)$f['stok'] ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Mulai Pakai</label>
                <input type="date" class="form-control" name="tanggal_pinjam"
                       value="<?= h($_POST['tanggal_pinjam'] ?? '') ?>" required>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Selesai Pakai</label>
                <input type="date" class="form-control" name="tanggal_kembali"
                       value="<?= h($_POST['tanggal_kembali'] ?? '') ?>" required>
              </div>
            </div>

            <div class="d-flex gap-2 mt-3 flex-wrap">
              <button class="btn btn-primary">
                <i class="bi bi-send"></i> Kirim Pengajuan
              </button>
              <a href="index.php" class="btn btn-outline-secondary">
                Kembali
              </a>
            </div>

          </form>

        </div>
      </div>

    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
