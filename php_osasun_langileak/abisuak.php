<?php
// php_osasun_langileak/abisuak.php
$bide_absolutua = '../';
session_start();
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_izena'] !== 'Osasun Langilea') {
    header("Location: ../php_orri_hasierakoak/login.php");
    exit;
}

require_once '../php_orri_laguntzaileak/DB_konexioa.php';
$langile_id = $_SESSION['erabiltzaile_id'];
$mezua = '';

// Irakurrita jarraipenak cookie-tik berreskuratu
$cookie_name = 'abisu_irakurrita_jarraipenak';
$irakurritako_ids = [];
if (isset($_COOKIE[$cookie_name])) {
    $irakurritako_ids = json_decode($_COOKIE[$cookie_name], true) ?: [];
}

// Mark as read logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read_id'])) {
    $j_id = intval($_POST['mark_read_id']);
    if (!in_array($j_id, $irakurritako_ids)) {
        $irakurritako_ids[] = $j_id;
        // Gorde cookie-a 30 egunetarako
        setcookie($cookie_name, json_encode($irakurritako_ids), time() + (30 * 24 * 60 * 60), "/");
        $mezua = "Abisua irakurrita gisa markatu da.";
    }
}

// 1. Lortu azken 15 egunetako jarraipenak (langilearen pazienteenak)
// Hemen detektatu ditzakegu "ezohikoak" dinamikoki
$muga_data = date('Y-m-d H:i:s', strtotime('-15 days'));
$stmt = $pdo->prepare("
    SELECT j.*, p.izena, p.abizenak, p.nan
    FROM jarraipenak j
    JOIN V_Langile_Pazienteak vlp ON j.paziente_id = vlp.paziente_id
    JOIN V_Pazientea p ON j.paziente_id = p.paziente_id
    WHERE vlp.langile_id = ? AND j.erregistro_data >= ?
    ORDER BY j.erregistro_data DESC
");
$stmt->execute([$langile_id, $muga_data]);
$dena = $stmt->fetchAll(PDO::FETCH_ASSOC);

$abisuak = [];
foreach ($dena as $j) {
    $detektatu = false;
    $arrazoiak = [];
    
    // Logika dinamikoa threshold-ekin
    if ($j['tentsio_sistolikoa'] > 140) $arrazoiak[] = "Tentsio sistolikoa altua (" . $j['tentsio_sistolikoa'] . ")";
    if ($j['tentsio_sistolikoa'] > 0 && $j['tentsio_sistolikoa'] < 90) $arrazoiak[] = "Tentsio sistolikoa baxua (" . $j['tentsio_sistolikoa'] . ")";
    
    if ($j['tentsio_diastolikoa'] > 90) $arrazoiak[] = "Tentsio diastolikoa altua (" . $j['tentsio_diastolikoa'] . ")";
    if ($j['tentsio_diastolikoa'] > 0 && $j['tentsio_diastolikoa'] < 60) $arrazoiak[] = "Tentsio diastolikoa baxua (" . $j['tentsio_diastolikoa'] . ")";
    
    if ($j['pultsua_ppm'] > 100) $arrazoiak[] = "Pultsu bizia (" . $j['pultsua_ppm'] . " ppm)";
    if ($j['pultsua_ppm'] > 0 && $j['pultsua_ppm'] < 50) $arrazoiak[] = "Pultsu geldoa (" . $j['pultsua_ppm'] . " ppm)";

    if (!empty($arrazoiak)) {
        $is_read = in_array($j['id'], $irakurritako_ids);
        
        // Soilik gehitu irakurri gabekoak (Erabiltzailearen eskaeraren arabera)
        if (!$is_read) {
            $abisuak[] = [
                'id' => $j['id'],
                'pazientea' => $j['izena'] . " " . $j['abizenak'],
                'paziente_id' => $j['paziente_id'],
                'data' => $j['erregistro_data'],
                'deskribapena' => implode(", ", $arrazoiak),
                'irakurrita' => false
            ];
        }
    }
}

$orri_izenburua = "Abisuak - GOsasun";
$uneko_orria = "abisuak";
$css_pertsonalizatua = "abisuak_jarraipenak.css";

include_once '../php_orri_includeak/osasun_langile_goiburua.php';
?>

    <main class="panel-nagusia">
        <div class="orri-goiburua">
            <div>
                <h2><img src="../img/svg/bell-ring.svg" alt="" class="ikono-ertaina marjina-esk-5"> Osasun Abisuak</h2>
                <p>Zure pazienteen neurketa ez-ohikoen zerrenda. Berrikusi eta jarraitu kasu bakoitza.</p>
            </div>
        </div>

        <?php if ($mezua): ?>
            <div class="alerta alerta-arrakasta"><?php echo htmlspecialchars($mezua); ?></div>
        <?php endif; ?>

        <div class="abisu-zerrenda">
            <?php if (count($abisuak) > 0): ?>
                <?php foreach ($abisuak as $a): ?>
                    <div class="abisu-txartela-medikua abisua-berria">
                        <div class="abisua-header">
                            <div>
                                <span class="etiketa etiketa-gorria">Berria</span>
                                <strong class="marjina-ezk-10"><?php echo htmlspecialchars($a['pazientea']); ?></strong>
                            </div>
                            <span class="testu-gris-txikia"><?php echo date('Y/m/d H:i', strtotime($a['data'])); ?></span>
                        </div>
                        <div class="abisua-gorputza">
                            <p class="marjina-behe-15"><?php echo htmlspecialchars($a['deskribapena']); ?></p>
                            <div class="flex-tartea-10">
                                <a href="jarraipenak.php?paziente_id=<?php echo $a['paziente_id']; ?>" class="botoia botoi-ertza-txikia">
                                    <img src="../img/svg/search.svg" alt="" class="ikono-txikia marjina-esk-5"> Xehetasunak
                                </a>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="mark_read_id" value="<?php echo $a['id']; ?>">
                                    <button type="submit" class="botoia botoi-nagusia-txikia">Irakurrita markatu</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="testua-erdian hutsartea-100 opazitatea-50">
                    <img src="../img/svg/check-circle.svg" alt="" class="ikono-handia-48 marjina-behe-15">
                    <p>Ez dago abisu berririk. Zure pazienteen neurketa guztiak normaltasunaren barruan daude.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php include_once '../php_orri_includeak/osasun_footer.php'; ?>
