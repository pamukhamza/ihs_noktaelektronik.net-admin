<?php 
include_once '../../db.php';
function admin_sepete_urun_ekle() {
    $uyeId = $_POST['uye_id'];
    $urun_id = $_POST['urun_id'];
    $adet = $_POST['urun_adet'];
    $database = new Database();
    try {
        $query = $database->insert("INSERT INTO uye_sepet (uye_id, urun_id, adet) VALUES ($uyeId, $urun_id, $adet)");
    } finally {
        $database = null; // Bağlantıyı kapat
    }
}
function editAnlikSepetIndirim() {
    $uyeId = $_POST['uyeId'];
    $indirim = $_POST['indirim'];
    $database = new Database();
    try {
        $query = "UPDATE uye_sepet SET sepet_ozel_indirim = :indirim WHERE uye_id = :uyeId";
        $var = $database->update($query, array('indirim' => $indirim, 'uyeId' => $uyeId));
    } finally {
        $database = null; // Close the connection
    }
}
function editAnlikSepetGuncelle() {
    $adet = $_POST['adet'];
    $sepet = $_POST['sepetId'];
    $fiyat = isset($_POST['fiyat']) && $_POST['fiyat'] !== '' ? $_POST['fiyat'] : null;

    $database = new Database();
    $query = "UPDATE uye_sepet SET adet = :adet, ozel_fiyat = :fiyat  WHERE id = :sepet";
    $var = $database->update($query, array('adet' => $adet, 'fiyat' => $fiyat, 'sepet' => $sepet));
}
function sepetUrunSil() {
    $gid = $_POST['gid'];
    $database = new Database();
    $delete = $database->delete("DELETE FROM uye_sepet WHERE id = $gid ");
}

if (isset($_POST['type'])) {
    $type = $_POST['type'];
    if ($type === 'admin_sepete_urun_ekle') {
        admin_sepete_urun_ekle();
        exit;
    } 
    elseif ($type === 'anlik_sepet_indirim') {
        editAnlikSepetIndirim();
        exit;
    }
    elseif ($type === 'anlik_sepet_guncelle') {
        editAnlikSepetGuncelle();
        exit;
    }
    elseif ($type === 'sepet_urun_sil') {
        sepetUrunSil();
        exit;
    }
}