<?php
require_once __DIR__ . '/../../config/auth.php';
require_login();

require_once __DIR__ . '/../../config/config.php';
$pdo = db();

/* Filter status */
$status_filter = $_GET['status'] ?? 'semua';
$params = [];
$where  = '';

if (in_array($status_filter, ['baru','dipinjam','dikembalikan'], true)) {
    $where  = "WHERE p.status = ?";
    $params[] = $status_filter;
}

/* Ambil data */
$sql = "SELECT p.*, f.nama AS fasilitas
        FROM peminjaman p
        JOIN fasilitas f ON f.id = p.fasilitas_id
        $where
        ORDER BY p.id DESC";
$st = $pdo->prepare($sql);
$st->execute($params);
$rows = $st->fetchAll();

/* Hitung pengajuan baru */
$count_baru = (int)$pdo
    ->query("SELECT COUNT(*) FROM peminjaman WHERE status='baru'")
    ->fetchColumn();

function badge_class($s){
  return match($s){
    'baru'         => 'bg-warning text-dark',
    'dipinjam'     => 'bg-success',
    'dikembalikan' => 'bg-secondary',
    default        => 'bg-light text-dark'
  };
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Dashboard Peminjaman</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-primary shadow-sm">
  <div class="container">
    <span class="navbar-brand fw-bold">Dashboard Peminjaman</span>
    <a href="../logout.php" class="btn btn-sm btn-light">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>
  </div>
</nav>

<div class="container my-4">

  <!-- TOP ACTION BAR -->
  <div class="d-flex flex-wrap gap-2 align-items-center mb-3">

    <a href="../fasilitas/index.php" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-box"></i> Fasilitas
    </a>

    <!-- INFO PENGAJUAN BARU (BUKAN TOMBOL TAMBAH) -->
    <?php if ($count_baru > 0): ?>
      <span class="badge bg-warning text-dark">
        <i class="bi bi-bell"></i>
        Pengajuan Baru: <?= $count_baru ?>
      </span>
    <?php else: ?>
      <span class="badge bg-light text-dark">
        Tidak ada pengajuan baru
      </span>
    <?php endif; ?>

    <!-- FILTER -->
    <form method="get" class="ms-auto d-flex gap-2 align-items-center">
      <label class="small text-muted">Status</label>
      <select name="status" class="form-select form-select-sm" style="width:auto">
        <option value="semua"        <?= $status_filter==='semua'?'selected':'' ?>>Semua</option>
        <option value="baru"         <?= $status_filter==='baru'?'selected':'' ?>>Baru</option>
        <option value="dipinjam"     <?= $status_filter==='dipinjam'?'selected':'' ?>>Dipinjam</option>
        <option value="dikembalikan" <?= $status_filter==='dikembalikan'?'selected':'' ?>>Dikembalikan</option>
      </select>
      <button class="btn btn-outline-secondary btn-sm">Terapkan</button>
    </form>
  </div>

  <!-- TABLE -->
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th width="50">#</th>
            <th>Peminjam</th>
            <th>HP</th>
            <th>Fasilitas</th>
            <th>Mulai</th>
            <th>Selesai</th>
            <th>Status</th>
            <th width="140">Aksi</th>
          </tr>
        </thead>
        <tbody>

        <?php if (!$rows): ?>
          <tr>
            <td colspan="8" class="text-center text-muted py-4">
              Belum ada data peminjaman
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($rows as $i => $r): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= h($r['warga_nama']) ?></td>
              <td><?= h($r['warga_hp']) ?></td>
              <td><?= h($r['fasilitas']) ?></td>
              <td><?= h($r['tanggal_pinjam']) ?></td>
              <td><?= h($r['tanggal_kembali']) ?></td>
              <td>
                <span class="badge <?= badge_class($r['status']) ?>">
                  <?= ucfirst($r['status']) ?>
                </span>
              </td>
              <td>
                <a href="form.php?id=<?= (int)$r['id'] ?>"
                   class="btn btn-warning btn-sm"
                   title="Edit / Proses">
                  <i class="bi bi-pencil"></i>
                </a>
                <a href="hapus.php?id=<?= (int)$r['id'] ?>"
                   onclick="return confirm('Hapus data peminjaman ini?')"
                   class="btn btn-danger btn-sm"
                   title="Hapus">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>

        </tbody>
      </table>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
