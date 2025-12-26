<?php
require_once __DIR__ . '/../config/auth.php';
logout();
header('Location: /rt_fasilitas_simple/public/login.php');
exit;
