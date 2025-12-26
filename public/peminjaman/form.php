<?php
require_once __DIR__ . '/../../config/auth.php';
require_login();

require_once __DIR__ . '/../../config/config.php';
$pdo = db();

/* Ambil list fasilitas untuk dropdown */
$fasilitas = $pdo->query("SELECT id,nama,stok FROM fasilitas ORDER BY nama ASC")->fetchAll();

/* Data awal form */
$id = (int)($_GET['id'] ?? 0);
$data = [
  'warga_nama'      => '',
  'warga_hp'        => '',
  'fasilitas_id'    => '',
  'tanggal_pinjam'  => '',
  'tanggal_kembali' => '',
  'status'          => 'baru',
];
$editing = false;
$old      = null;

/* Kalau edit, ambil data lama peminjaman */
if ($id) {
  $st = $pdo->prepare("SELECT * FROM peminjaman WHERE id=?");
  $st->execute([$id]);
  if ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $data    = $row;
    $editing = true;
    $old     = $row;
  }
}

/* Helper: hitung jumlah pinjaman aktif yang overlap tanggalnya */
function count_overlap_active($pdo, $fasilitas_id, $mulai, $selesai, $exclude_id = 0) {
  $sql = "SELECT COUNT(*) AS jml
          FROM peminjaman
          WHERE fasilitas_id = ?
            AND status = 'dipinjam'
            AND NOT (tanggal_kembali < ? OR tanggal_pinjam > ?)";
  $params = [$fasilitas_id, $mulai, $selesai];
  if ($exclude_id) {
    $sql .= " AND id <> ?";
    $params[] = $exclude_id;
  }
  $st = $pdo->prepare($sql);
  $st->execute($params);
  return (int)($st->fetch()['jml'] ?? 0);
}

