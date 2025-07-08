<?php
include_once '../db.php';
include '../../mail/mail_gonder.php';
$database = new Database();

function siparisOnay(){
    global $database;
    $sip_id = $_POST["sip_id"];
    $durum = 2;

    $updateQuery = "UPDATE b2b_siparisler SET durum = :durum WHERE id = :id";
    $params = [
        'durum' => $durum,
        'id' => $sip_id
    ];
    $updateStmt = $database->update($updateQuery, $params);
}

function teslimEdildi() {
    global $database;
    
    $sip_id = $_POST["sip_id"];
    $durum = 5;

    $sip = $database->fetch("SELECT * FROM b2b_siparisler WHERE id = :id", ['id' => $sip_id]);
    $uye_id = $sip["uye_id"];
    $siparisNumarasi = $sip["siparis_no"];
    $siparis_tarih = $sip["tarih"];

    $uye = $database->fetch("SELECT * FROM uyeler WHERE id = :id", ['id' => $uye_id]);
    $uyeAdSoyad = $uye["ad"] . ' ' . $uye["soyad"];
    $uye_email = $uye["email"];

    $updateStmt = $database->update("UPDATE b2b_siparisler SET durum = :durum WHERE id = :id", ['durum' => $durum,'id' => $sip_id]);

    $mail_icerik = siparisTeslimEdildi($uyeAdSoyad, $siparisNumarasi, $siparis_tarih);
    mailGonder($uye_email, 'Siparişiniz Teslim Edilmiştir!', $mail_icerik, 'Siparişiniz Teslim Edilmiştir!');
}

function getKargo($vId){
    global $database;
    $var = $database->fetch("SELECT dosya FROM kargo_pdf WHERE sip_id = :id", ['id' => $vId]);
    $var1 = $var['dosya'];
    return $var1;
}

function getiadeDuzenle($vId) {
    global $database;
    $query = "SELECT i.durum, u.ad, u.soyad, u.email, u.tel, i.id, i.sip_urun_id FROM b2b_iadeler AS i
              LEFT JOIN uyeler AS u ON u.id = i.uye_id WHERE i.id = :id";
    $params = [
        'id' => $vId
    ];
    $var = $database->fetch($query, $params);
    return $var;
}

function iadeDuzenle() {
    global $database;
    $id = $_POST['id'];
    $durum = $_POST['durum'];
    $sip_urun_id = $_POST['sipUrunId'];
    $iade = 2;
    $iade0 = 0;

    $q = "SELECT i.*, su.id, su.sip_id, s.id, s.siparis_no FROM iadeler AS i 
            LEFT JOIN siparis_urunler AS su ON i.sip_urun_id = su.id 
            LEFT JOIN siparisler AS s ON s.id = su.sip_id WHERE i.id = :id";
    $params = [
        'id' => $id
    ];
    $sip = $database->fetch($q, $params);
    $siparisNumarasi = $sip["siparis_no"];
    $uye_id = $sip["uye_id"];

    $qa = "SELECT * FROM uyeler WHERE id = :id";
    $params = [
        'id' => $uye_id
    ];
    $uye = $database->fetch($qa, $params);

    $uyeAdSoyad = $uye["ad"] . ' ' . $uye["soyad"];
    $mail = $uye["email"];

    $updateQuery = "UPDATE iadeler SET durum = :durum WHERE id = :id";
    $params = [
        'durum' => $durum,
        'id' => $id
    ];
    $updateStmt = $database->update($updateQuery, $params);

    $updateQuery1 = "UPDATE siparis_urunler SET iade = :iade WHERE id = :id";
    $params = [
        'iade' => $iade,
        'id' => $sip_urun_id
    ];
    $updateStmt1 = $database->update($updateQuery1, $params);

    if($durum == '4'){
        $mail_icerik = iadeOnayMail($uyeAdSoyad, $siparisNumarasi);
        mailGonder($mail, 'İadeniz Onaylandı!', $mail_icerik, 'Nokta Elektronik');

    } elseif ($durum == '5'){
        $mail_icerik = iadeRedMail($uyeAdSoyad, $siparisNumarasi);
        mailGonder($mail, 'İadeniz Reddedildi!', $mail_icerik, 'Nokta Elektronik');
    }
}

if(isset($_POST["kargoKoliKaydet"])){
    $koli = $_POST["koli"];
    $sip_id = $_POST["sip_id"];

    $query = "UPDATE siparisler SET koli = :koli WHERE id = :id";
    $params = [
        'koli' => $koli,
        'id' => $sip_id
    ];
    $database->update($query, $params);

    header("Location:../noktanet_admin/pages/b2b/b2b-siparisdetay.php?id=" . $sip_id) . "&w=noktab2b";
}


if (isset($_POST['type'])) {
    $type = $_POST['type'];
    if ($type === 'siparis_onay') {
        siparisOnay();
      exit;
    } elseif ($type === 'teslim_edildi') {
        teslimEdildi();
      exit;
    }elseif ($type === 'iadeDuzenle') {
        iadeDuzenle();
        exit;
    }
}
 

if(isset($_POST['tur'])){
    $id = $_POST['id'];
    $type = $_POST['tur'];
    if($type === 'kargo_barkod'){
        $data = getKargo($id);
    }elseif ($type === 'iadeDuzenle') {
        $data = getiadeDuzenle($id);
    }
    echo json_encode($data);
}else {
    echo json_encode(['error' => 'Invalid request']);
}

?>