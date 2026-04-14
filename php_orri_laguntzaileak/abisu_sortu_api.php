<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['erabiltzaile_id'])) {
    echo json_encode(['success' => false, 'message' => 'Saioa hasi gabe']);
    exit;
}
if (!isset($_SESSION['abisuak'])) $_SESSION['abisuak'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paziente_id = $_POST['paziente_id'];
    $mota = $_POST['mota'];
    $testua = $_POST['testua'];
    if (!isset($_SESSION['abisuak'][$paziente_id])) $_SESSION['abisuak'][$paziente_id] = [];
    // Ez sortu abisu bikoiztua: testu berdina duen abisua badago, ez gehitu
    $exists = false;
    foreach ($_SESSION['abisuak'][$paziente_id] as $abisu) {
        if ($abisu['testua'] === $testua) {
            $exists = true;
            break;
        }
    }
    if (!$exists) {
        $_SESSION['abisuak'][$paziente_id][] = [
            'mota' => $mota,
            'testua' => $testua,
            'irakurrita' => false,
            'data' => date('Y-m-d H:i:s'),
        ];
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Eskaera ezegokia']);
}
?>

