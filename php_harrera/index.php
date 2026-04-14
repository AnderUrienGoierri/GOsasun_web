<?php
// index.php: Harrerako langileen dashboard-a
$bide_absolutua = '../';
session_start();

if (!isset($_SESSION['rol_id']) || $_SESSION['rol_izena'] !== 'Harrera Langilea') {
    header("Location: ../php_hasiera/login.php");
    exit;
}

$orri_izenburua = 'Harrera Dashboard - GOsasun';
$uneko_orria = 'hasiera';
include_once '../php_includeak/harrera_goiburua.php';
?>

<main class="panel-nagusia">
    <div class="orri-goiburua">
        <h2>Ongi etorri, <?php echo htmlspecialchars($_SESSION['erabiltzaile_izena']); ?></h2>
        <p>Hona hemen harrera kudeaketarako sarbide azkarrak:</p>
    </div>

    <div class="funtzio-txartelak">
        <a href="pazienteak.php" class="txartela">
            <img src="../img/svg/users.svg" alt="" class="ikono-handia">
            <h3>Pazienteen Kudeaketa</h3>
            <p>Bilatu, erregistratu edo eguneratu pazienteen datuak.</p>
        </a>
        <a href="hitzorduak.php" class="txartela">
            <img src="../img/svg/calendar.svg" alt="" class="ikono-handia">
            <h3>Hitzorduak</h3>
            <p>Kudeatu zentroko hitzordu guztiak.</p>
        </a>
        <a href="dokumentuak.php" class="txartela">
            <img src="../img/svg/file-text.svg" alt="" class="ikono-handia">
            <h3>Dokumentuak</h3>
            <p>Bilatu eta ikusi pazienteek igotako dokumentuak.</p>
        </a>
    </div>
</main>

<?php include_once '../php_includeak/harrera_footer.php'; ?>
