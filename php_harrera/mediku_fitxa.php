<?php
$bide_absolutua = '../'; session_start();
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_izena'] !== 'Harrera') {
    header("Location: ../php_hasiera/login.php");
    exit;
}

require_once '../php_laguntzaileak/DB_konexioa.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: medikuak.php");
    exit;
}

$id = $_GET['id'];

// 1. Medikuaren datuak lortu
$stmt = $pdo->prepare("SELECT * FROM V_Medikua WHERE mediku_id = ?");
$stmt->execute([$id]);
$medikua = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medikua) {
    echo "Medikua ez da aurkitu.";
    exit;
}

// 2. Hitzorduak lortu (etorkizunekoak eta azkenak)
$stmtH = $pdo->prepare("SELECT h.*, p.izena as p_izena, p.abizenak as p_abizenak 
                        FROM Hitzorduak h 
                        JOIN Pazienteak p ON h.paziente_id = p.id 
                        WHERE h.mediku_id = ? 
                        ORDER BY h.data DESC LIMIT 15");
$stmtH->execute([$id]);
$hitzorduak = $stmtH->fetchAll(PDO::FETCH_ASSOC);

$orri_izenburua = $medikua['izena'] . " " . $medikua['abizenak'] . " - Fitxa";
$uneko_orria = "medikuak";
$css_pertsonalizatua = "medikuak.css";

include_once '../php_includeak/harrera_goiburua.php';
?>



<main class="panel-nagusia">
    <a href="medikuak.php" class="atzera-botoia"><img src="../img/svg/arrow-left.svg" alt="" class="ikono-ertaina marjina-esk-5"> Medikuen zerrendara itzuli</a>
    
    <div class="orri-goiburua">
        <h2><img src="../img/svg/stethoscope.svg" alt="" class="ikono-ertaina marjina-esk-10"> Medikuaren Fitxa</h2>
        <div class="flex-taldea-10">
            <span class="egoera-etiketa <?php echo $medikua['aktibo'] ? 'egoera-alta' : 'egoera-baja'; ?>">
                <?php echo $medikua['aktibo'] ? 'Kontu Aktiboa' : 'Kontu Ez-Aktiboa'; ?>
            </span>
            <a href="hitzorduak.php?filter_mediku_id=<?php echo $medikua['mediku_id']; ?>" class="botoia botoi-nagusia"><img src="../img/svg/calendar-days.svg" alt="" class="ikono-ertaina-zuria marjina-esk-5"> Ikusi Agenda Osoa</a>
        </div>
    </div>

    <div class="fitxa-edukiontzia">
        <!-- Ezkerreko zutabea: Profila eta Oinarrizko Datuak -->
        <div class="profil-txartela">
            <?php 
            $irudia_bide = htmlspecialchars($medikua['irudia'] ?? 'img/lehenetsia_medikua.png');
            if (strpos($irudia_bide, 'img/') === 0 && strpos($irudia_bide, 'img/png/') === false && strpos($irudia_bide, 'img/svg/') === false) {
                $irudia_bide = str_replace('img/', 'img/png/', $irudia_bide);
            }
            ?>
            <img src="../<?php echo $irudia_bide; ?>" alt="Profila" class="profil-irudia-handia">
            <h3 class="marjina-goi-15"><?php echo htmlspecialchars($medikua['izena'] . ' ' . $medikua['abizenak']); ?></h3>
            <div class="espezialitatea-txapa marjina-behe-10"><?php echo htmlspecialchars($medikua['espezialitatea']); ?></div>
            <p class="identifikadorea">ID: #<?php echo $medikua['mediku_id']; ?> | Zkia: <?php echo htmlspecialchars($medikua['elkargokide_zenbakia']); ?></p>
            
            <hr class="banatzaile-marra">
            
            <div class="testua-ezkerrean">
                <p class="betegarria-15"><img src="../img/svg/mail.svg" alt="" class="ikono-ertaina marjina-esk-10"><strong>Email:</strong> <br><span class="testu-normala marjina-ezk-30"><?php echo htmlspecialchars($medikua['email']); ?></span></p>
                <p class="betegarria-15"><img src="../img/svg/phone.svg" alt="" class="ikono-ertaina marjina-esk-10"><strong>Telefonoa:</strong> <br><span class="testu-normala marjina-ezk-30"><?php echo htmlspecialchars($medikua['telefonoa'] ?? 'Ez zehaztua'); ?></span></p>
            </div>
            
            <a href="mediku_editatu.php?id=<?php echo $medikua['mediku_id']; ?>" class="botoia botoi-ertza marjina-goi-zabalera"><img src="../img/svg/pencil.svg" alt="" class="ikono-ertaina marjina-esk-5"> Editatu Datuak</a>
        </div>

        <!-- Eskuineko zutabea: Info Profesionala eta Hitzorduak -->
        <div>
            <!-- Datu Profesionalak -->
            <div class="kutxa-zuria marjina-behe-30">
                <h3 class="izenburu-urdina"><img src="../img/svg/clipboard-pen.svg" alt="" class="ikono-ertaina marjina-esk-10"> Informazio Profesionala</h3>
                <div class="param-sareta">
                    <div class="param-txartela">
                        <div class="testu-gris-iluna">Kontsulta Gela</div>
                        <div class="param-balioa"><?php echo htmlspecialchars($medikua['kontsulta'] ?? '-'); ?></div>
                        <div class="testu-gris-txikia">Solairua / Gela</div>
                    </div>
                    <div class="param-txartela">
                        <div class="testu-gris-iluna">Lanaldia</div>
                        <div class="param-balioa"><?php echo htmlspecialchars($medikua['lanaldia']); ?></div>
                        <div class="testu-gris-txikia">Mota</div>
                    </div>
                    <div class="param-txartela">
                        <div class="testu-gris-iluna">Jaiotze Data</div>
                        <div class="param-balioa"><?php echo date('Y/m/d', strtotime($medikua['jaiotze_data'])); ?></div>
                        <div class="testu-gris-txikia"><?php echo floor((time() - strtotime($medikua['jaiotze_data'])) / 31556926); ?> urte</div>
                    </div>
                    <div class="param-txartela">
                        <div class="testu-gris-iluna">Erabiltzailea Izandakoan</div>
                        <div class="param-balioa"><?php echo date('Y/m/d', strtotime($medikua['sortze_data'])); ?></div>
                        <div class="testu-gris-txikia">Alta data</div>
                    </div>
                </div>
            </div>

            <!-- Hitzorduen historia -->
            <div class="kutxa-zuria-itzala">
                <h3 class="izenburu-iluna"><img src="../img/svg/calendar-days.svg" alt="" class="ikono-ertaina marjina-esk-10"> Azken eta Datozen Hitzorduak</h3>
                
                <?php if(count($hitzorduak) > 0): ?>
                    <div class="hitzordu-zerrenda">
                        <?php foreach($hitzorduak as $h): ?>
                            <?php $class = '';
                                if($h['egoera'] == 'Zain') $class = 'status-zain';
                                elseif($h['egoera'] == 'Bukatuta') $class = 'status-bukatuta';
                                elseif($h['egoera'] == 'Ezeztatuta') $class = 'status-ezeztatuta';
                            ?>
                            <div class="hitzordu-txartela betegarria-15" >
                                <div class="ordu-tartea etiketa-testua" >
                                    <strong><?php echo date('Y/m/d', strtotime($h['data'])); ?></strong><br>
                                    <span class="testu-gris-txikia"><?php echo date('H:i', strtotime($h['hasiera_ordua'])) . ' - ' . date('H:i', strtotime($h['bukaera_ordua'])); ?></span>
                                </div>
                                <div class="hitzordu-xehetasunak">
                                    <h4 class="testu-normala">Paz. <?php echo htmlspecialchars($h['p_abizenak'] . ' ' . $h['p_izena']); ?></h4>
                                    <p class="arrazoia testu-gris-txikia"><?php echo htmlspecialchars($h['arrazoia']); ?></p>
                                </div>
                                <div class="hitzordu-egoera">
                                    <span class="egoera-txapa <?php echo $class; ?>"><?php echo htmlspecialchars($h['egoera']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="egoera-hutsa">
                        <img src="../img/svg/calendar.svg" alt="" class="ikono-handia-3 marjina-behe-20">
                        <p class="testu-gris-etzana">Mediku honek ez du hitzordurik esleituta.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include_once '../php_includeak/harrera_footer.php'; ?>


