<?php
include_once '../../db.php';

function getTaksit($vId) {
    $database = new Database();
    $var = $database->fetch("SELECT * FROM b2b_banka_taksit_eslesme WHERE id = :id", array('id' => $vId));
    return $var;
}

// Hata ayıklama için hata raporlamayı aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

// JSON başlık ayarı
header('Content-Type: application/json');

if (isset($_POST['id']) && isset($_POST['type'])) {
    $id = $_POST['id'];
    $type = $_POST['type'];

    if ($type === 'taksit') {
        $data = getTaksit($id);

        if ($data) {
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["error" => "Veri bulunamadı"], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(["error" => "Geçersiz istek türü"], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode(["error" => "Eksik parametreler"], JSON_UNESCAPED_UNICODE);
}
?>
