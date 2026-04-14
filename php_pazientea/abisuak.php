<?php

$bide_absolutua = '../';
session_start();
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_izena'] !== 'Pazientea') {
    header("Location: ../php_hasiera/login.php");
    exit;
}

$paziente_id = $_SESSION['erabiltzaile_id'];
$mezua = '';
if (!isset($_SESSION['abisuak'])) $_SESSION['abisuak'] = [];
if (!isset($_SESSION['abisuak'][$paziente_id])) $_SESSION['abisuak'][$paziente_id] = [];
if (!isset($_SESSION['abisuak_irakurrita'])) $_SESSION['abisuak_irakurrita'] = [];
if (!isset($_SESSION['abisuak_irakurrita'][$paziente_id])) $_SESSION['abisuak_irakurrita'][$paziente_id] = [];

// Irakurrita abisuak cookie-tik berreskuratu
$cookie_name = 'abisuak_irakurrita_' . $paziente_id;
$abisuak_irakurrita = [];
if (isset($_COOKIE[$cookie_name])) {
    $abisuak_irakurrita = json_decode($_COOKIE[$cookie_name], true) ?: [];
}

// --- Gehitu: Neurketa ezohikoak detektatu eta abisuak sortu (bakarrik ez badaude jada) ---
require_once '../php_laguntzaileak/DB_konexioa.php';
$stmt = $pdo->prepare("SELECT erregistro_data, tentsio_sistolikoa, tentsio_diastolikoa, pisua_kg, altuera, pultsua_ppm FROM jarraipenak WHERE paziente_id = ? ORDER BY erregistro_data DESC");
$stmt->execute([$paziente_id]);
$jarraipenak = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Balio ezohikoen mugak
$mugak = [
    'tentsio_sistolikoa' => ['min' => 90, 'max' => 140],
    'tentsio_diastolikoa' => ['min' => 60, 'max' => 90],
    'pultsua_ppm' => ['min' => 50, 'max' => 100],
    'pisua_kg' => ['min' => 45, 'max' => 150],
];

foreach ($jarraipenak as $n) {
    // Sistolikoa
    if (isset($n['tentsio_sistolikoa']) && $n['tentsio_sistolikoa'] !== null && ($n['tentsio_sistolikoa'] < $mugak['tentsio_sistolikoa']['min'] || $n['tentsio_sistolikoa'] > $mugak['tentsio_sistolikoa']['max'])) {
        $testua = 'Tentsio sistolikoa ezohikoa: ' . $n['tentsio_sistolikoa'] . ' mmHg';
        $uniq = md5('sist' . $n['erregistro_data'] . $n['tentsio_sistolikoa']);
        $already = false;
        if (in_array($uniq, $abisuak_irakurrita)) $already = true;
        foreach ($_SESSION['abisuak'][$paziente_id] as $a) {
            if (($a['uniq'] ?? '') === $uniq) { $already = true; break; }
        }
        if (!$already) {
            $_SESSION['abisuak'][$paziente_id][] = [
                'abisu_id' => uniqid('abisu_'),
                'uniq' => $uniq,
                'paziente_id' => $paziente_id,
                'data' => $n['erregistro_data'],
                'mota' => 'Tentsioa',
                'testua' => $testua,
                'irakurrita' => 0
            ];
        }
    }
    // Diastolikoa
    if (isset($n['tentsio_diastolikoa']) && $n['tentsio_diastolikoa'] !== null && ($n['tentsio_diastolikoa'] < $mugak['tentsio_diastolikoa']['min'] || $n['tentsio_diastolikoa'] > $mugak['tentsio_diastolikoa']['max'])) {
        $testua = 'Tentsio diastolikoa ezohikoa: ' . $n['tentsio_diastolikoa'] . ' mmHg';
        $uniq = md5('diast' . $n['erregistro_data'] . $n['tentsio_diastolikoa']);
        $already = false;
        if (in_array($uniq, $abisuak_irakurrita)) $already = true;
        foreach ($_SESSION['abisuak'][$paziente_id] as $a) {
            if (($a['uniq'] ?? '') === $uniq) { $already = true; break; }
        }
        if (!$already) {
            $_SESSION['abisuak'][$paziente_id][] = [
                'abisu_id' => uniqid('abisu_'),
                'uniq' => $uniq,
                'paziente_id' => $paziente_id,
                'data' => $n['erregistro_data'],
                'mota' => 'Tentsioa',
                'testua' => $testua,
                'irakurrita' => 0
            ];
        }
    }
    // Pultsua
    if (isset($n['pultsua_ppm']) && $n['pultsua_ppm'] !== null && ($n['pultsua_ppm'] < $mugak['pultsua_ppm']['min'] || $n['pultsua_ppm'] > $mugak['pultsua_ppm']['max'])) {
        $testua = 'Pultsua ezohikoa: ' . $n['pultsua_ppm'] . ' ppm';
        $uniq = md5('pultsua' . $n['erregistro_data'] . $n['pultsua_ppm']);
        $already = false;
        if (in_array($uniq, $abisuak_irakurrita)) $already = true;
        foreach ($_SESSION['abisuak'][$paziente_id] as $a) {
            if (($a['uniq'] ?? '') === $uniq) { $already = true; break; }
        }
        if (!$already) {
            $_SESSION['abisuak'][$paziente_id][] = [
                'abisu_id' => uniqid('abisu_'),
                'uniq' => $uniq,
                'paziente_id' => $paziente_id,
                'data' => $n['erregistro_data'],
                'mota' => 'Pultsua',
                'testua' => $testua,
                'irakurrita' => 0
            ];
        }
    }
    // Pisua
    if (isset($n['pisua_kg']) && $n['pisua_kg'] !== null && ($n['pisua_kg'] < $mugak['pisua_kg']['min'] || $n['pisua_kg'] > $mugak['pisua_kg']['max'])) {
        $testua = 'Pisua ezohikoa: ' . $n['pisua_kg'] . ' kg';
        $uniq = md5('pisua' . $n['erregistro_data'] . $n['pisua_kg']);
        $already = false;
        if (in_array($uniq, $abisuak_irakurrita)) $already = true;
        foreach ($_SESSION['abisuak'][$paziente_id] as $a) {
            if (($a['uniq'] ?? '') === $uniq) { $already = true; break; }
        }
        if (!$already) {
            $_SESSION['abisuak'][$paziente_id][] = [
                'abisu_id' => uniqid('abisu_'),
                'uniq' => $uniq,
                'paziente_id' => $paziente_id,
                'data' => $n['erregistro_data'],
                'mota' => 'Pisua',
                'testua' => $testua,
                'irakurrita' => 0
            ];
        }
    }
}