/* Proses submit form */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id              = (int)($_POST['id'] ?? 0);
  $warga_nama      = trim($_POST['warga_nama'] ?? '');
  $warga_hp        = trim($_POST['warga_hp'] ?? '');
  $fasilitas_id    = (int)($_POST['fasilitas_id'] ?? 0);
  $tanggal_pinjam  = $_POST['tanggal_pinjam'] ?? '';
  $tanggal_kembali = $_POST['tanggal_kembali'] ?? '';
  $status          = $_POST['status'] ?? 'baru';
  $err             = '';

  if ($warga_nama === '' || !$fasilitas_id || $tanggal_pinjam === '' || $tanggal_kembali === '') {
    $err = "Nama peminjam, fasilitas, dan tanggal wajib diisi.";
  } else {
    if ($status === 'dipinjam') {
      $st = $pdo->prepare("SELECT stok FROM fasilitas WHERE id=?");
      $st->execute([$fasilitas_id]);
      $stok = (int)($st->fetch()['stok'] ?? 0);

      $aktif = count_overlap_active($pdo, $fasilitas_id, $tanggal_pinjam, $tanggal_kembali, $id);

      if ($aktif >= $stok) {
        $err = "Stok tidak mencukupi pada rentang tanggal tersebut. Tersedia: " .
               max(0, $stok - $aktif) . " dari $stok.";
      }
    }

    if ($err === '') {
      try {
        $pdo->beginTransaction();

        if ($editing) {
          $old_fasid  = (int)$old['fasilitas_id'];
          $old_status = $old['status'];

          if ($old_status === 'dipinjam' && ($status !== 'dipinjam' || $old_fasid !== $fasilitas_id)) {
            $st = $pdo->prepare("UPDATE fasilitas SET stok = stok + 1 WHERE id=?");
            $st->execute([$old_fasid]);
          }

          if ($status === 'dipinjam' && ($old_status !== 'dipinjam' || $old_fasid !== $fasilitas_id)) {
            $st = $pdo->prepare("SELECT stok FROM fasilitas WHERE id=? FOR UPDATE");
            $st->execute([$fasilitas_id]);
            $stok_now = (int)($st->fetch()['stok'] ?? 0);
            if ($stok_now <= 0) {
              throw new Exception("Stok fasilitas habis.");
            }
            $st = $pdo->prepare("UPDATE fasilitas SET stok = stok - 1 WHERE id=?");
            $st->execute([$fasilitas_id]);
          }

          $st = $pdo->prepare("UPDATE peminjaman
            SET warga_nama=?, warga_hp=?, fasilitas_id=?, tanggal_pinjam=?, tanggal_kembali=?, status=?
            WHERE id=?");
          $st->execute([
            $warga_nama,
            $warga_hp,
            $fasilitas_id,
            $tanggal_pinjam,
            $tanggal_kembali,
            $status,
            $id
          ]);
        } else {
          if ($status === 'dipinjam') {
            $st = $pdo->prepare("SELECT stok FROM fasilitas WHERE id=? FOR UPDATE");
            $st->execute([$fasilitas_id]);
            $stok_now = (int)($st->fetch()['stok'] ?? 0);
            if ($stok_now <= 0) {
              throw new Exception("Stok fasilitas habis.");
            }
            $st = $pdo->prepare("UPDATE fasilitas SET stok = stok - 1 WHERE id=?");
            $st->execute([$fasilitas_id]);
          }

          $st = $pdo->prepare("INSERT INTO peminjaman
            (warga_nama,warga_hp,fasilitas_id,tanggal_pinjam,tanggal_kembali,status)
            VALUES (?,?,?,?,?,?)");
          $st->execute([
            $warga_nama,
            $warga_hp,
            $fasilitas_id,
            $tanggal_pinjam,
            $tanggal_kembali,
            $status
          ]);
        }

        $pdo->commit();
        redirect('index.php');
      } catch (Exception $e) {
        if ($pdo->inTransaction()) {
          $pdo->rollBack();
        }
        $err = $e->getMessage();
      }
    }
  }

  if ($err !== '') {
    $data = [
      'id'              => $id,
      'warga_nama'      => $warga_nama,
      'warga_hp'        => $warga_hp,
      'fasilitas_id'    => $fasilitas_id,
      'tanggal_pinjam'  => $tanggal_pinjam,
      'tanggal_kembali' => $tanggal_kembali,
      'status'          => $status,
    ];
    $editing = (bool)$id;
  }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title><?= $editing ? 'Edit' : 'Tambah' ?> Peminjaman</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a href="index.php" class="navbar-brand fw-bold">Peminjaman</a>
    <a href="../logout.php" class="btn btn-sm btn-light">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>
  </div>
</nav>

<div class="container my-4">

  <nav class="mb-3 small">
    <a href="index.php" class="text-decoration-none">Peminjaman</a> /
    <span class="text-muted"><?= $editing ? 'Edit' : 'Tambah' ?></span>
  </nav>

  <div class="row justify-content-center">
    <div class="col-lg-8">

      <div class="card shadow-sm">
        <div class="card-body">

          <h4 class="fw-bold mb-3">
            <?= $editing ? 'Edit' : 'Tambah' ?> Peminjaman
          </h4>

          <?php if (!empty($err)): ?>
            <div class="alert alert-danger">
              <i class="bi bi-exclamation-triangle"></i>
              <?= h($err) ?>
            </div>
          <?php endif; ?>

          <form method="post">
            <input type="hidden" name="id" value="<?= (int)($data['id'] ?? 0) ?>">

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Nama Peminjam</label>
                <input class="form-control" name="warga_nama"
                       value="<?= h($data['warga_nama']) ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">No. HP</label>
                <input class="form-control" name="warga_hp"
                       value="<?= h($data['warga_hp']) ?>">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Fasilitas</label>
                <select class="form-select" name="fasilitas_id" required>
                  <option value="">-- pilih --</option>
                  <?php foreach ($fasilitas as $f): ?>
                    <option value="<?= (int)$f['id'] ?>"
                      <?= (int)$data['fasilitas_id'] === (int)$f['id'] ? 'selected' : '' ?>>
                      <?= h($f['nama']) ?> (stok: <?= (int)$f['stok'] ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                  <option value="baru" <?= $data['status'] === 'baru' ? 'selected' : '' ?>>Baru</option>
                  <option value="dipinjam" <?= $data['status'] === 'dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                  <option value="dikembalikan" <?= $data['status'] === 'dikembalikan' ? 'selected' : '' ?>>Dikembalikan</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Pinjam</label>
                <input type="date" class="form-control" name="tanggal_pinjam"
                       value="<?= h($data['tanggal_pinjam']) ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Kembali</label>
                <input type="date" class="form-control" name="tanggal_kembali"
                       value="<?= h($data['tanggal_kembali']) ?>" required>
              </div>
            </div>

            <div class="d-flex gap-2 mt-3 flex-wrap">
              <button class="btn btn-primary">
                <i class="bi bi-save"></i> Simpan
              </button>
              <a href="index.php" class="btn btn-secondary">Batal</a>

              <?php if ($editing): ?>
                <a href="hapus.php?id=<?= (int)$data['id'] ?>"
                   onclick="return confirm('Hapus data peminjaman ini?')"
                   class="btn btn-danger ms-auto">
                  <i class="bi bi-trash"></i> Hapus
                </a>
              <?php endif; ?>
            </div>

          </form>

        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
