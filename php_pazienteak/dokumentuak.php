<?php
// dokumentuak.php: Pazientearen dokumentuak kudeatzeko orria
$bide_absolutua = '../';
session_start();

if (!isset($_SESSION['rol_id']) || $_SESSION['rol_izena'] !== 'Pazientea') {
    header("Location: ../php_orri_hasierakoak/login.php");
    exit;
}

require_once '../php_orri_laguntzaileak/DB_konexioa.php';
$paziente_id = $_SESSION['erabiltzaile_id'];
$mezua = '';
$errorea = '';

// Kudeatu POST ekintzak (Editatu / Ezabatu)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'edit') {
            $dok_id = intval($_POST['dok_id']);
            $titulua = $_POST['dokumentu_izena'] ?? '';
            $desk = $_POST['deskribapena'] ?? '';
            
            try {
                // Egiaztatu jabetza
                $stmtCheck = $pdo->prepare("SELECT id, bidea_zerbitzarian FROM dokumentuak WHERE id = ? AND paziente_id = ?");
                $stmtCheck->execute([$dok_id, $paziente_id]);
                $dok_zaharra = $stmtCheck->fetch();
                
                if ($dok_zaharra) {
                    $bide_berria = $dok_zaharra['bidea_zerbitzarian'];
                    
                    // Fitxategi berria igo bada
                    if (isset($_FILES['pdf_berria']) && $_FILES['pdf_berria']['error'] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['pdf_berria']['tmp_name'];
                        $orig_name = basename($_FILES['pdf_berria']['name']);
                        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
                        
                        if ($ext === 'pdf') {
                            $pdf_dir = '../paziente_dokumentuak/';
                            $data = date('Ymd');
                            $ordua = date('His');
                            $garbi_titulua = preg_replace('/[^a-zA-Z0-9._-]/', '_', $titulua);
                            $fitx_izena_berria = "dok_paziente_{$paziente_id}_{$data}_{$ordua}_{$garbi_titulua}.pdf";
                            $bide_berria_osoa = $pdf_dir . $fitx_izena_berria;
                            
                            if (move_uploaded_file($tmp_name, $bide_berria_osoa)) {
                                // Ezabatu zaharra (baina segurua bada)
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
        } elseif ($_POST['action'] === 'delete') {
            $dok_id = intval($_POST['dok_id']);
            try {
                $stmtGet = $pdo->prepare("SELECT bidea_zerbitzarian FROM dokumentuak WHERE id = ? AND paziente_id = ?");
                $stmtGet->execute([$dok_id, $paziente_id]);
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
        } elseif ($_POST['action'] === 'create') {
            $titulua = $_POST['dokumentu_izena'] ?? '';
            $desk = $_POST['deskribapena'] ?? '';
            $pdf = $_FILES['pdf'] ?? null;

            if (!$titulua || !$pdf || $pdf['error'] !== UPLOAD_ERR_OK) {
                $errorea = "Datu guztiak bete eta PDF-a igo behar duzu.";
            } else {
                $ext = strtolower(pathinfo($pdf['name'], PATHINFO_EXTENSION));
                if ($ext !== 'pdf') {
                    $errorea = "Fitxategia PDF formatuan egon behar da.";
                } else {
                    $pdf_dir = '../paziente_dokumentuak/';
                    if (!is_dir($pdf_dir)) mkdir($pdf_dir, 0777, true);
                    
                    $data = date('Ymd');
                    $ordua = date('His');
                    $garbi_titulua = preg_replace('/[^a-zA-Z0-9._-]/', '_', $titulua);
                    $dest_name = "dok_paziente_{$paziente_id}_{$data}_{$ordua}_{$garbi_titulua}.pdf";
                    
                    if (move_uploaded_file($pdf['tmp_name'], $pdf_dir . $dest_name)) {
                        $stmt = $pdo->prepare("INSERT INTO dokumentuak (paziente_id, fitxategi_izena, bidea_zerbitzarian, dokumentu_izena, deskribapena) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$paziente_id, $dest_name, 'paziente_dokumentuak/' . $dest_name, $titulua, $mask]);
                        $mezua = "Dokumentu berria ondo igo da.";
                    } else {
                        $errorea = "Errorea fitxategia zerbitzarira mugitzean.";
                    }
                }
            }
        }
    }
}

// Dokumentuak lortu
$ordenazioa = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';
$bilaketa = isset($_GET['q']) ? $_GET['q'] : '';

$sql = "SELECT * FROM dokumentuak WHERE paziente_id = ?";
$params = [$paziente_id];

if ($bilaketa) {
    $sql .= " AND (dokumentu_izena LIKE ? OR deskribapena LIKE ?)";
    $params[] = "%$bilaketa%";
    $params[] = "%$bilaketa%";
}

$sql .= " ORDER BY igotze_data $ordenazioa";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$dokumentuak = $stmt->fetchAll(PDO::FETCH_ASSOC);

$orri_izenburua = 'Nire Dokumentuak - GOsasun';
$uneko_orria = 'dokumentuak';
$css_pertsonalizatua = 'dokumentuak.css';
include_once '../php_orri_includeak/paziente_goiburua.php';
?>

<main class="panel-nagusia">
    <div class="orri-goiburua">
        <div>
            <h2><img src="../img/svg/file-text.svg" alt="" class="ikono-ertaina marjina-esk-5"> Dokumentuen Kudeaketa</h2>
            <p>Ikusi, editatu edo ezabatu igotako dokumentu guztiak.</p>
        </div>
        <button class="botoia botoi-sortu" onclick="irekiModalSortu()">+ Gehitu Dokumentu Berria</button>
    </div>

    <?php if ($mezua): ?><div class="alerta alerta-arrakasta"><?php echo htmlspecialchars($mezua); ?></div><?php endif; ?>
    <?php if ($errorea): ?><div class="alerta alerta-errorea"><?php echo htmlspecialchars($errorea); ?></div><?php endif; ?>

    <div class="bilaketa-eremua">
        <form method="get" action="dokumentuak.php" class="flex-tartea-10">
            <input type="text" name="q" value="<?php echo htmlspecialchars($bilaketa); ?>" placeholder="Bilatu dokumentua..." class="inprimaki-kontrola">
            <button type="submit" class="botoia">Bilatu</button>
            <?php if ($bilaketa): ?>
                <a href="dokumentuak.php" class="botoia botoi-ertza">Garbitu</a>
            <?php endif; ?>
        </form>
        <div class="ordenazioa">
            <a href="?q=<?php echo urlencode($bilaketa); ?>&order=<?php echo $ordenazioa === 'DESC' ? 'asc' : 'desc'; ?>" class="botoia botoi-ertza">
                Ordenatu: <?php echo $ordenazioa === 'DESC' ? 'Berrienak lehenengo' : 'Zaharrenak lehenengo'; ?>
            </a>
        </div>
    </div>

    <div class="korritze-horizontala">
        <table class="dokumentu-taula">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Izena</th>
                    <th>Deskribapena</th>
                    <th>Ekintzak</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($dokumentuak)): ?>
                    <tr>
                        <td colspan="4" class="testua-erdian">Ez da dokumenturik aurkitu.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($dokumentuak as $d): ?>
                        <tr>
                            <td><?php echo date('Y/m/d H:i', strtotime($d['igotze_data'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($d['dokumentu_izena'] ?: $d['fitxategi_izena']); ?></strong></td>
                            <td><?php echo htmlspecialchars($d['deskribapena'] ?: '-'); ?></td>
                            <td class="ekintza-botoiak">
                                <a href="../<?php echo htmlspecialchars($d['bidea_zerbitzarian']); ?>" target="_blank" class="botoi-ikonoa" title="Ikusi PDF">
                                    <img src="../img/svg/eye.svg" alt="" class="ikono-ertaina">
                                </a>
                                <button class="botoi-ikonoa editatu-botoia" onclick="irekiModalEditatu(<?php echo $d['id']; ?>, '<?php echo addslashes($d['dokumentu_izena']); ?>', '<?php echo addslashes($d['deskribapena']); ?>')" title="Editatu">
                                    <img src="../img/svg/pencil.svg" alt="" class="ikono-ertaina">
                                </button>
                                <button class="botoi-ikonoa ezabatu-botoia" onclick="konfirmatuEzabatu(<?php echo $d['id']; ?>)" title="Ezabatu">
                                    <img src="../img/svg/trash-2.svg" alt="" class="ikono-ertaina">
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Create Modal -->
<div id="modalSortu" class="modal">
    <div class="modal-content">
        <span class="itxi-modal" onclick="itxiModalSortu()">&times;</span>
        <h3>Igo Dokumentu Berria</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="create">
            <div class="marjina-behe-15">
                <label>Dokumentuaren izena: <span class="beharrezkoa">*</span></label>
                <input type="text" name="dokumentu_izena" required class="inprimaki-kontrola" placeholder="Adib: Analitika 2024">
            </div>
            <div class="marjina-behe-15">
                <label>Deskribapena:</label>
                <textarea name="deskribapena" rows="3" class="inprimaki-kontrola" placeholder="Dokumentuaren xehetasun gehiago..."></textarea>
            </div>
            <div class="marjina-behe-20">
                <label>PDF fitxategia: <span class="beharrezkoa">*</span></label>
                <input type="file" name="pdf" accept="application/pdf" required class="inprimaki-kontrola">
            </div>
            <div class="botoi-taldea">
                <button type="button" class="botoia botoi-ertza" onclick="itxiModalSortu()">Utzi</button>
                <button type="submit" class="botoia">Igo Dokumentua</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="modalEditatu" class="modal">
    <div class="modal-content">
        <span class="itxi-modal" onclick="itxiModal()">&times;</span>
        <h3>Editatu Dokumentua</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit">
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
                <button type="button" class="botoia botoi-ertza" onclick="itxiModal()">Utzi</button>
                <button type="submit" class="botoia">Gorde aldaketak</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Form (Hidden) -->
<form id="deleteForm" method="post" style="display:none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="dok_id" id="delete_dok_id">
</form>

<script>
function irekiModalSortu() {
    document.getElementById('modalSortu').style.display = 'block';
}

function itxiModalSortu() {
    document.getElementById('modalSortu').style.display = 'none';
}

function irekiModalEditatu(id, izena, desk) {
    document.getElementById('edit_dok_id').value = id;
    document.getElementById('edit_izenburua').value = izena;
    document.getElementById('edit_deskribapena').value = desk;
    document.getElementById('modalEditatu').style.display = 'block';
}

function itxiModal() {
    document.getElementById('modalEditatu').style.display = 'none';
}

function konfirmatuEzabatu(id) {
    if (confirm('Ziur zaude dokumentu hau ezabatu nahi duzula?')) {
        document.getElementById('delete_dok_id').value = id;
        document.getElementById('deleteForm').submit();
    }
}

// Itxi modala kanpoan sakatzean
window.onclick = function(event) {
    if (event.target == document.getElementById('modalEditatu')) {
        itxiModal();
    }
    if (event.target == document.getElementById('modalSortu')) {
        itxiModalSortu();
    }
}
</script>

<?php include_once '../php_orri_includeak/paziente_footer.php'; ?>
