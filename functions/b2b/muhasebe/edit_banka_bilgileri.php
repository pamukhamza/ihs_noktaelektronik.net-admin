<?php
include("../../db.php");
function bankaBilgileri() {
    $database = new Database();

    $id = $_POST['id'];
    $bankaAdi = $_POST['bankaAdi'];
    $subeAdi = $_POST['subeAdi'];
    $iban = $_POST['iban'];
    $kolayAdres = $_POST['kolayAdres'];
    $hesapSahibi = $_POST['hesapSahibi'];
    $hesapTuru = $_POST['hesapTuru'];
    $swift = $_POST['swift'];
    $aktif = 1;

    if ($id) {
        $query = "UPDATE nokta_banka_bilgileri SET hesap_adi = :hesap_adi, banka_adi = :banka_adi, hesap = :hesap, sube_adi = :sube_adi, iban = :iban, 
                                                    kolay_adres = :kolay_adres, swift = :swift WHERE id = :id";
                                                    
        $var = $database->update($query, array('hesap_adi' => $hesapSahibi, 'banka_adi' => $bankaAdi, 'hesap' => $hesapTuru ,'sube_adi' => $subeAdi ,
                                                'iban' => $iban, 'kolay_adres' => $kolayAdres, 'swift' => $swift, 'id' => $id));
    } else {
        $query = "INSERT INTO nokta_banka_bilgileri (hesap_adi, banka_adi, hesap, sube_adi, iban, kolay_adres, swift, aktif) VALUES 
                                                    (:hesap_adi, :banka_adi, :hesap, :sube_adi, :iban, :kolay_adres, :swift, :aktif)";
        $var = $database->insert($query, array('hesap_adi' => $hesapSahibi, 'banka_adi' => $bankaAdi, 'hesap' => $hesapTuru ,'sube_adi' => $subeAdi ,
                                                'iban' => $iban, 'kolay_adres' => $kolayAdres, 'swift' => $swift, 'aktif' => $aktif));
    }
}

if (isset($_POST['type'])) {
    $type = $_POST['type'];
    if ($type === 'bankaBilgileri') {
        bankaBilgileri();
        exit;
    }
}  