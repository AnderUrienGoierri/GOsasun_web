<?php
require_once 'php_orri_laguntzaileak/DB_konexioa.php';
$stmt = $pdo->query("SHOW CREATE VIEW V_Dokumentuak_Osoa");
$row = $stmt->fetch();
print_r($row);
?>
