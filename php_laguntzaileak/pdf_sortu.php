<?php
session_start();
if (!isset($_SESSION['rol_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Baimenik ez']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf'])) {
    $uploadDir = dirname(__DIR__) . '/pdf_pdf_txostenak/';
    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $erabiltzaile_id = $_SESSION['erabiltzaile_id'] ?? 'Ezezaguna';
    // Optionally a patient ID could be passed if a doctor is saving someone else's report
    $paziente_id = $_POST['paziente_id'] ?? $erabiltzaile_id;
    $date = date('Ymd_His');
    
    $filename = "Grafika_Pazientea_{$paziente_id}_{$date}.pdf";
    $targetPath = $uploadDir . $filename;
    
    if (move_uploaded_file($_FILES['pdf']['tmp_name'], $targetPath)) {
        echo json_encode([
            'success' => true, 
            'url' => 'pdf_txostenak/' . $filename,
            'msg' => 'PDF txostena behar bezala gorde da!',
            'filename' => $filename
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Errorea fitxategia zerbitzarian gordetzean']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Eskaera okerra. PDF fitxategia falta da.']);
}
?>

