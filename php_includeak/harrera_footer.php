    <footer>
        <link rel="stylesheet" href="<?php echo $base_path; ?>css/footer.css">
        <div class="footer-logo-container">
            <img src="<?php echo $base_path; ?>img/GOsasun_logoa.png" alt="GOsasun Logo" class="footer-logo">
        </div>
        <div class="orri-oin-nabigazioa">
            <a href="index.php">Hasiera</a>
            <a href="pazienteak.php">Pazienteak</a>
            <a href="medikuak.php">Medikuak</a>
            <a href="hitzorduak.php">Hitzorduak</a>
            <a href="mezuak.php">Mezuak</a>
            <a href="kanpoko_mezuak.php">Kanpoko Mezuak</a>
            <a href="harrerako_langileak.php">Harrerako Langileak</a>
            <a href="<?php echo $base_path; ?>php_laguntzaileak/logout.php">Irten</a>
        </div>
        <p>&copy; 2026 GOsasun</p>
    </footer>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo $base_path; ?>js/common.js"></script>
    <?php if (isset($extra_js)): ?>
        <?php foreach ($extra_js as $js): ?>
            <script src="<?php echo $base_path; ?>js/<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

