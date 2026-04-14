<?php
$bide_absolutua = '../'; session_start();
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_izena'] !== 'Pazientea') {
    header("Location: ../php_orri_hasierakoak/login.php");
    exit;
}

require_once '../php_orri_laguntzaileak/DB_konexioa.php';

$paziente_id = $_SESSION['erabiltzaile_id'];
$mezua = '';
$errorea = '';

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

include_once '../php_orri_includeak/paziente_goiburua.php';
?>

<body data-paziente-id="<?php echo htmlspecialchars($paziente_id); ?>">
    <main class="panel-nagusia">
        <div class="orri-goiburua">
            <h2><img src="../img/svg/clipboard-pen.svg" alt="" class="ikono-ertaina marjina-esk-5"> Jarraipenen Historiala</h2>
            <p>Hemen zure osasun datuen jarraipena ikusi dezakezu. Jarraipen guztiak gailu bidez sinkronizatuta daude.</p>
        </div>

        <?php if ($mezua): ?><div class="alerta alerta-arrakasta"><?php echo $mezua; ?></div><?php endif; ?>
        <?php if ($errorea): ?><div class="alerta alerta-errorea"><?php echo $errorea; ?></div><?php endif; ?>

        <div class="txartel-klinikoa">
            <div class="flex-tartea-15 marjina-behe-20 flex-ebaki" >
                 <div class="izenburu-urdina flex-zentratua"><img src="../img/svg/line-chart.svg" alt="" class="ikono-1_5rem marjina-esk-10"> Azken Neurketak</div>
                 <div class="flex-taldea-10">
                    <input type="text" id="bilatuJarraipenak" class="inprimaki-kontrola" placeholder="Bilatu data edo oharren arabera..." onkeyup="bilatuTaulan()" style="max-width: 250px;">
                     <div class="testu-gris-txikia">
                        <label class="kurtsore-erakuslea">
                            <input type="checkbox" id="ikusiEzohikoak"> Ezohikoak soilik nabarmendu
                        </label>
                     </div>
                 </div>
            </div>
            
            <?php if (count($jarraipenak) > 0): ?>
                <div class="korritze-horizontala">
                    <table class="jarraipenen-taula" id="jarraipenenTaulaNagusia">
                        <thead>
                            <tr>
                                <th onclick="ordenatuTaula(0)" class="kurtsore-erakuslea">Data <img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"></th>
                                <th onclick="ordenatuTaula(1)" class="kurtsore-erakuslea">Ordua <img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"></th>
                                <th onclick="ordenatuTaula(2)" class="kurtsore-erakuslea">SIS <img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"></th>
                                <th onclick="ordenatuTaula(3)" class="kurtsore-erakuslea">DIA <img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"></th>
                                <th onclick="ordenatuTaula(4)" class="kurtsore-erakuslea">Pultsua <img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"></th>
                                <th onclick="ordenatuTaula(5)" class="kurtsore-erakuslea">Pisua <img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"></th>
                                <th onclick="ordenatuTaula(6)" class="kurtsore-erakuslea">Altuera <img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"></th>
                                <th>Oharrak</th>
                                <th>Ekintzak</th>
                            </tr>
                        </thead>
                        <tbody id="jarraipenakTableBody">
                            <?php foreach ($jarraipenak as $n): ?>
                                <tr data-sis="<?php echo $n['tentsio_sistolikoa']; ?>" data-dia="<?php echo $n['tentsio_diastolikoa']; ?>" data-pultsua="<?php echo $n['pultsua_ppm']; ?>">
                                    <td><strong><?php echo date('Y/m/d', strtotime($n['erregistro_data'])); ?></strong></td>
                                    <td><small><?php echo date('H:i', strtotime($n['erregistro_data'])); ?></small></td>
                                    <td><?php echo $n['tentsio_sistolikoa'] ?: '-'; ?></td>
                                    <td><?php echo $n['tentsio_diastolikoa'] ?: '-'; ?></td>
                                    <td><?php echo $n['pultsua_ppm'] ? $n['pultsua_ppm'] . ' ppm' : '-'; ?></td>
                                    <td><?php echo $n['pisua_kg'] ? $n['pisua_kg'] . ' kg' : '-'; ?></td>
                                    <td><?php echo $n['altuera'] ? $n['altuera'] . ' m' : '-'; ?></td>
                                    <td class="testu-gris-iluna"><?php echo htmlspecialchars($n['oharrak'] ?? '-'); ?></td>
                                    <td class="ekintza-botoiak testua-erdian">
                                        <!-- Editatu jarraipena -->
                                        <a href="javascript:void(0)" class="botoi-ikonoa" title="Editatu jarraipena" onclick="irekiEditatuModal(<?php echo $n['id']; ?>)">
                                            <img src="../img/svg/pencil.svg" alt="" class="ikono-txikia">
                                        </a>
                                        <!-- Dokumentuak ikusi -->
                                        <?php if ($n['dok_id']): ?>
                                            <a href="javascript:void(0)" class="botoi-ikonoa" title="Ikusi Dokumentuak" 
                                               onclick="irekiModalEditatuDok(<?php echo $n['dok_id']; ?>, '<?php echo addslashes($n['dokumentu_izena']); ?>', '<?php echo addslashes($n['deskribapena']); ?>')">
                                                <img src="../img/svg/search.svg" alt="" class="ikono-txikia">
                                            </a>
                                        <?php else: ?>
                                            <span class="opazitatea-20"><img src="../img/svg/search.svg" alt="" class="ikono-txikia"></span>
                                        <?php endif; ?>
                                    </td>
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
    <div id="modalEditatuDok" class="modala-inguratzailea">
        <div class="modala-edukia">
            <div class="modala-goiburua">
                <h3 id="modalIzenburua">Editatu Dokumentua</h3>
                <span class="itxi-modala kurtsore-erakuslea tamaina-1_5rem" onclick="itxiModalDok()">&times;</span>
            </div>
            <div class="padding-20">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit_dok">
                    <input type="hidden" name="dok_id" id="edit_dok_id">
                    
                    <div class="inprimaki-taldea">
                        <label>Dokumentuaren izena:</label>
                        <input type="text" name="dokumentu_izena" id="edit_izenburua" required class="inprimaki-kontrola">
                    </div>
                    
                    <div class="inprimaki-taldea">
                        <label>Deskribapena:</label>
                        <textarea name="deskribapena" id="edit_deskribapena" rows="3" class="inprimaki-kontrola"></textarea>
                    </div>
                    
                    <div class="inprimaki-taldea">
                        <label>Ordeztu fitxategia (PDF bakar bat):</label>
                        <input type="file" name="pdf_berria" accept="application/pdf" class="inprimaki-kontrola">
                    </div>
                    
                    <div class="flex-tartea-10 marjina-goi-20">
                        <button type="button" class="botoia botoi-ertza" onclick="itxiModalDok()">Utzi</button>
                        <div class="flex-hazkundea-1"></div>
                        <button type="submit" class="botoia botoi-nagusia">Gorde aldaketak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function bilatuTaulan() {
        let input = document.getElementById("bilatuJarraipenak");
        let filter = input.value.toLowerCase();
        let table = document.getElementById("jarraipenakTableBody");
        let tr = table.getElementsByTagName("tr");

        for (let i = 0; i < tr.length; i++) {
            let tdData = tr[i].getElementsByTagName("td")[0];
            let tdOharrak = tr[i].getElementsByTagName("td")[7]; // Oharrak
            if (tdData || tdOharrak) {
                let dataText = tdData.textContent || tdData.innerText;
                let oharrakText = tdOharrak.textContent || tdOharrak.innerText;
                if (dataText.toLowerCase().indexOf(filter) > -1 || oharrakText.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
    </script>
<?php include_once '../php_orri_includeak/paziente_footer.php'; ?>
