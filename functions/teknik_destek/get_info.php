<?php

include_once '../db.php';
$database = new Database();

function getTdpById($tId) {
    global $database;
    $query = "
        SELECT u.*, t.uye_id, t.takip_kodu, t.tarih, t.fatura_no, t.musteri, t.tel, t.mail, t.adres, t.teslim_eden, t.teslim_alan, t.gonderim_sekli, t.kargo_firmasi, t.aciklama
        FROM teknik_destek_urunler u
        LEFT JOIN nokta_teknik_destek t ON u.tdp_id = t.id
        WHERE u.id = :id
    ";
    $params = [
        'id' => $tId
    ];
    $result = $database->fetch($query, $params);

    return $result;

}
if (isset($_POST['id']) && isset($_POST['type'])) {
    $id = $_POST['id'];
    $type = $_POST['type'];
    if ($type === 'tdp') {
        $data = getTdpById($id);
        if ($data) {
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Kayıt bulunamadı']);
        }
    }
    
}



?>