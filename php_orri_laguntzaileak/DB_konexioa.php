<?php
$host = 'localhost';
$dbname = 'GOsasun_DB';
$username = 'root'; // Edo  adminRoot erabiltzailea
$password = '1MG32025'; //  adminRoot pasahitza

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Erroreak kudeatzeko konfigurazioa: Salbuespenak jaurti
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Bilaketa espezialak ez egiteko
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Konexio errorea: " . $e->getMessage());
}
?>
