<?php
require_once __DIR__ . '/../config/auth.php';

if (is_logged_in()) {
    header('Location: /rt_fasilitas_simple/public/peminjaman/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if (attempt_login($user, $pass)) {
        header('Location: /rt_fasilitas_simple/public/peminjaman/index.php');
        exit;
    } else {
        $err = 'Username atau password salah';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Login Pengurus RT</title>

  <!-- Bootstrap CSS -->
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

<!-- LOGIN CARD -->
<div class="container d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 56px);">

  <div class="col-11 col-sm-8 col-md-5 col-lg-4">
    <div class="card shadow">
      <div class="card-body">

        <h4 class="text-center fw-bold mb-4">
          Login Pengurus RT
        </h4>

        <?php if (!empty($err)): ?>
          <div class="alert alert-warning alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i>
            <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?>
            <button class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required autofocus>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button class="btn btn-primary w-100">
              <i class="bi bi-box-arrow-in-right"></i> Masuk
            </button>
          </div>

          <div class="text-center mt-3">
            <a href="index.php" class="text-decoration-none small">
              ← Kembali ke Beranda
            </a>
          </div>
        </form>

      </div>
    </div>
  </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
