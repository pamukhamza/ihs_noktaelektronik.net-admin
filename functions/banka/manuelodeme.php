<?php
include_once '../db.php';
include_once '../functions.php';
include_once '../../mail/mail_gonder.php';
include_once 'dekontolustur.php';
require_once '../wolvox/pos_olustur.php';
require_once 'handlers/POSHandler.php';
require_once 'handlers/FinansPOSHandler.php';
require_once 'handlers/KuveytPOSHandler.php';
require_once 'handlers/YapiKrediPOSHandler.php';

$database = new Database();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get currency rates
$kur = $database->fetch("SELECT * FROM b2b_kurlar WHERE id = :id", ['id' => '2']);
$satis_dolar = $kur['satis'];
$alis_dolar = $kur['alis'];

$kur2 = $database->fetch("SELECT * FROM b2b_kurlar WHERE id = :id", ['id' => '3']);
$satis_euro = $kur2['satis'];
$alis_euro = $kur2['alis'];

$siparisNumarasi = WEB4UniqueOrderNumber();

if (isset($_GET['cariveri']) || isset($_GET['cariveriFinans']) || isset($_GET['cariveriKuveyt'])) {
    // Decode payment data
    if(isset($_GET['cariveri'])) {
        $veri = base64_decode($_GET['cariveri']);
    } elseif(isset($_GET['cariveriFinans'])) {
        $veri = base64_decode($_GET['cariveriFinans']);
    } elseif(isset($_GET['cariveriKuveyt'])) {
        $veri = base64_decode($_GET['cariveriKuveyt']);
    }
    
    $decodedVeri = json_decode($veri, true);
    
    // Extract payment details
    $yantoplam = $decodedVeri["yantoplam"];
    $cardNo = $decodedVeri["cardNo"];
    $maskedCardNo = substr($cardNo, 0, 4) . str_repeat('*', strlen($cardNo) - 8) . substr($cardNo, -4);
    $cardHolder = $decodedVeri["cardHolder"];
    $banka_id = $decodedVeri["banka_id"];
    $hesap = $decodedVeri["hesap"];
    $taksit_sayisi = $decodedVeri["taksit"];
    $uye_id = $decodedVeri["uye_id"];
    $lang = $decodedVeri["lang"];

    $doviz = ($hesap == 1) ? "$" : "TL";

    // Get bank and user details
    $banka_pos = $database->fetch("SELECT * FROM b2b_banka_pos_listesi WHERE id = :id", ['id' => $banka_id]);
    $uye = $database->fetch("SELECT * FROM uyeler WHERE id = :id", ['id' => $uye_id]);

    // Format currency and dates
    $dov_al = str_replace('.', ',', $alis_dolar);
    $dov_sat = str_replace('.', ',', $satis_dolar);
    $currentDateTime = date("d.m.Y H:i:s");
    $degistirme_tarihi = date("d.m.Y H:i:s", strtotime($currentDateTime . " +3 hours"));

    // Prepare payment data
    $paymentData = [
        'uye_id' => $uye_id,
        'firmaUnvani' => $uye['firmaUnvani'],
        'maskedCardNo' => $maskedCardNo,
        'cardHolder' => $cardHolder,
        'taksit_sayisi' => $taksit_sayisi,
        'yantoplam' => $yantoplam,
        'degistirme_tarihi' => $degistirme_tarihi,
        'uyecarikod' => $uye['BLKODU'],
        'hesap' => $hesap,
        'currentDateTime' => $currentDateTime,
        'dov_al' => $dov_al,
        'dov_sat' => $dov_sat,
        'siparisNumarasi' => $siparisNumarasi,
        'blbnhskodu' => $banka_pos["BLBNHSKODU"],
        'banka_adi' => $banka_pos["BANKA_ADI"],
        'doviz' => $doviz,
        'banka_tanimi' => $banka_pos["TANIMI"],
        'uye_mail' => $uye['email']
    ];

    // Process payment based on type
    if(isset($_GET['cariveriFinans'])) {
        $handler = new FinansPOSHandler($database, $paymentData);
        $handler->processPayment();
    } 
    if(isset($_GET['cariveriKuveyt'])) {
        $handler = new KuveytPOSHandler($database, $paymentData);
        $handler->processPayment();
    } 
}
if(isset($_GET['cariveriYapiKredi'])){
    if(isset($_GET['cariveriYapiKredi'])) {
        $veri = base64_decode($_GET['cariveriYapiKredi']);
    }
    $decodedVeri = json_decode($veri, true);
    
    // Extract payment details
    $yantoplam = $decodedVeri["yantoplam"];
    $cardNo = $decodedVeri["cardNo"];
    $maskedCardNo = substr($cardNo, 0, 4) . str_repeat('*', strlen($cardNo) - 8) . substr($cardNo, -4);
    $cardHolder = $decodedVeri["cardHolder"];
    $banka_id = $decodedVeri["banka_id"];
    $hesap = $decodedVeri["hesap"];
    $taksit_sayisi = $decodedVeri["taksit"];
    $uye_id = $decodedVeri["uye_id"];
    $lang = $decodedVeri["lang"];

    $doviz = ($hesap == 1) ? "$" : "TL";

    // Get bank and user details
    $banka_pos = $database->fetch("SELECT * FROM b2b_banka_pos_listesi WHERE id = :id", ['id' => $banka_id]);
    $uye = $database->fetch("SELECT * FROM uyeler WHERE id = :id", ['id' => $uye_id]);

    // Format currency and dates
    $dov_al = str_replace('.', ',', $alis_dolar);
    $dov_sat = str_replace('.', ',', $satis_dolar);
    $currentDateTime = date("d.m.Y H:i:s");
    $degistirme_tarihi = date("d.m.Y H:i:s", strtotime($currentDateTime . " +3 hours"));

    // Prepare payment data
    $paymentData = [
        'uye_id' => $uye_id,
        'firmaUnvani' => $uye['firmaUnvani'],
        'maskedCardNo' => $maskedCardNo,
        'cardHolder' => $cardHolder,
        'taksit_sayisi' => $taksit_sayisi,
        'yantoplam' => $yantoplam,
        'degistirme_tarihi' => $degistirme_tarihi,
        'uyecarikod' => $uye['BLKODU'],
        'hesap' => $hesap,
        'currentDateTime' => $currentDateTime,
        'dov_al' => $dov_al,
        'dov_sat' => $dov_sat,
        'siparisNumarasi' => $siparisNumarasi,
        'blbnhskodu' => $banka_pos["BLBNHSKODU"],
        'banka_adi' => $banka_pos["BANKA_ADI"],
        'doviz' => $doviz,
        'banka_tanimi' => $banka_pos["TANIMI"],
        'uye_mail' => $uye['email']
    ];
    
    if(isset($_GET['cariveriYapiKredi'])) {
        $handler = new YapiKrediPOSHandler($database, $paymentData);
        $handler->processPayment();
    }
}
?>