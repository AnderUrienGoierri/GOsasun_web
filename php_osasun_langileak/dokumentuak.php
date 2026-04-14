<?php
// dokumentuak.php: Osasun Langileentzako dokumentuak bilatzeko orria
$bide_absolutua = '../';
session_start();

if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_izena'], ['Osasun Langilea', 'Harrera Langilea'])) {
    header("Location: ../php_orri_hasierakoak/login.php");
    exit;
}

require_once '../php_orri_laguntzaileak/DB_konexioa.php';
$langile_id = $_SESSION['erabiltzaile_id'];
$mezua = '';
$errorea = '';

// --- DOKUMENTUA IGOTZEKO LOGIKA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $p_id = intval($_POST['paziente_id'] ?? 0);
    $titulua = trim($_POST['dokumentu_izena'] ?? '');
    $desk = trim($_POST['deskribapena'] ?? '');
    $pdf = $_FILES['pdf'] ?? null;

    if (!$p_id || !$titulua || !$pdf || $pdf['error'] !== UPLOAD_ERR_OK) {
        $errorea = "Mesedez, hautatu pazientea, bete titulua eta igo PDF bat.";
    } else {
        $ext = strtolower(pathinfo($pdf['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $errorea = "PDF fitxategiak bakarrik onartzen dira.";
        } else {
            $pdf_dir = '../paziente_dokumentuak/';
            if (!is_dir($pdf_dir)) mkdir($pdf_dir, 0777, true);
            
            $timestamp = date('Ymd_His');
            $garbi_titulua = preg_replace('/[^a-zA-Z0-9._-]/', '_', $titulua);
            $dest_name = "dok_manuala_{$p_id}_{$timestamp}_{$garbi_titulua}.pdf";
            
            if (move_uploaded_file($pdf['tmp_name'], $pdf_dir . $dest_name)) {
                $stmtInsert = $pdo->prepare("INSERT INTO dokumentuak (paziente_id, fitxategi_izena, bidea_zerbitzarian, dokumentu_izena, deskribapena) VALUES (?, ?, ?, ?, ?)");
                $stmtInsert->execute([$p_id, $dest_name, 'paziente_dokumentuak/' . $dest_name, $titulua, $desk]);
                $mezua = "Dokumentu berria ondo gorde da.";
            } else {
                $errorea = "Errorea fitxategia gorde bitartean.";
            }
        }
    }
}

// --- PAZIENTEAK LORTU (CREATION DROPDOWN) ---
if ($_SESSION['rol_izena'] === 'Osasun Langilea') {
    $stmtP = $pdo->prepare("SELECT paziente_id, izena, abizenak, nan FROM V_Langile_Pazienteak WHERE langile_id = ? ORDER BY abizenak ASC");
    $stmtP->execute([$langile_id]);
} else {
    $stmtP = $pdo->prepare("SELECT paziente_id, izena, abizenak, nan FROM V_Pazientea ORDER BY abizenak ASC");
    $stmtP->execute();
}
$paziente_zerrenda = $stmtP->fetchAll(PDO::FETCH_ASSOC);

// --- BILAKETA ETA ORDENAZIOA ---
$q = isset($_GET['q']) ? $_GET['q'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'data';
$order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';

$sort_columns = [
    'data' => 'd.igotze_data',
    'pazientea' => 'p.abizenak',
    'nan' => 'p.nan',
    'titulua' => 'd.dokumentu_izena'
];
$order_by = $sort_columns[$sort] ?? 'd.igotze_data';

$sql = "SELECT d.*, p.izena as p_izena, p.abizenak as p_abizenak, p.nan as p_nan 
        FROM dokumentuak d 
        JOIN V_Pazientea p ON d.paziente_id = p.paziente_id";

$params = [];
if ($q) {
    $sql .= " WHERE (p.izena LIKE ? OR p.abizenak LIKE ? OR p.nan LIKE ? OR d.dokumentu_izena LIKE ?)";
    $params = ["%$q%", "%$q%", "%$q%", "%$q%"];
}

// Osasun langilea bada, bere pazienteei mugatu? 
// Normalean dokumentu orokorra denez, beharbada dena ikusi dezakete, edo bereak bakarrik.
// Kasu honetan, bere pazienteei mugatuko diegu segurtasunagatik (Harrerakoek denak ikusten dituzte)
if ($_SESSION['rol_izena'] === 'Osasun Langilea') {
    $sql .= ($q ? " AND " : " WHERE ") . " p.paziente_id IN (SELECT paziente_id FROM V_Langile_Pazienteak WHERE langile_id = ?)";
    $params[] = $langile_id;
}

$sql .= " ORDER BY $order_by $order";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$dokumentuak = $stmt->fetchAll(PDO::FETCH_ASSOC);

$orri_izenburua = 'Dokumentuak Bilatu - GOsasun';
$uneko_orria = 'dokumentuak';
$css_pertsonalizatua = 'dokumentuak.css';

if ($_SESSION['rol_izena'] === 'Osasun Langilea') {
    include_once '../php_orri_includeak/osasun_langile_goiburua.php';
} else {
    include_once '../php_orri_includeak/harrera_goiburua.php';
}
?>

<main class="panel-nagusia">
    <div class="orri-goiburua">
        <div>
            <h2><img src="../img/svg/search.svg" alt="" class="ikono-ertaina marjina-esk-5"> Dokumentuen Bilaketa</h2>
            <p>Bilatu eta kudeatu pazienteen agiri klinikoak eta dokumentu erregistroak.</p>
        </div>
    </div>

    <?php if ($mezua): ?><div class="alerta alerta-arrakasta"><?php echo $mezua; ?></div><?php endif; ?>
    <?php if ($errorea): ?><div class="alerta alerta-errorea"><?php echo $errorea; ?></div><?php endif; ?>

    <!-- DOKUMENTU BERRIA (MODALAREN ORDEZ DETAILS ERABILIKO DUGU ESTILOA MANTENTZEKO) -->
     
    <details class="marjina-behe-20">
        <summary class="botoia botoi-sortu flex-zentratua"><img src="../img/svg/plus-circle.svg" alt="" class="ikono-ertaina marjina-esk-5" style="filter: brightness(0) invert(1);"> Dokumentu Berria Gehitu</summary>
        <div class="inprimaki-edukiontzia form-edukiontzi-zuria padding-30 marjina-goi-20 kutxa-itzala">
            <h3 class="izenburu-urdina marjina-behe-20"><img src="../img/svg/file-plus.svg" alt="" class="ikono-ertaina marjina-esk-5"> Dokumentu Berriaren Erregistroa</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create">
                
                <div class="sareta-bikoa marjina-behe-15">
                    <div class="inprimaki-taldea">
                        <label class="etiketa-lodia">Hautatu Pazientea *</label>
                        <select name="paziente_id" required class="inprimaki-kontrola sarrera-handia">
                            <option value="">Aukeratu bat...</option>
                            <?php foreach ($paziente_zerrenda as $paz): ?>
                                <option value="<?php echo $paz['paziente_id']; ?>">
                                    <?php echo htmlspecialchars($paz['abizenak'] . ', ' . $paz['izena'] . ' (' . $paz['nan'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="inprimaki-taldea">
                        <label class="etiketa-lodia">PDF Agiria *</label>
                        <input type="file" name="pdf" accept="application/pdf" required class="inprimaki-kontrola sarrera-handia">
                    </div>
                </div>

                <div class="sareta-bikoa marjina-behe-20">
                    <div class="inprimaki-taldea">
                        <label class="etiketa-lodia">Dokumentu Titulua *</label>
                        <input type="text" name="dokumentu_izena" required class="inprimaki-kontrola sarrera-handia" placeholder="Adib: Analisi Odol-analisiak">
                    </div>
                    <div class="inprimaki-taldea">
                        <label class="etiketa-lodia">Azalpen Txikia</label>
                        <input type="text" name="deskribapena" class="inprimaki-kontrola sarrera-handia" placeholder="Ohar gehigarriak...">
                    </div>
                </div>

                <div class="flex-bukaera">
                    <button type="submit" class="botoia botoi-nagusia"><img src="../img/svg/upload.svg" alt="" class="ikono-txikia marjina-esk-5" style="filter: brightness(0) invert(1);"> Igo Zerbitzarira</button>
                </div>
            </form>
        </div>
    </details>

    <div class="bilaketa-eremua">
        <form method="get" class="flex-tartea-10 flex-erdia">
            <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Bilatu pazientea edo dokumentua..." class="inprimaki-kontrola">
            <input type="hidden" name="sort" value="<?php echo $sort; ?>">
            <input type="hidden" name="order" value="<?php echo strtolower($order); ?>">
            <button type="submit" class="botoia">Bilatu</button>
            <?php if ($q): ?>
                <a href="dokumentuak.php" class="botoia botoi-ertza">Garbitu</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="korritze-horizontala">
        <table class="dokumentu-taula">
            <thead>
                <tr>
                    <?php 
                    function getSortUrl($col, $current_sort, $current_order, $q) {
                        $new_order = ($current_sort === $col && $current_order === 'DESC') ? 'asc' : 'desc';
                        return "?q=" . urlencode($q) . "&sort=$col&order=$new_order";
                    }
                    ?>
                    <th><a href="<?php echo getSortUrl('data', $sort, $order, $q); ?>" class="esteka-garbia"><img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"> Data</a></th>
                    <th><a href="<?php echo getSortUrl('pazientea', $sort, $order, $q); ?>" class="esteka-garbia"><img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"> Pazientea</a></th>
                    <th><a href="<?php echo getSortUrl('nan', $sort, $order, $q); ?>" class="esteka-garbia"><img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"> NAN</a></th>
                    <th><a href="<?php echo getSortUrl('titulua', $sort, $order, $q); ?>" class="esteka-garbia"><img src="../img/svg/sort.svg" alt="" class="ikono-txikia opazitatea-50"> Dokumentua</a></th>
                    <th>Deskribapena</th>
                    <th class="testua-erdian">Ekintzak</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($dokumentuak)): ?>
                    <tr>
                        <td colspan="6" class="testua-erdian padding-30 opazitatea-50">Ez da dokumenturik aurkitu zure bilaketarako.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($dokumentuak as $d): ?>
                        <tr>
                            <td><?php echo date('Y/m/d H:i', strtotime($d['igotze_data'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($d['p_abizenak'] . ', ' . $d['p_izena']); ?></strong></td>
                            <td><small><?php echo htmlspecialchars($d['p_nan']); ?></small></td>
                            <td class="testu-urdina-lodia"><?php echo htmlspecialchars($d['dokumentu_izena'] ?: $d['fitxategi_izena']); ?></td>
                            <td class="testu-gris-txikia"><?php echo htmlspecialchars($d['deskribapena'] ?: '-'); ?></td>
                            <td class="ekintza-botoiak">
                                <a href="../<?php echo htmlspecialchars($d['bidea_zerbitzarian']); ?>" target="_blank" class="botoi-ikonoa" title="Dokumentua Ikusi">
                                    <img src="../img/svg/search.svg" alt="" class="ikono-ertaina">
                                </a>
                                <a href="../<?php echo htmlspecialchars($d['bidea_zerbitzarian']); ?>" download class="botoi-ikonoa" title="Deskargatu">
                                    <img src="../img/svg/download.svg" alt="" class="ikono-ertaina">
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php 
if ($_SESSION['rol_izena'] === 'Osasun Langilea') {
    include_once '../php_orri_includeak/osasun_footer.php';
} else {
    include_once '../php_orri_includeak/harrera_footer.php';
}
?>

<?php 
if ($_SESSION['rol_izena'] === 'Osasun Langilea') {
    include_once '../php_orri_includeak/osasun_footer.php';
} else {
    include_once '../php_orri_includeak/harrera_footer.php';
}
?>
