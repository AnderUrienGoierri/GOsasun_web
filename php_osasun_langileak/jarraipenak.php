<?php
$bide_absolutua = '../'; session_start();
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_izena'] !== 'Osasun Langilea') {
    header("Location: ../php_orri_hasierakoak/login.php");
    exit;
}

require_once '../php_orri_laguntzaileak/DB_konexioa.php';
$osasun_langile_id = $_SESSION['erabiltzaile_id'];
$mezua = "";
$errorea = "";

// --- DOKUMENTUA IGOTZEKO LOGIKA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'gehitu_dokumentua') {
    $j_id = intval($_POST['jarraipen_id'] ?? 0);
    $d_izena = trim($_POST['dokumentu_izena'] ?? '');
    $d_desk = trim($_POST['deskribapena'] ?? '');
    $pdf = $_FILES['pdf_fitxategia'] ?? null;

    if (!$j_id || !$d_izena || !$pdf || $pdf['error'] !== UPLOAD_ERR_OK) {
        $errorea = "Mesedez, bete beharrezko eremu guztiak eta aukeratu PDF bat.";
    } else {
        $ext = strtolower(pathinfo($pdf['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $errorea = "PDF fitxategiak bakarrik onartzen dira.";
        } else {
            $pdf_dir = '../paziente_dokumentuak/';
            if (!is_dir($pdf_dir)) mkdir($pdf_dir, 0777, true);

            $timestamp = date('Ymd_His');
            $garbi_izena = preg_replace('/[^a-zA-Z0-9._-]/', '_', $d_izena);
            $dest_name = "dok_jarraipena_{$j_id}_{$timestamp}_{$garbi_izena}.pdf";
            
            if (move_uploaded_file($pdf['tmp_name'], $pdf_dir . $dest_name)) {
                $stmtInsert = $pdo->prepare("INSERT INTO dokumentuak (jarraipena_id, fitxategi_izena, bidea_zerbitzarian, dokumentu_izena, deskribapena) VALUES (?, ?, ?, ?, ?)");
                $stmtInsert->execute([$j_id, $dest_name, 'paziente_dokumentuak/' . $dest_name, $d_izena, $d_desk]);
                $mezua = "Dokumentu berria ondo gorde da.";
            } else {
                $errorea = "Errorea fitxategia gorde bitartean.";
            }
        }
    }
}

// 1. Lortu esleitutako pazienteen zerrenda
$stmtP = $pdo->prepare("SELECT paziente_id AS id, izena, abizenak, nan 
                       FROM V_Langile_Pazienteak
                       WHERE langile_id = ?
                       ORDER BY abizenak ASC");
$stmtP->execute([$osasun_langile_id]);
$pazienteak = $stmtP->fetchAll(PDO::FETCH_ASSOC);

// 2. Aukeratutako pazientearen jarraipenak lortu (baldin badago)
$paziente_id_aukera = $_GET['paziente_id'] ?? null;
$jarraipenak = [];
$paziente_izena = "";

if ($paziente_id_aukera) {
    // Ziurtatu medikuak sarbidea duela
    $stm_egiaztatu = $pdo->prepare("SELECT 1 FROM V_Langile_Pazienteak WHERE langile_id = ? AND paziente_id = ?");
    $stm_egiaztatu->execute([$osasun_langile_id, $paziente_id_aukera]);
    
    if ($stm_egiaztatu->fetch()) {
        $h_data = $_GET['h_data'] ?? null;
        $a_data = $_GET['a_data'] ?? null;
        
        $sql = "
            SELECT j.*, 
                   (SELECT COUNT(*) FROM dokumentuak d WHERE d.jarraipena_id = j.id) as dokumentu_kopurua,
                   (SELECT d.bidea_zerbitzarian FROM dokumentuak d WHERE d.jarraipena_id = j.id ORDER BY d.igotze_data DESC LIMIT 1) as azken_dokumentu_bidea
            FROM jarraipenak j 
            WHERE j.paziente_id = ?";
        
        $params = [$paziente_id_aukera];
        
        if ($h_data) {
            $sql .= " AND j.erregistro_data >= ?";
            $params[] = str_replace('T', ' ', $h_data) . ':00';
        }
        if ($a_data) {
            $sql .= " AND j.erregistro_data <= ?";
            $params[] = str_replace('T', ' ', $a_data) . ':59';
        }
        
        $sql .= " ORDER BY j.erregistro_data DESC";
        
        $stmtN = $pdo->prepare($sql);
        $stmtN->execute($params);
        $jarraipenak = $stmtN->fetchAll(PDO::FETCH_ASSOC);
        
        // Lortu pazientearen izena izenbururako
        $stmtI = $pdo->prepare("SELECT izena, abizenak FROM V_Pazientea WHERE paziente_id = ?");
        $stmtI->execute([$paziente_id_aukera]);
        $pInfo = $stmtI->fetch();
        $paziente_izena = $pInfo['izena'] . " " . $pInfo['abizenak'];
    }
}

$orri_izenburua = "Pazienteen jarraipenak - GOsasun";
$uneko_orria = "jarraipenak";
$css_pertsonalizatua = "osasun_langile_errezetak.css";

include_once '../php_orri_includeak/osasun_langile_goiburua.php';
?>

    <main class="panel-nagusia">
        <div class="orri-goiburua">
            <div>
                <h2><img src="../img/svg/clipboard-pen.svg" alt="" class="ikono-ertaina marjina-esk-5"> Jarraipenen Historiala</h2>
                <p>Hautatu paziente bat bere bizi-seinaleen eta sintomen historiala ikusteko. Jarraipenak eta neurketak kudeatzeko tresna nagusia.</p>
            </div>
        </div>

        <?php if ($mezua): ?><div class="alerta alerta-arrakasta"><?php echo $mezua; ?></div><?php endif; ?>
        <?php if ($errorea): ?><div class="alerta alerta-errorea"><?php echo $errorea; ?></div><?php endif; ?>

        <div class="inprimaki-edukiontzia form-edukiontzi-zuria padding-30 marjina-behe-30 kutxa-itzala">
            <h3 class="izenburu-urdina marjina-behe-20"><img src="../img/svg/search.svg" alt="" class="ikono-ertaina marjina-esk-5"> Pazientea eta Filtroak</h3>
            
            <form id="pazienteAukeraForm" action="jarraipenak.php" method="GET">
                <div class="sareta-bikoa">
                    <div class="inprimaki-taldea">
                        <label for="pazienteBilatzailea" class="etiketa-lodia">Bilatu Pazientea (Izena edo NAN)</label>
                        <input type="text" id="pazienteBilatzailea" class="inprimaki-kontrola sarrera-handia" placeholder="Adib: Garcia, Ane..." list="pazienteakList">
                        <datalist id="pazienteakList">
                            <?php foreach ($pazienteak as $p): ?>
                                <option value="<?php echo htmlspecialchars($p['abizenak'] . ", " . $p['izena']); ?>" data-id="<?php echo $p['id']; ?>">
                                    <?php echo htmlspecialchars($p['nan']); ?>
                                </option>
                            <?php endforeach; ?>
                        </datalist>
                        <input type="hidden" name="paziente_id" id="paziente_id" value="<?php echo $paziente_id_aukera; ?>">
                    </div>

                    <div class="inprimaki-taldea">
                        <label class="etiketa-lodia">Data-tartea aukeratu (Hasi - Bukatu)</label>
                        <div class="flex-tartea-10">
                            <input type="datetime-local" name="h_data" value="<?php echo $_GET['h_data'] ?? ''; ?>" class="inprimaki-kontrola" title="Hasiera data">
                            <input type="datetime-local" name="a_data" value="<?php echo $_GET['a_data'] ?? ''; ?>" class="inprimaki-kontrola" title="Amaiera data">
                        </div>
                    </div>
                </div>
                
                <div class="flex-bukaera marjina-goi-15">
                    <button type="submit" class="botoia botoi-nagusia"><img src="../img/svg/search.svg" alt="" class="ikono-txikia marjina-esk-5" style="filter: brightness(0) invert(1);"> Filtroak aplikatu</button>
                    <?php if (isset($_GET['h_data']) || isset($_GET['a_data'])): ?>
                        <a href="jarraipenak.php?paziente_id=<?php echo $paziente_id_aukera; ?>" class="botoia botoi-ertza marjina-ezk-10">Filtroa garbitu</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <?php if ($paziente_id_aukera): ?>
            <div class="kutxa-zuria-itzala padding-25">
                <div class="flex-tartea-15 flex-erdia marjina-behe-20 orri-azpiko-marra">
                    <div>
                        <h3 class="izenburu-urdina marjina-behe-5"><?php echo htmlspecialchars($paziente_izena); ?></h3>
                        <p class="testu-gris-txikia">Pazientearen jarraipen historiko osoa</p>
                    </div>
                    <div class="flex-taldea-15">
                        <label class="kurtsore-erakuslea flex-erdia">
                            <input type="checkbox" id="ikusiEzohikoak" class="marjina-esk-5"> 
                            <span class="testu-arriskua-ezk-lodia">Ezohikoak soilik</span>
                        </label>
                        <a href="paziente_info.php?id=<?php echo $paziente_id_aukera; ?>" class="botoia botoi-ertza-txikia">
                            <img src="../img/svg/user.svg" alt="" class="ikono-txikia marjina-esk-5"> Ikusi Fitxa
                        </a>
                    </div>
                </div>
                
                <?php if (count($jarraipenak) > 0): ?>
                    <div class="korritze-horizontala">
                        <table class="jarraipen-taula">
                            <thead>
                                <tr>
                                    <th onclick="ordenatuTaula(0)" class="kurtsore-erakuslea">Data <img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"></th>
                                    <th onclick="ordenatuTaula(1)" class="kurtsore-erakuslea">Ordua <img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"></th>
                                    <th onclick="ordenatuTaula(2)" class="kurtsore-erakuslea">SIS <img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"></th>
                                    <th onclick="ordenatuTaula(3)" class="kurtsore-erakuslea">DIA <img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"></th>
                                    <th onclick="ordenatuTaula(4)" class="kurtsore-erakuslea">Pultsua <img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"></th>
                                    <th onclick="ordenatuTaula(5)" class="kurtsore-erakuslea">Altuera <img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"></th>
                                    <th onclick="ordenatuTaula(6)" class="kurtsore-erakuslea">Pisua <img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"></th>
                                    <th style="min-width: 200px;">Oharrak / Sintomak</th>
                                    <th style="min-width: 150px;">Ekintzak</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jarraipenak as $n): ?>
                                    <tr>
                                        <td><strong><?php echo date('Y/m/d', strtotime($n['erregistro_data'])); ?></strong></td>
                                        <td><small><?php echo date('H:i', strtotime($n['erregistro_data'])); ?></small></td>
                                        <td class="testua-erdian"><?php echo $n['tentsio_sistolikoa'] ?: '-'; ?></td>
                                        <td class="testua-erdian"><?php echo $n['tentsio_diastolikoa'] ?: '-'; ?></td>
                                        <td class="testu-urdina-lodia testua-erdian"><?php echo $n['pultsua_ppm'] ? $n['pultsua_ppm'] . ' <small>ppm</small>' : '-'; ?></td>
                                        <td class="testua-erdian"><?php echo $n['altuera'] ? $n['altuera'] . ' <small>cm</small>' : '-'; ?></td>
                                        <td class="testua-erdian"><?php echo $n['pisua_kg'] ? $n['pisua_kg'] . ' <small>kg</small>' : '-'; ?></td>
                                        <td class="testu-gris-iluna"><?php echo htmlspecialchars($n['oharrak'] ?? '-'); ?></td>
                                        <td class="ekintza-botoiak">
                                            <!-- Editatu jarraipena -->
                                            <a href="jarraipen_editatu.php?id=<?php echo $n['id']; ?>" class="botoi-ikonoa" title="Editatu jarraipena">
                                                <img src="../img/svg/pencil.svg" alt="" class="ikono-ertaina">
                                            </a>
                                            <!-- Dokumentua gehitu -->
                                            <a href="javascript:void(0)" class="botoi-ikonoa ireki-igo-modal" title="Dokumentua gehitu" data-id="<?php echo $n['id']; ?>">
                                                <img src="../img/svg/clipboard-pen.svg" alt="" class="ikono-ertaina">
                                            </a>
                                            <!-- Dokumentuak ikusi -->
                                            <a href="javascript:void(0)" class="botoi-ikonoa ireki-ikusi-modal <?php echo $n['dokumentu_kopurua'] == 0 ? 'opazitatea-20' : ''; ?>" 
                                               title="Dokumentuak ikusi" data-id="<?php echo $n['id']; ?>">
                                                <img src="../img/svg/search.svg" alt="" class="ikono-ertaina">
                                            </a>
                                            
                                            <?php if ($n['dokumentu_kopurua'] > 0): ?>
                                                <!-- Deskargatu azkena -->
                                                <a href="../<?php echo htmlspecialchars($n['azken_dokumentu_bidea']); ?>" target="_blank" class="botoi-ikonoa" title="Deskargatu azkena">
                                                    <img src="../img/svg/download.svg" alt="" class="ikono-ertaina">
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="testua-erdian hutsartea-50 opazitatea-50">
                        <img src="../img/svg/inbox.svg" alt="" class="ikono-handia-48 marjina-behe-15">
                        <p>Ez dago neurketa erregistratuta paziente honentzat.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="testua-erdian hutsartea-100 opazitatea-50">
                <img src="../img/svg/users.svg" alt="" class="ikono-handia-48 marjina-behe-15">
                <p>Hautatu paziente bat bere neurketen historia kargatzeko.</p>
            </div>
        <?php endif; ?>
    </main>

    <!-- 1. MODALA: Dokumentua gehitu -->
    <div id="igoDokumentuaModala" class="modala">
        <div class="modala-edukia">
            <span class="itxi-modala" onclick="itxiModala('igoDokumentuaModala')">&times;</span>
            <h3 class="izenburu-urdina"><img src="../img/svg/download.svg" alt="" class="ikono-ertaina marjina-esk-5"> Dokumentu berria igo</h3>
            <form method="POST" enctype="multipart/form-data" class="marjina-goi-20">
                <input type="hidden" name="action" value="gehitu_dokumentua">
                <input type="hidden" name="jarraipen_id" id="modal_jarraipen_id">
                
                <div class="inprimaki-taldea marjina-behe-15">
                    <label class="etiketa-lodia">Dokumentuaren izena *</label>
                    <input type="text" name="dokumentu_izena" required class="inprimaki-kontrola" placeholder="Adib: Analisia, Txostena...">
                </div>
                
                <div class="inprimaki-taldea marjina-behe-15">
                    <label class="etiketa-lodia">Deskribapena</label>
                    <textarea name="deskribapena" class="inprimaki-kontrola" rows="2"></textarea>
                </div>
                
                <div class="inprimaki-taldea marjina-behe-20">
                    <label class="etiketa-lodia">PDF fitxategia *</label>
                    <input type="file" name="pdf_fitxategia" required accept="application/pdf" class="inprimaki-kontrola">
                </div>
                
                <div class="botoi-taldea flex-bukaera">
                    <button type="button" class="botoia botoi-ertza" onclick="itxiModala('igoDokumentuaModala')">Utzi</button>
                    <button type="submit" class="botoia botoi-nagusia">Gorde Dokumentua</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. MODALA: Dokumentuak ikusi -->
    <div id="ikusiDokumentuakModala" class="modala">
        <div class="modala-edukia modala-handia">
            <span class="itxi-modala" onclick="itxiModala('ikusiDokumentuakModala')">&times;</span>
            <h3 class="izenburu-urdina"><img src="../img/svg/file-text.svg" alt="" class="ikono-ertaina marjina-esk-5"> Lotutako Dokumentuak</h3>
            <div id="dokumentuZerrendaEremua" class="marjina-goi-20">
                <!-- AJAX bidez beteko da -->
                <p class="testua-erdian padding-20">Kargatzen...</p>
            </div>
        </div>
    </div>

    <!-- JavaScript inclusion -->
    <script src="../js/jarraipenak.js"></script>
<?php include_once '../php_orri_includeak/osasun_footer.php'; ?>
