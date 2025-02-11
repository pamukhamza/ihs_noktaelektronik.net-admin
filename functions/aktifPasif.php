<?php
if ($_POST) {
    include("db.php");
    $db = new Database(); // Database sınıfını başlat

    $konum = $_POST['konum'];
    $id = (int)$_POST['id'];
    $durum = (int)$_POST['durum'];

    // Tablo adını kontrol etmek ve sınırlamak için doğrulama yapın
    $allowedTables = array("uyeler", "b2b_banka_taksit_eslesme", "nokta_banka_bilgileri");
    if (!in_array($konum, $allowedTables)) {
        echo "Geçersiz tablo adı";
        exit;
    }

    // Güncelleme sorgusunu çalıştır
    $query = "UPDATE $konum SET aktif = :durum WHERE id = :id";
    $params = ['durum' => $durum, 'id' => $id];

    if ($db->update($query, $params)) {
        echo "$id nolu kayıt değiştirildi";
    } else {
        echo "Güncelleme sırasında hata oluştu";
    }
}
?>
