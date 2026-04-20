<?php
require_once 'php_orri_laguntzaileak/DB_konexioa.php';
$stmt = $pdo->query("SELECT bidea_zerbitzarian FROM dokumentuak LIMIT 5");
$rows = $stmt->fetchAll();
print_r($rows);
?>
