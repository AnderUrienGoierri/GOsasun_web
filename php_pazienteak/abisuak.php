<?php

$bide_absolutua = '../';
session_start();
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_izena'] !== 'Pazientea') {
    header("Location: ../php_orri_hasierakoak/login.php");
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

// Mark as read logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read_id'])) {
    $abisu_id = $_POST['mark_read_id'];
    foreach ($_SESSION['abisuak'][$paziente_id] as $i => $a) {
        if ($a['abisu_id'] == $abisu_id) {
            $_SESSION['abisuak'][$paziente_id][$i]['irakurrita'] = 1;
            if (!in_array($a['uniq'] ?? '', $abisuak_irakurrita)) {
                $abisuak_irakurrita[] = $a['uniq'] ?? '';
                setcookie($cookie_name, json_encode($abisuak_irakurrita), time() + (10 * 365 * 24 * 60 * 60), "/");
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

include_once '../php_orri_includeak/paziente_goiburua.php';
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

<?php include_once '../php_orri_includeak/paziente_footer.php';
?>


