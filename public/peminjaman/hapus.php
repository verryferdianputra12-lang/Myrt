<?php
// Hanya pengurus RT yang sudah login yang boleh hapus
require_once __DIR__ . '/../../config/auth.php';
require_login();

// Koneksi database
require_once __DIR__ . '/../../config/config.php';
$pdo = db();

$id = (int)($_GET['id'] ?? 0);

if ($id) {
    try {
        $pdo->beginTransaction();

        // Ambil data peminjaman dulu (untuk balikin stok kalau perlu)
        $st = $pdo->prepare("
            SELECT fasilitas_id, status 
            FROM peminjaman 
            WHERE id = ? 
            FOR UPDATE
        ");
        $st->execute([$id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Kalau statusnya masih 'dipinjam', stok fasilitas harus dikembalikan
            if ($row['status'] === 'dipinjam') {
                $st2 = $pdo->prepare("
                    UPDATE fasilitas 
                    SET stok = stok + 1 
                    WHERE id = ?
                ");
                $st2->execute([(int)$row['fasilitas_id']]);
            }

            // Hapus record peminjaman
            $del = $pdo->prepare("DELETE FROM peminjaman WHERE id = ?");
            $del->execute([$id]);
        }

        $pdo->commit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Optional: bisa tulis ke log kalau mau debug
        // error_log($e->getMessage());
    }
}

// Balik lagi ke halaman daftar peminjaman
redirect('index.php');
