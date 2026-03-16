<?php
session_start();
$bide_absolutua = '../';
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_izena'] !== 'Pazientea') {
    header("Location: ../php_hasiera/login.php");
    exit;
}

require_once '../php_laguntzaileak/DB_konexioa.php';

$orri_izenburua = "Ezarpenak - GOsasun";
$uneko_orria = "ezarpenak";

include_once '../php_includeak/paziente_goiburua.php';

require_once '../php_includeak/konfigurazioa_kargatu.php';
$konf = kargatuKonfigurazioa(false);

$hizkuntza_def = $konf['hizkuntza'];
$kolore_nagusia_def = $konf['kolore_nagusia'];
$bigarren_kolorea_def = $konf['bigarren_kolorea'];
$gaia_def = $konf['gaia'];
?>

<main class="panel-nagusia">
    <div class="orri-goiburua">
        <h1><img src="../img/settings.svg" alt="" class="ikono-32px-erdian"> Ezarpenak</h1>
        <a href="index.php" class="botoia botoi-itsua"><img src="../img/arrow-left.svg" alt="" class="ikono-16px-erdian"> Itzuli</a>
    </div>

    <section class="ezarpen-panela-wrapper edukiontzi-ertaina">
        <div class="kutxa-zuria ertz-lodi-urdina">
            <div class="testua-erdian-marjina-behe-20">
                <p class="testu-grisa">Hemen zure interfazearen itxura aldatu dezakezu.</p>
                <?php if (isset($_GET['ezarpenak_gordeta'])): ?>
                    <div class="alerta alerta-arrakasta marjina-goi-15">Ezarpenak XML fitxategian gorde dira!</div>
                <?php endif; ?>
                <?php if (isset($_GET['ezarpenak_reset'])): ?>
                    <div class="alerta alerta-arrakasta marjina-goi-15">Hasierako baloreak berreskuratu dira!</div>
                <?php endif; ?>
            </div>
            <form action="../php_laguntzaileak/ezarpenak_gorde.php" method="POST">
                <!-- Ezarpen orokorretarako (itxura) -->
                <input type="hidden" name="form_type" value="orokorra">
                <input type="hidden" name="itzulera" value="pazientea">

                <div class="inprimaki-taldea">
                    <label>Defektuzko hizkuntza:</label>
                    <select name="hizkuntza" class="inprimaki-kontrola">
                        <option value="eu" <?php echo ($hizkuntza_def === 'eu') ? 'selected' : ''; ?>>Euskara</option>
                        <option value="es" <?php echo ($hizkuntza_def === 'es') ? 'selected' : ''; ?>>Castellano</option>
                        <option value="en" <?php echo ($hizkuntza_def === 'en') ? 'selected' : ''; ?>>English</option>
                    </select>
                </div>

                <div class="inprimaki-taldea">
                    <label>Kolore nagusia:</label>
                    <input type="color" name="kolore_nagusia" value="<?php echo htmlspecialchars($kolore_nagusia_def); ?>" class="inprimaki-kontrola sarrera-altuera-50">
                </div>

                <div class="inprimaki-taldea">
                    <label>Bigarren mailako kolorea:</label>
                    <input type="color" name="bigarren_kolorea" value="<?php echo htmlspecialchars($bigarren_kolorea_def); ?>" class="inprimaki-kontrola sarrera-altuera-50">
                </div>

                <div class="inprimaki-taldea">
                    <label>Footer kolorea:</label>
                    <input type="color" name="footer_kolorea" value="<?php echo htmlspecialchars($footer_kolorea_def); ?>" class="inprimaki-kontrola sarrera-altuera-50">
                </div>

                <div class="inprimaki-taldea">
                    <label>Aplikazioaren Itxura (Gaia):</label>
                    <select name="gaia" class="inprimaki-kontrola">
                        <option value="argia" <?php echo ($gaia_def === 'argia') ? 'selected' : ''; ?>>Argia (Mahaigaina klasikoa)</option>
                        <option value="iluna" <?php echo ($gaia_def === 'iluna') ? 'selected' : ''; ?>>Iluna (Modo oscuro)</option>
                    </select>
                </div>

                <div class="testua-erdian-marjina-goi-30">
                    <button type="submit" class="botoia botoi-nagusia zabalera-osoa-300px">Gorde Ezarpenak (XML-an)</button>
                </div>
            </form>

            <form action="../php_laguntzaileak/ezarpenak_gorde.php" method="POST" class="marjina-goi-15">
                <input type="hidden" name="ekintza" value="reset">
                <input type="hidden" name="itzulera" value="pazientea">
                <div class="testua-erdian">
                    <button type="submit" class="botoia botoi-itsua-gorria" onclick="return confirm('Ziur zaude zure ezarpenak ezabatu eta hasierakoak berreskuratu nahi dituzula?')">Berreskuratu hasierako baloreak</button>
                </div>
            </form>
        </div>
    </section>
</main>

<?php
include_once '../php_includeak/paziente_footer.php';
?>
