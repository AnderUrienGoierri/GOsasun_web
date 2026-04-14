<?php
// dokumentuak.php: Osasun Langileentzako dokumentuak bilatzeko orria
$bide_absolutua = '../';
session_start();

if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_izena'], ['Osasun Langilea', 'Harrera Langilea'])) {
    header("Location: ../php_hasiera/login.php");
    exit;
}

require_once '../php_laguntzaileak/DB_konexioa.php';
$mezua = '';
$errorea = '';

// Bilaketa eta ordenazio parametroak
$q = isset($_GET['q']) ? $_GET['q'] : '';
$order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';

// SQL eraiki
$sql = "SELECT d.*, p.izena as p_izena, p.abizenak as p_abizenak, p.nan as p_nan 
        FROM dokumentuak d 
        JOIN v_pazientea p ON d.paziente_id = p.paziente_id";

$params = [];
if ($q) {
    $sql .= " WHERE p.izena LIKE ? OR p.abizenak LIKE ? OR p.nan LIKE ? OR d.dokumentu_izena LIKE ?";
    $params = ["%$q%", "%$q%", "%$q%", "%$q%"];
}

$sql .= " ORDER BY d.igotze_data $order";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$dokumentuak = $stmt->fetchAll(PDO::FETCH_ASSOC);

$orri_izenburua = 'Dokumentuak Bilatu - GOsasun';
$uneko_orria = 'dokumentuak';
$css_pertsonalizatua = 'dokumentuak.css';

// Goiburua aukeratu rolaren arabera
if ($_SESSION['rol_izena'] === 'Osasun Langilea') {
    include_once '../php_includeak/mediku_goiburua.php';
} else {
    include_once '../php_includeak/harrera_goiburua.php';
}
?>

<main class="panel-nagusia">
    <div class="orri-goiburua">
        <div>
            <h2><img src="../img/svg/search.svg" alt="" class="ikono-ertaina marjina-esk-5"> Dokumentuen Bilaketa</h2>
            <p>Bilatu pazienteen dokumentuak izen, abizen, NAN edo dokumentuaren tituluaren arabera.</p>
        </div>
    </div>

    <div class="bilaketa-eremua">
        <form method="get" class="flex-tartea-10">
            <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Bilatu pazientea edo dokumentua..." class="inprimaki-kontrola">
            <button type="submit" class="botoia">Bilatu</button>
            <?php if ($q): ?>
                <a href="dokumentuak.php" class="botoia botoi-ertza">Garbitu</a>
            <?php endif; ?>
        </form>
        <div class="ordenazioa">
            <a href="?q=<?php echo urlencode($q); ?>&order=<?php echo $order === 'DESC' ? 'asc' : 'desc'; ?>" class="botoia botoi-ertza">
                Ordenatu: <?php echo $order === 'DESC' ? 'Berrienak lehenengo' : 'Zaharrenak lehenengo'; ?>
            </a>
        </div>
    </div>

    <div class="korritze-horizontala">
        <table class="dokumentu-taula">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Pazientea</th>
                    <th>NAN</th>
                    <th>Dokumentua</th>
                    <th>Deskribapena</th>
                    <th>Ekintzak</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($dokumentuak)): ?>
                    <tr>
                        <td colspan="6" class="testua-erdian">Ez da dokumenturik aurkitu.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($dokumentuak as $d): ?>
                        <tr>
                            <td><?php echo date('Y/m/d H:i', strtotime($d['igotze_data'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($d['p_abizenak'] . ', ' . $d['p_izena']); ?></strong></td>
                            <td><small><?php echo htmlspecialchars($d['p_nan']); ?></small></td>
                            <td><?php echo htmlspecialchars($d['dokumentu_izena'] ?: $d['fitxategi_izena']); ?></td>
                            <td><small><?php echo htmlspecialchars($d['deskribapena'] ?: '-'); ?></small></td>
                            <td class="ekintza-botoiak">
                                <a href="../<?php echo htmlspecialchars($d['bidea_zerbitzarian']); ?>" target="_blank" class="botoi-ikonoa" title="Ikusi / Deskargatu">
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
    include_once '../php_includeak/mediku_footer.php';
} else {
    include_once '../php_includeak/harrera_footer.php';
}
?>
