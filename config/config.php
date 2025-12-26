
<?php
$DB_HOST='localhost'; $DB_NAME='rt_fasilitas_simple'; $DB_USER='root'; $DB_PASS='';
function db(){ static $pdo=null; if(!$pdo){ $pdo=new PDO("mysql:host={$GLOBALS['DB_HOST']};dbname={$GLOBALS['DB_NAME']};charset=utf8mb4",$GLOBALS['DB_USER'],$GLOBALS['DB_PASS'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]); } return $pdo; }
function h($s){ return htmlspecialchars($s??'', ENT_QUOTES, 'UTF-8'); }
function redirect($u){ header("Location: $u"); exit; }
