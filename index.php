<?php
$bide_absolutua = '';
$orri_izenburua = "GOsasun - Zure Osasun Ataria";
$uneko_orria = "index";

include 'php_orri_includeak/goiburua.php';
?>

    <main class="hero-sekzioa">
            
        <div class="hero-errenkada">
            <h1><?php echo $itzulpenak->index->izenburua_nagusia; ?></h1>
            <img src="img/png/GOsasun_logoa.png" alt="GOsasun" class="hero-logo-handia">
            
        </div>

       
        <div class="azpititulu-edukiontzia">
            <p class="azpititulua"><?php echo $itzulpenak->index->azpititulua; ?></p>
        </div>

        <!-- Aurkezpen bideoa -->
        <div class="hero-bideo-edukiontzia">
            <video class="hero-bideoa" autoplay muted loop controls poster="img/png/index_pazientea.png">
                <source src="mp4/GOsasun_bideoa.mp4" type="video/mp4">
                Zure brauserrak ez du bideoa onartzen.
            </video>
        </div>

        
        <div class="hero-errenkada portal-sarbideak">
            <!-- Pazientea -->
            <a href="php_orri_hasierakoak/login.php" class="portal-txartela">
                <div class="portal-irudia">
                    <img src="img/png/index_pazientea.png" alt="Pazientea">
                </div>
                <div class="portal-info">
                    <h3><?php echo $itzulpenak->index->paziente_izenburua; ?></h3>
                    <p><?php echo $itzulpenak->index->paziente_testua; ?></p>
                    <span class="botoia botoi-nagusia"><?php echo $itzulpenak->index->sartu; ?></span>
                </div>
            </a>

            <!-- Medikua -->
            <a href="php_orri_hasierakoak/login.php" class="portal-txartela">
                <div class="portal-irudia">
                    <img src="img/png/index_medikua.png" alt="Medikua">
                </div>
                <div class="portal-info">
                    <h3><?php echo $itzulpenak->index->mediku_izenburua; ?></h3>
                    <p><?php echo $itzulpenak->index->mediku_testua; ?></p>
                    <span class="botoia botoi-nagusia"><?php echo $itzulpenak->index->sartu; ?></span>
                </div>
            </a>

            <!-- Harrera -->
            <a href="php_orri_hasierakoak/login.php" class="portal-txartela">
                <div class="portal-irudia">
                    <img src="img/png/index_harrera.png" alt="Harrera">
                </div>
                <div class="portal-info">
                    <h3><?php echo $itzulpenak->index->harrera_izenburua; ?></h3>
                    <p><?php echo $itzulpenak->index->harrera_testua; ?></p>
                    <span class="botoia botoi-nagusia"><?php echo $itzulpenak->index->sartu; ?></span>
                </div>
            </a>
        </div>
    </main>

    <section class="ezaugarriak-wrapper">
        <div class="ezaugarriak-container">
            <div class="ezaugarri-txartela">
                <div class="ezaugarri-ikonoa"><img src="img/svg/line-chart.svg" alt="Neurketa Jarraipena"></div>
                <h3>Neurketa Jarraipena</h3>
                <p>Sartu zure eguneroko osasun datuak (tentsioa, pultsua, pisua, altuera...) eta ikusi bilakaera.</p>
            </div>
            <div class="ezaugarri-txartela">
                <div class="ezaugarri-ikonoa"><img src="img/svg/activity.svg" alt="Grafikak eta Analisia"></div>
                <h3>Grafikak eta Analisia</h3>
                <p>Ikusi zure osasun datuen grafika interaktiboak eta aztertu zure bilakaera modu bisualean.</p>
            </div>
            <div class="ezaugarri-txartela">
                <div class="ezaugarri-ikonoa"><img src="img/svg/file-text.svg" alt="Dokumentuen kudeaketa"></div>
                <h3>Dokumentuen kudeaketa</h3>
                <p>Kudeatu zure txosten medikoak, analizak eta bestelako dokumentu garrantzitsuak modu seguruan.</p>
            </div>
            <div class="ezaugarri-txartela">
                <div class="ezaugarri-ikonoa"><img src="img/svg/calendar.svg" alt="Hitzorduak"></div>
                <h3>Hitzorduak</h3>
                <p>Ikusi zure hurrengo hitzorduak era erraz eta azkar batean.</p>
            </div>
        </div>
    </section>

<?php include 'php_orri_includeak/ezarpenak_modala.php'; ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById("ezarpenakModala");
        var btns = document.querySelectorAll("#irekiEzarpenakModala, #irekiEzarpenakModalaMugikorra");
        var span = document.getElementsByClassName("itxi-modala")[0];

        btns.forEach(function(btn) {
            btn.onclick = function(e) {
                e.preventDefault();
                modal.style.display = "block";
            }
        });

        if (span) {
            span.onclick = function() {
                modal.style.display = "none";
            }
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        <?php if (isset($_GET['ezarpenak_gordeta']) || isset($_GET['ezarpenak_reset'])): ?>
        modal.style.display = "block";
        <?php endif; ?>
    });
    </script>


<?php
$js_gehigarria = ["hasiera_index.js"];
include 'php_orri_includeak/footer.php';
?>

