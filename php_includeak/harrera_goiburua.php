<?php
if (!isset($page_title)) {
    $page_title = "Harrera - GOsasun";
}
?>
<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <!-- Google-etik deskargatutako estiloak -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/estilo_orokorrak.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/goiburua.css">
    <?php
        $orri_izena = basename($_SERVER['PHP_SELF'], '.php');
        if (isset($custom_css)) {
            $css_fitxategia = $custom_css;
        } else {
            $css_fitxategia = "harrera_" . $orri_izena . ".css";
        }
    ?>
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/<?php echo $css_fitxategia; ?>">
</head>
<body class="<?php echo $body_class ?? 'panel-gorputza'; ?>">
    <header class="panel-goiburua">
        <div class="logoa">
            <a href="index.php" class="logo-esteka">
                <img src="<?php echo $base_path; ?>img/GOsasun_logoa.png" alt="GOsasun" class="logo-irudia">
                <span class="logo-etiketa">Harrera</span>
            </a>
        </div>
        <button class="menu-botoia" aria-label="Ireki menua">☰</button>
        <ul class="nabigazio-estekak">
            <li><a href="index.php" class="<?php echo ($current_page === 'index') ? 'aktiboa' : ''; ?>">Hasiera</a></li>
            <li><a href="pazienteak.php" class="<?php echo ($current_page === 'pazienteak') ? 'aktiboa' : ''; ?>">Pazienteak</a></li>
            <li><a href="medikuak.php" class="<?php echo ($current_page === 'medikuak') ? 'aktiboa' : ''; ?>">Medikuak</a></li>
            <li><a href="hitzorduak.php" class="<?php echo ($current_page === 'hitzorduak') ? 'aktiboa' : ''; ?>">Hitzorduak</a></li>
            <li><a href="mezuak.php" class="<?php echo ($current_page === 'mezuak') ? 'aktiboa' : ''; ?>">Mezuak</a></li>
            <li><a href="kanpoko_mezuak.php" class="<?php echo ($current_page === 'kanpoko_mezuak') ? 'aktiboa' : ''; ?>">Kanpoko Mezuak</a></li>
            <li><a href="harrerako_langileak.php" class="<?php echo ($current_page === 'harrerako_langileak') ? 'aktiboa' : ''; ?>">Harrerako Langileak</a></li>
            <li><a href="<?php echo $base_path; ?>php_laguntzaileak/logout.php" class="botoia botoi-ertza arrisku-kolorea">Saioa Itxi</a></li>
        </ul>
    </header>

