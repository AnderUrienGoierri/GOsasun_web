<?php
$bide_absolutua = '../'; session_start();
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_izena'] !== 'Pazientea') {
    header("Location: ../php_hasiera/login.php");
    exit;
}

require_once '../php_laguntzaileak/DB_konexioa.php';

$paziente_id = $_SESSION['erabiltzaile_id'];
$mezua = '';
$errorea = '';

<<<<<<< Updated upstream:php_pazientea/neurketak.php
// Lortu neurketen historia (pultsua eta altuera barne)
$stmtjarraipenak = $pdo->prepare("SELECT erregistro_data, tentsio_sistolikoa, tentsio_diastolikoa, pisua_kg, altuera, pultsua_ppm, oharrak FROM jarraipenak WHERE paziente_id = ? ORDER BY erregistro_data DESC");
$stmtjarraipenak->execute([$paziente_id]);
$jarraipenak = $stmtjarraipenak->fetchAll(PDO::FETCH_ASSOC);

$orri_izenburua = "Nire jarraipenak - GOsasun";
$uneko_orria = "jarraipenak";
$css_pertsonalizatua = "pazienteak.css";
=======
// Kudeatu dokumentuaren edizioa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_dok') {
    $dok_id = intval($_POST['dok_id']);
    $titulua = $_POST['dokumentu_izena'] ?? '';
    $desk = $_POST['deskribapena'] ?? '';
    
    try {
        $stmtCheck = $pdo->prepare("SELECT id, bidea_zerbitzarian FROM dokumentuak WHERE id = ? AND paziente_id = ?");
        $stmtCheck->execute([$dok_id, $paziente_id]);
        $dok_zaharra = $stmtCheck->fetch();
        
        if ($dok_zaharra) {
            $bide_berria = $dok_zaharra['bidea_zerbitzarian'];
            
            if (isset($_FILES['pdf_berria']) && $_FILES['pdf_berria']['error'] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['pdf_berria']['tmp_name'];
                $ext = strtolower(pathinfo($_FILES['pdf_berria']['name'], PATHINFO_EXTENSION));
                
                if ($ext === 'pdf') {
                    $pdf_dir = '../paziente_dokumentuak/';
                    $data = date('Ymd');
                    $ordua = date('His');
                    $garbi_titulua = preg_replace('/[^a-zA-Z0-9._-]/', '_', $titulua);
                    $fitx_izena_berria = "dok_paziente_{$paziente_id}_{$data}_{$ordua}_{$garbi_titulua}.pdf";
                    
                    if (move_uploaded_file($tmp_name, $pdf_dir . $fitx_izena_berria)) {
                        if (file_exists('../' . $dok_zaharra['bidea_zerbitzarian'])) {
                            unlink('../' . $dok_zaharra['bidea_zerbitzarian']);
                        }
                        $bide_berria = 'paziente_dokumentuak/' . $fitx_izena_berria;
                    }
                }
            }
            
            $stmtUpdate = $pdo->prepare("UPDATE dokumentuak SET dokumentu_izena = ?, deskribapena = ?, bidea_zerbitzarian = ? WHERE id = ?");
            $stmtUpdate->execute([$titulua, $desk, $bide_berria, $dok_id]);
            $mezua = "Dokumentua ondo eguneratu da.";
        }
    } catch (PDOException $e) {
        $errorea = "Errorea eguneratzean: " . $e->getMessage();
    }
}

