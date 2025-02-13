<?php
include_once '../../db.php';
$database = new Database();

if (isset($_POST["kur_guncelle"])) {
    $id = $_POST['id'];
    $alis = $_POST['alis'];
    $satis = $_POST['satis'];

    // Güncelleme sorgusunu `update` fonksiyonuyla çalıştır
    $query = "UPDATE b2b_kurlar SET alis = :alis, satis = :satis WHERE id = :id";
    $params = [
        'alis' => $alis,
        'satis' => $satis,
        'id' => $id
    ];

    if ($database->update($query, $params)) {
        header("Location: ../../../pages/b2b/b2b-doviz.php?w=noktab2b&s=1");
        exit;
    } else {
        echo "Güncelleme başarısız!";
    }
}
?>
