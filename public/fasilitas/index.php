<?php
require_once __DIR__ . '/../../config/auth.php';
require_login();

require_once __DIR__ . '/../../config/config.php';
$pdo = db();

$sql  = "SELECT id, nama, lokasi, stok, keterangan
         FROM fasilitas
         ORDER BY id ASC";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Fasilitas</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-primary shadow-sm">
  <div class="container">
    <span class="navbar-brand fw-bold">Manajemen Fasilitas RT</span>
    <div class="d-flex gap-2">
      <a href="../peminjaman/index.php" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i> Peminjaman
      </a>
      <a href="../logout.php" class="btn btn-sm btn-outline-light">
        Logout
      </a>
    </div>
  </div>
</nav>

<div class="container my-4">

  <!-- HEADER ACTION -->
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="mb-0 fw-semibold">Daftar Fasilitas</h5>
    <a href="form.php" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Tambah Fasilitas
    </a>
  </div>

  <!-- TABLE -->
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:50px">#</th>
            <th>Nama</th>
            <th>Lokasi</th>
            <th style="width:90px">Stok</th>
            <th>Keterangan</th>
            <th style="width:160px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$rows): ?>
            <tr>
              <td colspan="6" class="text-center text-muted py-4">
                Belum ada data fasilitas
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($rows as $i => $r): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td class="fw-semibold"><?= h($r['nama']) ?></td>
                <td><?= h($r['lokasi'] ?: '-') ?></td>
                <td>
                  <span class="badge <?= $r['stok'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                    <?= (int)$r['stok'] ?>
                  </span>
                </td>
                <td><?= h($r['keterangan'] ?: '-') ?></td>
                <td>
                  <div class="d-flex gap-1">
                    <a href="form.php?id=<?= (int)$r['id'] ?>"
                       class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <a href="hapus.php?id=<?= (int)$r['id'] ?>"
                       onclick="return confirm('Hapus fasilitas ini?')"
                       class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="text-center text-muted small mt-4">
    © <?= date('Y') ?> Sistem Peminjaman Fasilitas RT
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
