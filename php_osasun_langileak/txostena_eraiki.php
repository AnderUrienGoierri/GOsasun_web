<?php
// php_osasun_langileak/txostena_eraiki.php
$bide_absolutua = '../'; session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_izena'], ['Osasun Langilea', 'Harrera Langilea'])) {
    header("Location: ../php_orri_hasierakoak/login.php");
    exit;
}

require_once '../php_orri_laguntzaileak/DB_konexioa.php';

$paziente_id = $_GET['paziente_id'] ?? null;
if (!$paziente_id) {
    header("Location: pazienteak.php");
    exit;
}

// Lortu pazientearen datuak goibururako
$stmt = $pdo->prepare("SELECT * FROM V_Pazientea WHERE paziente_id = ?");
$stmt->execute([$paziente_id]);
$pazientea = $stmt->fetch(PDO::FETCH_ASSOC);

$orri_izenburua = "Txostena Eraiki - " . htmlspecialchars($pazientea['izena'] . ' ' . $pazientea['abizenak']);
$uneko_orria = "pazienteak";
$css_pertsonalizatua = "pazienteak.css";

include_once '../php_orri_includeak/osasun_langile_goiburua.php';
?>

<main class="panel-nagusia">
    <div class="orri-goiburua">
        <a href="paziente_info.php?id=<?php echo $paziente_id; ?>" class="atzera-esteka-testua"><img src="../img/svg/arrow-left.svg" alt="" class="ikono-txikia marjina-esk-5"> Itzuli pazientearen fitxara</a>
        <h2>Txosten Kliniko Pertsonalizatua</h2>
        <p>Hautatu txostenean sartu nahi dituzun atalak eta datu-tartea.</p>
    </div>

    <div class="sareta-bikoa-flex marjina-goi-30">
        <!-- Ezkerreko zutabea: Konfigurazioa -->
        <div class="txartel-zuria flex-hazkundea-1">
            <h3 class="izenburu-urdina-txikia"><img src="../img/svg/settings.svg" alt="" class="ikono-ertaina marjina-esk-5"> Parametrizazioa</h3>
            
            <div class="konfigurazio-atala marjina-goi-20">
                <label class="etiketa-lodibitxia">Txostenaren Deskribapena / Izena</label>
                <input type="text" id="txosten_izena" class="inprimaki-kontrola" placeholder="Adib: Jarraipen Klinikoa Ane" value="Paziente Txostena">
            </div>

            <div class="konfigurazio-atala marjina-goi-20">
                <label class="etiketa-lodibitxia">Datu-tartea</label>
                <div class="sareta-bikoa-flex">
                    <div class="inprimaki-taldea flex-1">
                        <label>Hasiera</label>
                        <input type="date" id="txosten_hasiera" class="inprimaki-kontrola">
                    </div>
                    <div class="inprimaki-taldea flex-1">
                        <label>Amaiera</label>
                        <input type="date" id="txosten_amaiera" class="inprimaki-kontrola" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="botoi-taldea-txikia marjina-goi-10">
                    <button class="botoia-preset" onclick="setPresetDate('gaur')">Gaur</button>
                    <button class="botoia-preset" onclick="setPresetDate('astea')">Azken Astea</button>
                    <button class="botoia-preset" onclick="setPresetDate('hilabetea')">Azken Hilabetea</button>
                    <button class="botoia-preset" onclick="setPresetDate('dena')">Dena</button>
                </div>
            </div>

            <div class="konfigurazio-atala marjina-goi-30">
                <label class="etiketa-lodibitxia">Txertatu beharreko atalak</label>
                <div class="aukera-zerrenda">
                    <label class="aukera-etiketa text-primary-darker" style="font-weight: 600; background: #f0f7ff; padding: 10px; border-radius: 6px; display: block; margin-bottom: 5px;">
                        <input type="checkbox" checked disabled> Logotipoa eta Kontaktua (Beti erantsia)
                    </label>
                    <label class="aukera-etiketa text-primary-darker" style="font-weight: 600; background: #f0f7ff; padding: 10px; border-radius: 6px; display: block; margin-bottom: 20px;">
                        <input type="checkbox" checked disabled> Pazientearen Fitxa (Beti erantsia)
                    </label>

                    <label class="aukera-etiketa"><input type="checkbox" name="sections" value="jarraipenak" checked> Jarraipen datuak</label>
                    <label class="aukera-etiketa"><input type="checkbox" name="sections" value="hitzorduak" checked> Hitzorduak</label>
                    <label class="aukera-etiketa"><input type="checkbox" name="sections" value="errezetak"> Errezetak</label>
                    <label class="aukera-etiketa"><input type="checkbox" name="sections" value="grafikak" id="chk_grafikak" onchange="toggleGrafikaOptions()"> Grafikak</label>
                </div>

                <div id="grafika_aukerak" class="ezkutatuta marjina-esk-20 marjina-goi-10" style="padding-left: 25px; border-left: 2px solid var(--primary-color);">
                    <p class="testu-txikia-gris">Hautatu grafikan erakutsi nahi diren balioak:</p>
                    <label class="aukera-etiketa-txikia"><input type="checkbox" name="charts" value="tentsioa" checked> Tentsio Arteriala</label>
                    <label class="aukera-etiketa-txikia"><input type="checkbox" name="charts" value="pisua_kg" checked> Pisua (kg)</label>
                    <label class="aukera-etiketa-txikia"><input type="checkbox" name="charts" value="pultsua_ppm"> Pultsua (ppm)</label>
                    <label class="aukera-etiketa-txikia"><input type="checkbox" name="charts" value="altuera"> Altuera (cm)</label>
                </div>
            </div>

            <div class="akzioak-footer marjina-goi-40">
                <input type="hidden" id="txosten_paziente_id" value="<?php echo $paziente_id; ?>">
                <button class="botoia botoi-nagusia zabal-100" onclick="hasiTxostenSorkuntza()">
                    <img src="../img/svg/file-text.svg" alt="" class="ikono-txikia marjina-esk-5" style="filter: brightness(0) invert(1);"> PDF Sortu eta Gorde
                </button>
            </div>
        </div>

        <!-- Eskuineko zutabea: Status & Aurrebista Info -->
        <div class="txartel-zuria flex-1" style="max-width: 400px;">
            <h3 class="izenburu-urdina-txikia">Sorkuntza Egoera</h3>
            <div id="report_status" class="alerta alerta-info marjina-goi-20" style="display: none;">
                <div class="flex-erdian">
                    <div class="loader-txikia marjina-esk-10"></div>
                    <span id="report_status_text">Prestatzen...</span>
                </div>
            </div>
            
            <div class="informazio-kutxa-lagungarria marjina-goi-20">
                <h4>Garrantzitsua:</h4>
                <ul>
                    <li>PDFa automatikoki gordeko da pazientearen dokumentuen atalean.</li>
                    <li>Grafikak aukeratuz gero, karga denbora pixka bat handituko da.</li>
                    <li>Egiaztatu datu guztiak zuzenak direla inprimatu aurretik.</li>
                </ul>
            </div>

            <div id="hidden_report_container" style="display:none; width: 800px; padding: 40px; background: white; color: black; font-family: 'Inter', sans-serif;">
                <!-- Dinamikoki beteko da JS bidez -->
            </div>
        </div>
    </div>
