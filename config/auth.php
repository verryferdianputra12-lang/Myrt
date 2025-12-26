<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function require_login() {
    // GANTI 'rt_fasilitas_simple' kalau nama folder kamu beda
    if (!is_logged_in()) {
        header('Location: /rt_fasilitas_simple/public/login.php');
        exit;
    }
}

/**
 * attempt_login
 * - kalau tabel users kosong, otomatis bikin user default:
 *   username: rt, password: rt12345
 */
function attempt_login($username, $password) {
    $pdo = db();

    // seed user default jika belum ada user sama sekali
    $row = $pdo->query("SELECT COUNT(*) AS c FROM users")->fetch();
    if ((int)$row['c'] === 0) {
        $st = $pdo->prepare("INSERT INTO users(username, password_hash) VALUES(?, ?)");
        $st->execute(['rt', password_hash('rt12345', PASSWORD_BCRYPT)]);
    }

    $st = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username=? LIMIT 1");
    $st->execute([$username]);
    $u = $st->fetch();

    if ($u && password_verify($password, $u['password_hash'])) {
        $_SESSION['user'] = [
            'id'       => $u['id'],
            'username' => $u['username'],
        ];
        return true;
    }
    return false;
}

function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = [];
    session_destroy();
}
