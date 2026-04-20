<?php
require_once 'php_orri_laguntzaileak/DB_konexioa.php';

// Bilatu 'C:\' edo 'C:/' duten bideak
$stmt = $pdo->query("SELECT id, bidea_zerbitzarian FROM dokumentuak WHERE bidea_zerbitzarian LIKE 'C:%'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Aurkitutako bide absolutuak: " . count($rows) . "\n";

foreach ($rows as $row) {
    $id = $row['id'];
    $bidea = $row['bidea_zerbitzarian'];
    
    // Bilatu 'paziente_dokumentuak' zatia eta hortik aurrera mantendu
    $pos = strpos($bidea, 'paziente_dokumentuak');
    if ($pos !== false) {
        $bide_berria = substr($bidea, $pos);
        // Aldatu \ -> /
        $bide_berria = str_replace('\\', '/', $bide_berria);
        
        echo "Eguneratzen ID $id: $bidea -> $bide_berria\n";
        $stmtUpd = $pdo->prepare("UPDATE dokumentuak SET bidea_zerbitzarian = ? WHERE id = ?");
        $stmtUpd->execute([$bide_berria, $id]);
    }
}

echo "Garbiketa bukatuta.\n";
?>
