<?php
// dokumentuak.php: Osasun Langileentzako dokumentuak bilatzeko orria
$bide_absolutua = '../';
session_start();

if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_izena'], ['Osasun Langilea', 'Harrera Langilea'])) {
    header("Location: ../php_orri_hasierakoak/login.php");
    exit;
}

require_once '../php_orri_laguntzaileak/DB_konexioa.php';
require_once '../php_orri_laguntzaileak/dokumentu_estekak.php';
require_once '../php_orri_laguntzaileak/fitxategi_baimenak.php';
$langile_id = $_SESSION['erabiltzaile_id'];
$mezua = '';
$errorea = '';

// --- DOKUMENTUA IGOTZEKO LOGIKA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
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

                $helmugaBidea = $pdf_dir . $dest_name;

                if (move_uploaded_file($pdf['tmp_name'], $helmugaBidea)) {
                    normalizatu_fitxategi_baimenak($helmugaBidea);
                    try {
                        $pdo->beginTransaction();

                        $stmtJ = $pdo->prepare("INSERT INTO jarraipenak (paziente_id, osasun_langile_id, oharrak, erregistro_data) VALUES (?, ?, ?, NOW())");
                        $stmtJ->execute([$p_id, $langile_id, 'Dokumentu atxikia']);
                        $jarraipen_id = $pdo->lastInsertId();

                        $stmtInsert = $pdo->prepare("INSERT INTO dokumentuak (jarraipena_id, fitxategi_izena, bidea_zerbitzarian, dokumentu_izena, deskribapena) VALUES (?, ?, ?, ?, ?)");
                        $stmtInsert->execute([$jarraipen_id, $dest_name, 'paziente_dokumentuak/' . $dest_name, $titulua, $desk]);

                        $pdo->commit();
                        $mezua = "Dokumentu berria ondo gorde da.";
                    } catch (PDOException $e) {
                        if ($pdo->inTransaction()) {
                            $pdo->rollBack();
                        }
                        if (file_exists($pdf_dir . $dest_name)) {
                            unlink($pdf_dir . $dest_name);
                        }
                        $errorea = "Errorea datu basean gordetzean: " . $e->getMessage();
                    }
                } else {
                    $errorea = "Errorea fitxategia gorde bitartean.";
                }
            }
        }
    } elseif ($_POST['action'] === 'delete') {
        $dok_id = intval($_POST['dok_id']);
        try {
            $stmtGet = $pdo->prepare("SELECT bidea_zerbitzarian FROM dokumentuak WHERE id = ?");
            $stmtGet->execute([$dok_id]);
            $dok = $stmtGet->fetch();

            if ($dok) {
                if (file_exists('../' . $dok['bidea_zerbitzarian'])) {
                    unlink('../' . $dok['bidea_zerbitzarian']);
                }
                $stmtDel = $pdo->prepare("DELETE FROM dokumentuak WHERE id = ?");
                $stmtDel->execute([$dok_id]);
                $mezua = "Dokumentua ezabatu da.";
            }
        } catch (PDOException $e) {
            $errorea = "Errorea ezabatzean: " . $e->getMessage();
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
    'data' => 'igotze_data',
    'pazientea' => 'p_abizenak',
    'nan' => 'p_nan',
    'titulua' => 'dokumentu_izena'
];
$order_by = $sort_columns[$sort] ?? 'igotze_data';


$sql = "SELECT * FROM V_Dokumentuak_Osoa";

$params = [];
if ($q) {
    $sql .= " WHERE (p_izena LIKE ? OR p_abizenak LIKE ? OR p_nan LIKE ? OR dokumentu_izena LIKE ? OR fitxategi_izena LIKE ?)";
    $params = ["%$q%", "%$q%", "%$q%", "%$q%", "%$q%"];
}

// Osasun langilea bada, bere pazienteei mugatu? 
// Normalean dokumentu orokorra denez, beharbada dena ikusi dezakete, edo bereak bakarrik.
// Kasu honetan, bere pazienteei mugatuko diegu segurtasunagatik (Harrerakoek denak ikusten dituzte)
if ($_SESSION['rol_izena'] === 'Osasun Langilea') {
    $sql .= ($q ? " AND " : " WHERE ") . " paziente_id IN (SELECT paziente_id FROM V_Langile_Pazienteak WHERE langile_id = ?)";
    $params[] = $langile_id;
}

// --- PAGINAZIOA ---
$orria = isset($_GET['orria']) ? (int)$_GET['orria'] : 1;
if ($orria < 1) $orria = 1;
$limitea = 10;
$desplazamendua = ($orria - 1) * $limitea;

// Kontatu guztira (filtroak aplikatuta)
$sqlCount = "SELECT COUNT(*) FROM V_Dokumentuak_Osoa";

if ($q) {
    $sqlCount .= " WHERE (p_izena LIKE ? OR p_abizenak LIKE ? OR p_nan LIKE ? OR dokumentu_izena LIKE ? OR fitxategi_izena LIKE ?)";
}

if ($_SESSION['rol_izena'] === 'Osasun Langilea') {
    $sqlCount .= ($q ? " AND " : " WHERE ") . " paziente_id IN (SELECT paziente_id FROM V_Langile_Pazienteak WHERE langile_id = ?)";
}

$stmtCount = $pdo->prepare($sqlCount);
$stmtCount->execute($params);
$guztira = $stmtCount->fetchColumn();
$orri_kopurua = ceil($guztira / $limitea);


$sql .= " ORDER BY $order_by $order LIMIT $limitea OFFSET $desplazamendua";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$dokumentuak = $stmt->fetchAll(PDO::FETCH_ASSOC);

// URL oinarria paginaziorako
$url_oinarria = "dokumentuak.php?q=" . urlencode($q) . "&sort=" . urlencode($sort) . "&order=" . urlencode($order);

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
            <h2><img src="../img/svg/search.svg" alt="" class="ikono-ertaina tarte-eskubia"> Dokumentuen Bilaketa</h2>
            <p>Bilatu eta kudeatu pazienteen agiri klinikoak eta dokumentu erregistroak.</p>
        </div>
    </div>

    <?php if ($mezua): ?><div class="alerta alerta-arrakasta"><?php echo $mezua; ?></div><?php endif; ?>
    <?php if ($errorea): ?><div class="alerta alerta-errorea"><?php echo $errorea; ?></div><?php endif; ?>

    <!-- DOKUMENTU BERRIA (MODALAREN ORDEZ DETAILS ERABILIKO DUGU ESTILOA MANTENTZEKO) -->
    <div style="display: flex; gap: 15px; align-items: flex-start; flex-wrap: wrap;" class="tarte-behea">
        <details style="flex-grow: 1;">
            <summary class="botoia botoi-sortu flex-zentratu" style="width:fit-content;"><img src="../img/svg/plus-circle.svg" alt="" class="ikono-ertaina tarte-eskubia" style="filter: brightness(0) invert(1);"> Dokumentu Berria Gehitu</summary>
            <div class="txartel-atala tarte-goia" style="max-width: 800px; margin-bottom: 20px;">
            <h3 class="izenburu-nabarmena tarte-behea"><img src="../img/svg/file-text.svg" alt="" class="ikono-ertaina tarte-eskubia"> Dokumentu Berriaren Erregistroa</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create">

                <div class="sareta-bikoa tarte-behea">
                    <div class="inprimaki-taldea">
                        <label class="etiketa-lodia">Hautatu Pazientea * (Izena, NAN edo ID)</label>
                        <div class="paziente-bilatzaile-edukiontzia" style="position: relative;">
                            <!-- Bilatzailea AJAX -->
                            <input type="text" id="bilaketa_testua" class="inprimaki-kontrola sarrera-handia" style="margin-bottom: 5px;" placeholder="Bilatu izena, NAN edo ID..." autocomplete="off">
                            <div id="bilaketa_emaitzak" class="bilaketa-emaitzak" style="position: absolute; top: 45px; left: 0; right: 0; background: white; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000; max-height: 300px; overflow-y: auto; display: none;"></div>
                            
                            <!-- Dropdown osoa -->
                            <select name="paziente_id" id="paziente_select" required class="inprimaki-kontrola sarrera-handia">
                                <option value="">...Edo aukeratu zerrendatik...</option>
                                <?php foreach ($paziente_zerrenda as $paz): ?>
                                    <option value="<?php echo $paz['paziente_id']; ?>">
                                        <?php echo htmlspecialchars($paz['abizenak'] . ', ' . $paz['izena'] . ' (' . $paz['nan'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="inprimaki-taldea">
                        <label class="etiketa-lodia">PDF Agiria *</label>
                        <input type="file" name="pdf" accept="application/pdf" required class="inprimaki-kontrola sarrera-handia">
                    </div>
                </div>

                <div class="sareta-bikoa tarte-behea">
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
                    <button type="submit" class="botoia botoi-nagusia"><img src="../img/svg/upload.svg" alt="" class="ikono-txikia tarte-eskubia" style="filter: brightness(0) invert(1);"> Igo Zerbitzarira</button>
                </div>
            </form>
        </div>
        </details>

        <a href="txostena_eraiki.php" class="botoia botoi-nagusia flex-zentratu" style="background-color: var(--primary-color); height: fit-content;">
            <img src="../img/svg/file-text.svg" alt="" class="ikono-ertaina tarte-eskubia ikono-zuria"> Txostena Sortu (PDF)
        </a>
    </div>

    <div class="bilaketa-eremua">
        <form method="get" class="tarte-flex flex-erdia">
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
                    <th class="zentratu">Ekintzak</th>
                </tr>
            </thead>
            <tbody>
                    <?php if (empty($dokumentuak)): ?>
                    <tr>
                        <td colspan="6" class="zentratu padding-30 opazitatea-50">Ez da dokumenturik aurkitu zure bilaketarako.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($dokumentuak as $d): ?>
                        <?php $dokumentu_esteka = lortu_dokumentu_esteka($d); ?>
                        <tr>
                            <td><?php echo date('Y/m/d H:i', strtotime($d['igotze_data'])); ?></td>
                            <td><strong><?php echo htmlspecialchars(($d['p_abizenak'] ?? $d['paziente_abizenak'] ?? '') . ', ' . ($d['p_izena'] ?? $d['paziente_izena'] ?? '')); ?></strong></td>
                            <td><small><?php echo htmlspecialchars($d['p_nan'] ?? '-'); ?></small></td>
                            <td class="testu-urdina-lodia"><?php echo htmlspecialchars($d['dokumentu_izena'] ?: $d['fitxategi_izena']); ?></td>
                            <td class="testu-gris-txikia"><?php echo htmlspecialchars($d['deskribapena'] ?: '-'); ?></td>
                            <td class="ekintza-botoiak">
                                <a href="<?php echo htmlspecialchars($dokumentu_esteka); ?>" target="_blank" class="botoi-ikonoa" title="Ikusi PDF">
                                    <img src="../img/svg/eye.svg" alt="" class="ikono-ertaina">
                                </a>
                                <a href="<?php echo htmlspecialchars($dokumentu_esteka); ?>" download class="botoi-ikonoa" title="Deskargatu">
                                    <img src="../img/svg/download.svg" alt="" class="ikono-ertaina">
                                </a>
                                <button type="button" class="botoi-ikonoa ezabatu-botoia" title="Ezabatu" onclick="if(confirm('Ziur zaude dokumentua ezabatu nahi duzula?')) { document.getElementById('delete_dok_id').value = <?php echo $d['dokumentu_id']; ?>; document.getElementById('deleteForm').submit(); }">
                                    <img src="../img/svg/trash-2.svg" alt="" class="ikono-ertaina">
                                </button>
                            </td>
                        </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Delete Form (Hidden) -->
<form id="deleteForm" method="post" style="display:none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="dok_id" id="delete_dok_id">
</form>

<!-- PAGINAZIO BOTOIAK -->
<?php if ($orri_kopurua > 1): ?>
    <div class="paginazioa flex-zentratu tarte-goia-20 talde-flex">
        <a href="<?php echo $url_oinarria; ?>&orria=<?php echo max(1, $orria - 1); ?>"
            class="botoia botoi-ertza-txikia <?php echo ($orria <= 1) ? 'desaktibatuta' : ''; ?>">
            <img src="../img/svg/chevron-left.svg" alt="" class="ikono-txikia"> Aurrekoak
        </a>

        <span class="orrialde-info">Orrialdea: <strong><?php echo $orria; ?></strong> / <?php echo $orri_kopurua; ?></span>

        <a href="<?php echo $url_oinarria; ?>&orria=<?php echo min($orri_kopurua, $orria + 1); ?>"
            class="botoia botoi-ertza-txikia <?php echo ($orria >= $orri_kopurua) ? 'desaktibatuta' : ''; ?>">
            Hurrengoak <img src="../img/svg/chevron-right.svg" alt="" class="ikono-txikia">
        </a>
    </div>
<?php endif; ?>

</main>

<?php
if ($_SESSION['rol_izena'] === 'Osasun Langilea') {
    include_once '../php_orri_includeak/osasun_footer.php';
} else {
    include_once '../php_orri_includeak/harrera_footer.php';
}
?>

<script>
// Pazienteen bilaketa dinamikoa AJAX bidez
const bilaketaInput = document.getElementById('bilaketa_testua');
const emaitzakBox = document.getElementById('bilaketa_emaitzak');

bilaketaInput.addEventListener('input', async (e) => {
    const q = e.target.value;
    if (q.length < 2) {
        emaitzakBox.style.display = 'none';
        return;
    }

    try {
        const resp = await fetch(`../php_orri_laguntzaileak/bilatu_pazienteak_ajax.php?q=${encodeURIComponent(q)}`);
        const data = await resp.json();

        if (data.success && data.pazienteak.length > 0) {
            emaitzakBox.innerHTML = '';
            data.pazienteak.forEach(p => {
                const div = document.createElement('div');
                div.style.padding = '10px';
                div.style.borderBottom = '1px solid #eee';
                div.style.cursor = 'pointer';
                div.style.display = 'flex';
                div.style.alignItems = 'center';
                
                div.onmouseover = () => div.style.backgroundColor = '#f1f5f9';
                div.onmouseout = () => div.style.backgroundColor = 'transparent';
                
                const imgPath = p.irudia ? `../${p.irudia.replace('img/', 'img/png/')}` : '../img/png/lehenetsia_pazientea.png';
                div.innerHTML = `
                    <img src="${imgPath}" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 15px; object-fit: cover;" onerror="this.src='../img/png/lehenetsia_pazientea.png'">
                    <div>
                        <strong style="display:block; color: #1e293b;">${p.izena} ${p.abizenak}</strong>
                        <small style="color: #64748b;">NAN: ${p.nan} | ID: #${p.paziente_id}</small>
                    </div>
                `;
                div.onclick = () => {
                    document.getElementById('paziente_select').value = p.paziente_id;
                    bilaketaInput.value = `${p.izena} ${p.abizenak}`;
                    emaitzakBox.style.display = 'none';
                };
                emaitzakBox.appendChild(div);
            });
            emaitzakBox.style.display = 'block';
        } else {
            emaitzakBox.style.display = 'none';
        }
    } catch (err) {
        console.error("Bilaketa errorea:", err);
    }
});

// Itxi emaitzak kanpoan klik egitean
document.addEventListener('click', (e) => {
    if (!bilaketaInput.contains(e.target) && !emaitzakBox.contains(e.target)) {
        emaitzakBox.style.display = 'none';
    }
});
</script>
