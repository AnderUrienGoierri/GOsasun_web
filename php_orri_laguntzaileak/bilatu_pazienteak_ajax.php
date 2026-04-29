<?php
// php_orri_laguntzaileak/bilatu_pazienteak_ajax.php
session_start();
require_once 'DB_konexioa.php';

header('Content-Type: application/json');

if (!isset($_SESSION['erabiltzaile_id'])) {
    echo json_encode(['success' => false, 'error' => 'Saioa hasi gabe']);
    exit;
}

$testua = $_GET['q'] ?? '';
$langile_id = $_SESSION['erabiltzaile_id'];

if (strlen($testua) < 2) {
    echo json_encode(['success' => true, 'pazienteak' => []]);
    exit;
}

try {
    // Bilatu izena, abizena edo NANaren arabera
    $bilaketa = "%$testua%";
    $stmt = $pdo->prepare("
        SELECT paziente_id, izena, abizenak, nan, irudia
        FROM V_Langile_Pazienteak
        WHERE langile_id = ?
        AND (izena LIKE ? OR abizenak LIKE ? OR nan LIKE ? OR paziente_id LIKE ?)
        LIMIT 10
    ");
    $stmt->execute([$langile_id, $bilaketa, $bilaketa, $bilaketa, $bilaketa]);
    $pazienteak = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'pazienteak' => $pazienteak]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