// Mark as read logic (irakurri gisa markatu POST bidez)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read_id'])) {
    $abisu_id = $_POST['mark_read_id'];
    foreach ($_SESSION['abisuak'][$paziente_id] as $i => $a) {
        if ($a['abisu_id'] == $abisu_id) {
            $_SESSION['abisuak'][$paziente_id][$i]['irakurrita'] = 1;
            // Irakurrita gisa markatutako abisuaren uniq balioa cookie-an gorde
            if (!in_array($a['uniq'], $abisuak_irakurrita)) {
                $abisuak_irakurrita[] = $a['uniq'];
                setcookie($cookie_name, json_encode($abisuak_irakurrita), time() + (10 * 365 * 24 * 60 * 60), "/"); // 10 urteko cookie iraunkorra
            }
            $mezua = "Abisua irakurrita markatu da.";
            break;
        }
    }
}

// JSON API eskaera (abisuak_logika.js-ek erabiltzeko)
if (isset($_GET['json']) && $_GET['json'] == 1) {
    header('Content-Type: application/json');
    echo json_encode(['abisuak' => array_values($_SESSION['abisuak'][$paziente_id])]);
    exit;
}

$abisuak = array_values($_SESSION['abisuak'][$paziente_id]);

?>
<?php $orri_izenburua = "Abisuak - GOsasun";
$uneko_orria = "abisuak";
$css_pertsonalizatua = "abisuak.css";

include_once '../php_includeak/paziente_goiburua.php';
?>


    <main class="panel-nagusia">
        <div class="orri-goiburua">
            <h2><img src="../img/svg/bell-ring.svg" alt="" class="ikono-ertaina marjina-esk-5"> Osasun Abisuak</h2>
            <p>Zure neurketen araberako abisu automatikoak.</p>
        </div>

        <?php if ($mezua): ?>
            <div class="alerta alerta-arrakasta"><?php echo htmlspecialchars($mezua); ?></div>
        <?php endif; ?>

        <div class="abisu-zerrenda">
            <?php if (count($abisuak) > 0): ?>
                <?php foreach ($abisuak as $a): ?>
                    <div class="abisu-txartela <?php echo !empty($a['irakurrita']) ? '' : 'ez-irakurrita'; ?>">
                        <h4>
                            <span>
                                <span class="abisu-mota mota-<?php echo isset($a['mota']) ? strtolower($a['mota']) : 'ezezaguna'; ?>">
                                    <?php echo isset($a['mota']) ? htmlspecialchars($a['mota']) : '-'; ?>
                                </span>
                                <?php echo !empty($a['irakurrita']) ? '' : '<span class="testu-arriskua-ezk"><img src="../img/svg/alert.svg" alt="" class="ikono-14px-erdian"> Berria</span>'; ?>
                            </span>
                            <?php if (empty($a['irakurrita'])): ?>
                                <form method="POST" class="barneko-bistarapena">
                                    <input type="hidden" name="mark_read_id" value="<?php echo isset($a['abisu_id']) ? $a['abisu_id'] : ''; ?>">
                                    <button type="submit" class="irakurri-botoia">Markatu irakurrita gisa</button>
                                </form>
                            <?php endif; ?>
                        </h4>
                        <p><?php echo isset($a['testua']) ? htmlspecialchars($a['testua']) : '-'; ?></p>
                        <span class="abisu-data"><img src="../img/svg/calendar-days.svg" alt="" class="ikono-ertaina marjina-esk-5"> <?php echo isset($a['data']) ? date('Y/m/d H:i', strtotime($a['data'])) : '-'; ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="egoera-hutsa kutxa-hutsa-40" >
                    <div class="ikono-handia-3"><img src="../img/svg/smile.svg" alt="" class="ikono-ertaina marjina-esk-5"></div>
                    <h3>Ez duzu abisurik!</h3>
                    <p>Zure neurketa guztiak normaltasunaren barruan daude une honetan.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php include_once '../php_includeak/paziente_footer.php';
?>


