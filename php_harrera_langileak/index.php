<?php
// index.php: Harrerako langileen dashboard-a
$bide_absolutua = '../';
session_start();

if (!isset($_SESSION['rol_id']) || $_SESSION['rol_izena'] !== 'Harrera Langilea') {
    header("Location: ../php_orri_hasierakoak/login.php");
    exit;
}

$orri_izenburua = 'Harrera Dashboard - GOsasun';
$uneko_orria = 'hasiera';
include_once '../php_orri_includeak/harrera_goiburua.php';
?>

<?php
require_once '../php_orri_laguntzaileak/DB_konexioa.php';
$erabiltzaile_id = $_SESSION['erabiltzaile_id'];

// Harrerako langilearen datuak lortu
$stmt = $pdo->prepare("SELECT * FROM V_Harrera WHERE langile_id = ?");
$stmt->execute([$erabiltzaile_id]);
$erabiltzaile_datuak = $stmt->fetch(PDO::FETCH_ASSOC);

// Paziente totalak
$stmtPaziente = $pdo->query("SELECT COUNT(*) FROM Pazienteak");
$pazienteKopurua = $stmtPaziente->fetchColumn();

// Gaurko hitzorduak (orokorra)
$gaur = date('Y-m-d');
$stmtHitzordu = $pdo->prepare("SELECT COUNT(*) FROM Hitzorduak WHERE data = ? AND egoera = 'Zain'");
$stmtHitzordu->execute([$gaur]);
$gaurkoHitzorduak = $stmtHitzordu->fetchColumn();

// Langile kopurua (Osasun Langileak)
$stmtLangile = $pdo->query("SELECT COUNT(*) FROM osasun_langileak");
$langileKopurua = $stmtLangile->fetchColumn();
?>
<main class="panel-nagusia">
    <section class="kaixo-atalak flex-zentratua-20" >
        <?php 
        $irudia_bide = htmlspecialchars($erabiltzaile_datuak['irudia'] ?? 'img/lehenetsia.png');
        if (strpos($irudia_bide, 'img/') === 0 && strpos($irudia_bide, 'img/png/') === false && strpos($irudia_bide, 'img/svg/') === false) {
            $irudia_bide = str_replace('img/', 'img/png/', $irudia_bide);
        }
        ?>
        <img src="../<?php echo $irudia_bide; ?>" alt="Zure profila" class="profil-irudia-80">
        <div>
            <h1 class="izenburu-nagusia"><?php echo $itzulpenak->erabiltzaile_panela->kaixo ?? 'Kaixo'; ?>, <?php echo htmlspecialchars($_SESSION['erabiltzaile_izena']); ?>!</h1>
            <p class="azpititulu-grisa">Zentroko kudeaketa orokorra eta harrera</p>
        </div>
    </section>

    <!-- Estatistika Txartelak (Dashboard) -->
    <div class="panel-sareta marjina-behe-30 aginte-panel-sareta">
        <div class="kutxa-zuria-itzala testua-erdian">
            <div class="ikono-kutxa-handia"><img src="../img/svg/calendar-days.svg" alt="" class="ikono-ertaina marjina-esk-5"></div>
            <h3 class="zenbaki-handi-urdina"><?php echo $gaurkoHitzorduak; ?></h3>
            <p class="azpititulu-grisa">Gaurko Hitzorduak</p>
            <a href="hitzorduak.php" class="botoia botoi-ertza marjina-goi-15 zabalera-osoa">Agenda Kudeatu</a>
        </div>
        
        <div class="kutxa-zuria-itzala testua-erdian">
            <div class="ikono-kutxa-handia"><img src="../img/svg/file-text.svg"  alt="" class="ikono-ertaina marjina-esk-5"></div>
            <h3 class="zenbaki-handi-urdina"><?php echo $langileKopurua; ?></h3>
            <p class="azpititulu-grisa">Dokumentuak</p>
            <a href="dokumentuak.php" class="botoia botoi-ertza marjina-goi-15 zabalera-osoa">Dokumentuak Kudeatu</a>
        </div>
    </div>

    <h2 class="izenburu-nagusia marjina-behe-20"><img src="../img/svg/zap.svg" alt="" class="ikono-ertaina marjina-esk-5"> Ekintza Azkarrak</h2>
    <section class="menu-sareta">
        <a href="pazienteak.php" class="menu-txartela">
            <div class="txartel-ikonoa"><img src="../img/svg/users.svg" alt="Pazienteen Kudeaketa" class="ikono-handia-48"></div>
            <h3>Pazienteen Kudeaketa</h3>
            <p>Bilatu, erregistratu edo eguneratu pazienteen datuak.</p>
        </a>
        <a href="hitzorduak.php" class="menu-txartela">
            <div class="txartel-ikonoa"><img src="../img/svg/calendar.svg" alt="Hitzorduak" class="ikono-handia-48"></div>
            <h3>Hitzorduak</h3>
            <p>Kudeatu zentroko hitzordu guztiak eta esleipenak.</p>
        </a>
        <a href="dokumentuak.php" class="menu-txartela">
            <div class="txartel-ikonoa"><img src="../img/svg/file-text.svg" alt="Dokumentuak" class="ikono-handia-48"></div>
            <h3>Dokumentuak</h3>
            <p>Bilatu eta ikusi pazienteek igotako dokumentuak.</p>
        </a>
        <a href="osasun_langileak.php" class="menu-txartela">
            <div class="txartel-ikonoa"><img src="../img/svg/stethoscope.svg" alt="Osasun Langileak" class="ikono-handia-48"></div>
            <h3>Osasun Langileak</h3>
            <p>Zentroko mediku eta langileen zerrenda eta kudeaketa.</p>
        </a>
        <a href="ezarpenak.php" class="menu-txartela">
            <div class="txartel-ikonoa"><img src="../img/svg/settings.svg" alt="Ezarpenak" class="ikono-handia-48"></div>
            <h3>Ezarpenak</h3>
            <p>Pertsonalizatu webgunearen itxura (hizkuntza, kolorea...).</p>
        </a>
        <a href="../php_orri_laguntzaileak/logout.php" class="menu-txartela">
            <div class="txartel-ikonoa"><img src="../img/svg/log-out.svg" alt="Saioa Itxi" class="ikono-handia-48"></div>
            <h3>Saioa Itxi</h3>
            <p>Itxi zure saioa eta irten sistematik modu seguruan.</p>
        </a>
    </section>
</main>

<?php include_once '../php_orri_includeak/harrera_footer.php'; ?>
