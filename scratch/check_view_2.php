<?php
require_once 'php_orri_laguntzaileak/DB_konexioa.php';
$stmt = $pdo->query("SHOW CREATE VIEW V_Langile_Pazienteak");
$row = $stmt->fetch();
print_r($row);
?>
