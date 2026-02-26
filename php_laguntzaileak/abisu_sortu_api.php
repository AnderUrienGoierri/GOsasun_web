<?php
session_start();
require_once '../php_laguntzaileak/DB_konexioa.php';

header('Content-Type: application/json');

if (!isset($_SESSION['erabiltzaile_id'])) {
    echo json_encode(['success' => false, 'message' => 'Saioa hasi gabe']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paziente_id = $_POST['paziente_id'];
    $mota = $_POST['mota'];
    $testua = $_POST['testua'];

    try {
        $stmt = $pdo->prepare("INSERT INTO Abisuak (paziente_id, data, mota, testua) VALUES (?, NOW(), ?, ?)");
        $stmt->execute([$paziente_id, $mota, $testua]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Eskaera ezegokia']);
}
?>

