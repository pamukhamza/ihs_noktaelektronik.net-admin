<?php
include("../../db.php");

function getBankaBilgisiById($bId) {
    $db = new Database();

    $query = "SELECT * FROM nokta_banka_bilgileri WHERE id = $bId";
    $bankabilgisi = $db->fetch($query);

    return $bankabilgisi;
}

if (isset($_POST['id']) && isset($_POST['type'])) {
    $id = $_POST['id'];
    $type = $_POST['type'];

    if ($type === 'bankaBilgisi') {
        $data = getBankaBilgisiById($id);
        
        if ($data) {
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Banka bilgisi bulunamadı.']);
        }
    }
} else {
    echo json_encode(['error' => 'Geçersiz istek.']);
}
?>
