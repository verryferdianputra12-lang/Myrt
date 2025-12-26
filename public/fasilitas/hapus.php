
<?php
require_once __DIR__.'/../../config/config.php'; $pdo=db();
$id=(int)($_GET['id']??0); if($id){ $st=$pdo->prepare("DELETE FROM fasilitas WHERE id=?"); $st->execute([$id]); }
redirect('index.php');