</main>

<style>
.txartel-zuria { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
.izenburu-urdina-txikia { color: var(--primary-color); display: flex; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px; }
.konfigurazio-atala { border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; }
.etiketa-lodibitxia { font-weight: 600; color: var(--text-base); margin-bottom: 8px; display: block; }
.aukera-etiketa { display: block; margin-bottom: 10px; cursor: pointer; color: var(--text-dark); }
.aukera-etiketa input { margin-right: 10px; transform: scale(1.2); }
.botoia-preset { background: #f1f5f9; border: 1px solid #e2e8f0; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; }
.botoia-preset:hover { background: var(--primary-color); color: white; border-color: var(--primary-color); }
.loader-txikia { border: 3px solid #f3f3f3; border-top: 3px solid var(--primary-color); border-radius: 50%; width: 20px; height: 20px; animation: spin 1s linear infinite; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
.informazio-kutxa-lagungarria { background: #fffcf0; border: 1px solid #fef3c7; padding: 15px; border-radius: 8px; font-size: 0.9rem; color: #92400e; }
.informazio-kutxa-lagungarria ul { margin: 10px 0 0 20px; padding: 0; }
.zabal-100 { width: 100%; justify-content: center; padding: 15px; font-size: 1.1rem; }
.ezkutatuta { display: none; }
.aukera-etiketa-txikia { display: block; margin-bottom: 5px; font-size: 0.9rem; color: #475569; }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../js/txostenak.js"></script>

<?php include_once '../php_orri_includeak/osasun_footer.php'; ?>
