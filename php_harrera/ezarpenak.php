<?php
session_start();
$bide_absolutua = '../';
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_izena'] !== 'Harrera') {
    header("Location: ../php_hasiera/login.php");
    exit;
}

require_once '../php_laguntzaileak/DB_konexioa.php';

$orri_izenburua = "Ezarpenak - GOsasun";
$uneko_orria = "ezarpenak";

include_once '../php_includeak/harrera_goiburua.php';

require_once '../php_includeak/konfigurazioa_kargatu.php';
$konf = kargatuKonfigurazioa(false);

$sistema_izena_def = $konf['sistema_izena'];
$hitzordu_muga_def = $konf['hitzordu_muga'];
$ordutegia_ireki_def = $konf['ordutegia_ireki'];
$ordutegia_itxi_def = $konf['ordutegia_itxi'];
$mantenimendua_def = $konf['mantenimendua'];
?>

<main class="panel-nagusia">
    <div class="orri-goiburua">
        <h1><img src="../img/settings.svg" alt="" class="ikono-32px-erdian"> Osasun Zentroaren Ezarpenak</h1>
        <a href="index.php" class="botoia botoi-itsua"><img src="../img/arrow-left.svg" alt="" class="ikono-16px-erdian"> Itzuli</a>
    </div>

    <section class="ezarpen-panela-wrapper edukiontzi-ertaina">
        <div class="kutxa-zuria ertz-lodi-urdina">
            <div class="testua-erdian-marjina-behe-20">
                <p class="testu-grisa">Hemen web-aren logika aplikatiboa eta zentroaren informazioa konfiguratu ditzakezu XML fitxategian.</p>
                <?php if (isset($_GET['ezarpenak_gordeta'])): ?>
                    <div class="alerta alerta-arrakasta marjina-goi-15">Ezarpenak XML fitxategian gorde dira!</div>
                <?php endif; ?>
                <?php if (isset($_GET['ezarpenak_reset'])): ?>
                    <div class="alerta alerta-arrakasta marjina-goi-15">Hasierako baloreak berreskuratu dira!</div>
                <?php endif; ?>
            </div>
            <form action="../php_laguntzaileak/ezarpenak_gorde.php" method="POST">
                <input type="hidden" name="form_type" value="osasun_zentroa">

                <div class="inprimaki-taldea">
                    <label>Sistemaren Izena:</label>
                    <input type="text" name="sistema_izena" value="<?php echo htmlspecialchars($sistema_izena_def); ?>" class="inprimaki-kontrola">
                </div>

                <div class="inprimaki-taldea">
                    <label>Mediku bakoitzeko gehienezko hitzorduak (eguneko):</label>
                    <input type="number" name="hitzordu_muga" value="<?php echo htmlspecialchars($hitzordu_muga_def); ?>" min="1" max="50" class="inprimaki-kontrola">
                </div>

                <div class="flex-15px-tartea">
                    <div class="inprimaki-taldea flex-hazkundea-1">
                        <label>Zentroa irekitzeko ordua:</label>
                        <input type="time" name="ordutegia_ireki" value="<?php echo htmlspecialchars($ordutegia_ireki_def); ?>" class="inprimaki-kontrola">
                    </div>
                    <div class="inprimaki-taldea flex-hazkundea-1">
                        <label>Zentroa ixteko ordua:</label>
                        <input type="time" name="ordutegia_itxi" value="<?php echo htmlspecialchars($ordutegia_itxi_def); ?>" class="inprimaki-kontrola">
                    </div>
                </div>

                <div class="inprimaki-taldea flex-erdian hutsartea-10 marjina-goi-10">
                    <input type="checkbox" name="mantenimendua" id="mantenimendua" value="bai" <?php echo ($mantenimendua_def === 'bai') ? 'checked' : ''; ?> class="checkbox-20px">
                    <label for="mantenimendua" class="marjina-behe-0">Mantenimendu Modua (bezeroei sarbidea itxi batzuetan erabiltzeko prestatu)</label>
                </div>

                <div class="testua-erdian-marjina-goi-30">
                    <button type="submit" class="botoia botoi-nagusia zabalera-osoa-300px">Gorde Ezarpenak (XML-an)</button>
                </div>
            </form>

            <form action="../php_laguntzaileak/ezarpenak_gorde.php" method="POST" class="marjina-goi-15">
                <input type="hidden" name="ekintza" value="reset">
                <input type="hidden" name="form_type" value="osasun_zentroa">
                <input type="hidden" name="itzulera" value="harrera">
                <div class="testua-erdian">
                    <button type="submit" class="botoia botoi-itsua-gorria" onclick="return confirm('Ziur zaude osasun zentroaren ezarpenak leheneratu nahi dituzula?')">Berreskuratu hasierako baloreak</button>
                </div>
            </form>
        </div>
    </section>
</main>

<?php
include_once '../php_includeak/harrera_footer.php';
?>
