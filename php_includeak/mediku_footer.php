    <footer>
        <link rel="stylesheet" href="<?php echo $base_path; ?>css/footer.css">
        <div class="footer-logo-container">
            <img src="<?php echo $base_path; ?>img/GOsasun_logoa.png" alt="GOsasun Logo" class="footer-logo">
        </div>
        <div class="orri-oin-nabigazioa">
            <a href="hitzorduak.php">Hitzorduak</a> | 
            <a href="errezetak.php">Errezetak</a> | 
            <a href="grafikak.php">Grafikak</a> | 
            <a href="mezuak.php">Mezuak</a> | 
            <a href="abisuak.php">Abisuak</a> | 
            <a href="<?php echo $base_path; ?>php_laguntzaileak/logout.php">Saioa Itxi</a>
        </div>
        <p>&copy; 2026 GOsasun</p>
    </footer>

    <!-- JS -->
    <script src="<?php echo $base_path; ?>js/common.js"></script>
    <?php if (isset($extra_js)): ?>
        <?php foreach ($extra_js as $js): ?>
            <script src="<?php echo $base_path; ?>js/<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

