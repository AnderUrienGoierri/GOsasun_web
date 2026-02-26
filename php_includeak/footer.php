    <footer>
        <link rel="stylesheet" href="<?php echo $base_path; ?>css/footer.css">
        <div class="footer-logo-container">
            <img src="<?php echo $base_path; ?>img/GOsasun_logoa.png" alt="GOsasun Logo" class="footer-logo">
        </div>
        <div class="orri-oin-nabigazioa">
            <a href="<?php echo $base_path; ?>index.php">Hasiera</a> | 
            <a href="<?php echo $base_path; ?>php_hasiera/kontaktua.php">Kontaktua</a> | 
            <a href="<?php echo $base_path; ?>php_hasiera/login.php">Saioa Hasi</a>
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

