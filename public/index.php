<?php
// Halaman ini public (tidak pakai require_login)
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>MY RT - Peminjaman Fasilitas</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/style.css">
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <span class="navbar-brand fw-bold">
      <i class="bi bi-building"></i> MY RT
    </span>
  </div>
</nav>

<!-- CONTENT -->
<div class="container my-5">

  <!-- HERO CARD -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h3 class="fw-bold mb-3">
        Peminjaman Fasilitas RT01 Pondok Cabe
      </h3>

      <p>
        Web peminjaman fasilitas <b>RT01 Pondok Cabe</b> ini digunakan untuk
        mencatat dan mengelola peminjaman fasilitas milik RT seperti
        <b>kursi, tenda, dan sound system</b> agar lebih rapi dan terkontrol.
      </p>

      <h5 class="mt-4 fw-semibold">Alur Peminjaman</h5>
      <ol class="mt-2">
        <li>Warga mengisi data diri di menu <b>Pendaftaran Peminjam</b>.</li>
        <li>Pengurus RT mencatat peminjaman berdasarkan data peminjam.</li>
        <li>Fasilitas dipinjam sesuai jadwal yang disepakati.</li>
        <li>Setelah selesai, fasilitas dikembalikan ke RT.</li>
      </ol>

      <div class="d-flex gap-2 flex-wrap mt-4">
        <a href="daftar_peminjam.php" class="btn btn-primary">
          <i class="bi bi-person-plus"></i> Pendaftaran Peminjam
        </a>
        <a href="login.php" class="btn btn-outline-secondary">
          <i class="bi bi-box-arrow-in-right"></i> Login Pengurus RT
        </a>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <div class="text-center text-muted small mt-5">
    © 2025 Verry Ferdian Putra
  </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