// Lortu neurketen historia (pultsua eta altuera barne) + dokumentuak
$stmtjarraipenak = $pdo->prepare("SELECT j.*, d.id as dok_id, d.dokumentu_izena, d.deskribapena 
                                 FROM jarraipenak j 
                                 LEFT JOIN dokumentuak d ON j.id = d.jarraipena_id 
                                 WHERE j.paziente_id = ? 
                                 ORDER BY j.erregistro_data DESC");
$stmtjarraipenak->execute([$paziente_id]);
$jarraipenak = $stmtjarraipenak->fetchAll(PDO::FETCH_ASSOC);

$orri_izenburua = "Jarraipenen historiala - GOsasun";
$uneko_orria = "jarraipenak";
$css_pertsonalizatua = "pazienteak.css,dokumentuak.css";
$js_gehigarria = ["jarraipenak.js"];
>>>>>>> Stashed changes:php_pazientea/jarraipenak.php

include_once '../php_includeak/paziente_goiburua.php';
?>

<body data-paziente-id="<?php echo htmlspecialchars($paziente_id); ?>">
    <main class="panel-nagusia">
        <div class="orri-goiburua">
<<<<<<< Updated upstream:php_pazientea/neurketak.php
            <h2><img src="../img/svg/clipboard-pen.svg" alt="" class="ikono-ertaina marjina-esk-5"> Neurketen Historiala</h2>
            <p>Hemen zure osasun datuen jarraipena ikusi dezakezu. jarraipenak kanpoko gailu bidez inportatzen dira.</p>
        </div>

        <div class="txartel-klinikoa">
            <h3><img src="../img/svg/line-chart.svg" alt="" class="ikono-1_5rem marjina-esk-10"> Azken jarraipenak</h3>
            
            <?php if (count($jarraipenak) > 0): ?>
                <div class="korritze-horizontala">
                    <table class="jarraipen-taula">
                        <thead>
                            <tr>
                                <th class="kurtsore-erakuslea" onclick="ordenatuTaula(0)"><?php echo $itzulpenak->dashboard_pazientea->data_column; ?> <img src="../img/svg/sort.svg" alt="" class="ikono-txikia-gardena"></th>
                                <th class="kurtsore-erakuslea" onclick="ordenatuTaula(1)"><?php echo $itzulpenak->dashboard_pazientea->ordua_column; ?> <img src="../img/svg/sort.svg" alt="" class="ikono-txikia-gardena"></th>
                                <th class="kurtsore-erakuslea" onclick="ordenatuTaula(2)"><?php echo $itzulpenak->dashboard_pazientea->tentsio_sistolikoa; ?> <img src="../img/svg/sort.svg" alt="" class="ikono-txikia-gardena"></th>
                                <th class="kurtsore-erakuslea" onclick="ordenatuTaula(3)"><?php echo $itzulpenak->dashboard_pazientea->tentsio_diastolikoa; ?> <img src="../img/svg/sort.svg" alt="" class="ikono-txikia-gardena"></th>
                                <th class="kurtsore-erakuslea" onclick="ordenatuTaula(4)"><?php echo $itzulpenak->dashboard_pazientea->pultsua; ?> <img src="../img/svg/sort.svg" alt="" class="ikono-txikia-gardena"></th>
                                <th class="kurtsore-erakuslea" onclick="ordenatuTaula(5)"><?php echo $itzulpenak->dashboard_pazientea->altuera; ?> <img src="../img/svg/sort.svg" alt="" class="ikono-txikia-gardena"></th>
                                <th class="kurtsore-erakuslea" onclick="ordenatuTaula(6)"><?php echo $itzulpenak->dashboard_pazientea->pisua; ?> <img src="../img/svg/sort.svg" alt="" class="ikono-txikia-gardena"></th>
                                <th><?php echo $itzulpenak->dashboard_pazientea->oharrak; ?></th>
                                <th>Ekintzak</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jarraipenak as $n): ?>
                                <tr>
<<<<<<< Updated upstream:php_pazientea/neurketak.php
                                    <td><strong><?php echo date('Y/m/d', strtotime($n['erregistro_data'])); ?></strong><br><small><?php echo date('H:i', strtotime($n['erregistro_data'])); ?></small></td>
                                    <td><?php echo ($n['tentsio_sistolikoa'] && $n['tentsio_diastolikoa']) ? $n['tentsio_sistolikoa'] . '/' . $n['tentsio_diastolikoa'] : '-'; ?></td>
                                    <td><?php echo $n['pultsua_ppm'] ? $n['pultsua_ppm'] . ' ppm' : '-'; ?></td>
                                    <td><?php echo $n['altuera'] ? $n['altuera'] . ' cm' : '-'; ?></td>
                                    <td><?php echo $n['pisua_kg'] ? $n['pisua_kg'] . ' kg' : '-'; ?></td>
                                    <td class="testu-gris-iluna"><?php echo htmlspecialchars($n['oharrak'] ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="zerrenda-hutsa">
                    <img src="../img/svg/clipboard-list.svg" alt="" class="ikono-handia-48 marjina-behe-15 opazitatea-50">
                    <p>Oraindik ez duzu neurketarik erregistratuta.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="marjina-goi-30 testua-erdian">
            <a href="index.php" class="botoia botoi-ertza">Itzuli Panelera</a>
        </div>
    </main>

    <!-- Edit Modal for Documents -->
    <div id="modalEditatuDok" class="modal">
        <div class="modal-content">
            <span class="itxi-modal" onclick="itxiModalDok()">&times;</span>
            <h3>Editatu Dokumentua</h3>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_dok">
                <input type="hidden" name="dok_id" id="edit_dok_id">
                <div class="marjina-behe-15">
                    <label>Dokumentuaren izena:</label>
                    <input type="text" name="dokumentu_izena" id="edit_izenburua" required class="inprimaki-kontrola">
                </div>
                <div class="marjina-behe-15">
                    <label>Deskribapena:</label>
                    <textarea name="deskribapena" id="edit_deskribapena" rows="3" class="inprimaki-kontrola"></textarea>
                </div>
                <div class="marjina-behe-20">
                    <label>Ordeztu fitxategia (PDF bakar bat):</label>
                    <input type="file" name="pdf_berria" accept="application/pdf" class="inprimaki-kontrola">
                </div>
                <div class="botoi-taldea">
                    <button type="button" class="botoia botoi-ertza" onclick="itxiModalDok()">Utzi</button>
                    <button type="submit" class="botoia">Gorde aldaketak</button>
                </div>
            </form>
        </div>
    </div>

<?php include_once '../php_includeak/paziente_footer.php'; ?>
