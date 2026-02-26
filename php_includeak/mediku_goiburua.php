<?php
// Badaezpada, sesioa hasita ez badago
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'GOsasun'; ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/estilo_orokorrak.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/goiburua.css">
    <?php
        $orri_izena = basename($_SERVER['PHP_SELF'], '.php');
        if (isset($custom_css)) {
            $css_fitxategia = $custom_css;
        } else {
            $css_fitxategia = "medikua_" . $orri_izena . ".css";
        }
    ?>
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/<?php echo $css_fitxategia; ?>">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="<?php echo $body_class ?? 'panel-gorputza'; ?>">
    <header class="panel-goiburua">
        <div class="logoa">
            <a href="index.php" class="logo-esteka">
                <img src="<?php echo $base_path; ?>img/GOsasun_logoa.png" alt="GOsasun" class="logo-irudia">
                <span class="logo-etiketa">Medikua</span>
            </a>
        </div>
        <button class="menu-botoia" aria-label="Ireki menua">☰</button>
        <ul class="nabigazio-estekak">
            <li><a href="index.php" <?php echo (isset($current_page) && $current_page === 'index') ? 'class="aktiboa"' : ''; ?>>Hasiera</a></li>
            <li><a href="pazienteak.php" <?php echo (isset($current_page) && $current_page === 'pazienteak') ? 'class="aktiboa"' : ''; ?>>Nire Pazienteak</a></li>
            <li><a href="hitzorduak.php" <?php echo (isset($current_page) && $current_page === 'hitzorduak') ? 'class="aktiboa"' : ''; ?>>Hitzorduak</a></li>
            <li><a href="errezetak.php" <?php echo (isset($current_page) && $current_page === 'errezetak') ? 'class="aktiboa"' : ''; ?>>Errezetak</a></li>
            <li><a href="grafikak.php" <?php echo (isset($current_page) && $current_page === 'grafikak') ? 'class="aktiboa"' : ''; ?>>Grafikak</a></li>
            <li><a href="mezuak.php" <?php echo (isset($current_page) && $current_page === 'mezuak') ? 'class="aktiboa"' : ''; ?>>Mezuak</a></li>
            <li><a href="abisuak.php" <?php echo (isset($current_page) && $current_page === 'abisuak') ? 'class="aktiboa"' : ''; ?>>Abisuak</a></li>
            <li><a href="<?php echo $base_path; ?>php_laguntzaileak/logout.php" class="botoia botoi-ertza arrisku-kolorea" >Saioa Itxi</a></li>
        </ul>
    </header>

