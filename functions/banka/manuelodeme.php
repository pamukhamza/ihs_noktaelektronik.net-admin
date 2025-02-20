<?php
include_once '../db.php';
include_once '../functions.php';
include 'dekont_olustur.php';
require_once '../wolvox/pos_olustur.php';
$database = new Database();

error_reporting(0); // HATA YAZDIRMA
ini_set('display_errors', 0); // HATA YAZDIRMA
error_reporting(E_ALL);

$kur = $database->fetch("SELECT * FROM kurlar WHERE id = :id", ['id' => '2']);
$satis_dolar = $kur['satis'];
$alis_dolar = $kur['alis'];

$kur2 = $database->fetch("SELECT * FROM kurlar WHERE id = :id", ['id' => '3']);
$satis_euro = $kur2['satis'];
$alis_euro = $kur2['alis'];

$siparisNumarasi = WEB4UniqueOrderNumber();

if (isset($_GET['cariveri']) || isset($_GET['cariveriFinans'])) {
    if(isset($_GET['cariveri'])) {
        $veri = base64_decode($_GET['cariveri']);
    } elseif(isset($_GET['cariveriFinans'])) {
        $veri = base64_decode($_GET['cariveriFinans']);
    }
    $decodedVeri = json_decode($veri, true);
    $yantoplam = $decodedVeri["yantoplam"];
    $cardNo = $decodedVeri["cardNo"];
    $cariOdeme = "cari";
    $maskedCardNo = substr($cardNo, 0, 4) . str_repeat('*', strlen($cardNo) - 8) . substr($cardNo, -4);
    $cardHolder = $decodedVeri["cardHolder"];
    $banka_id = $decodedVeri["banka_id"];
    $hesap = $decodedVeri["hesap"];
    $taksit_sayisi = $decodedVeri["taksit"];
    $uye_id = $decodedVeri["uye_id"];
    $lang = $decodedVeri["lang"];

    if($hesap == 1){$doviz = "$";}else{$doviz = "TL";}

    $banka_pos = $database->fetch("SELECT * FROM banka_pos_listesi WHERE id = :id", ['id' => $banka_id]);
    $blbnhskodu = $banka_pos["BLBNHSKODU"];
    $banka_adi = $banka_pos["BANKA_ADI"];
    $banka_tanimi = $banka_pos["TANIMI"];

    $uye = $database->fetch("SELECT * FROM uyeler WHERE id = :id", ['id' => $uye_id]);
    $uyecarikod = $uye['BLKODU'];
    $uye_mail = $uye['email'];
    $firmaUnvani = $uye['firmaUnvani'];
    $yonetici_maili = 'h.pamuk@noktaelektronik.net';

    $dov_al = str_replace('.', ',', $alis_dolar);
    $dov_sat = str_replace('.', ',', $satis_dolar);

    $currentDateTime = date("d.m.Y H:i:s");
    $degistirme_tarihi = date("d.m.Y H:i:s", strtotime($currentDateTime . " +3 hours"));

    if(isset($_GET['cariveri'])) {
        //Param Pos
        $sonucStr = $_POST['TURKPOS_RETVAL_Sonuc_Str'];
        $dekont = $_POST['TURKPOS_RETVAL_Dekont_ID'];
        $tutar = $_POST['TURKPOS_RETVAL_Tahsilat_Tutari'];
        $tutar = str_replace(',', '.', $tutar);
        $pos_id = 1;
        $basarili = 1;

        $query = "INSERT INTO sanal_pos_odemeler (`uye_id`, `pos_id`, `islem`, `islem_turu`, `tutar`, `basarili`) VALUES (:uye_id, :pos_id, :islem, :islem_turu, :tutar, :basarili) ";
        $params = [ 'uye_id' => $uye_id, 'pos_id' => $pos_id, 'islem' => $sonucStr, 'islem_turu' => $cariOdeme, 'tutar' => $tutar, 'basarili' => $basarili, ];
        $database->insert($query, $params);

        $inserted_id = $database->lastInsertId();


        dekontOlustur($uye_id, $inserted_id, $firmaUnvani,$maskedCardNo, $cardHolder ,$taksit_sayisi,$yantoplam,$degistirme_tarihi);
        posXmlOlustur($uyecarikod, $hesap, $degistirme_tarihi,$currentDateTime,$yantoplam,'',$dov_al,$dov_sat,$siparisNumarasi,$blbnhskodu,$banka_adi,'', $doviz, $banka_tanimi);
        $mail_icerik = cariOdeme($firmaUnvani,$yantoplam,$taksit_sayisi);
        mailGonder($uye_mail, 'Cari Ödeme Bildirimi', $mail_icerik, 'Nokta Elektronik');
        header("Location: pages/b2b/b2b-sanalpos?w=noktab2b&cari_odeme=");

    }elseif(isset($_GET['cariveriFinans']) && $_POST["mdStatus"] == "1") {
        $username = 'noktaadmin';
        $password = 'NEBsis28736.!';
        if($taksit_sayisi == 1 || $taksit_sayisi == 0){
            $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <CC5Request>
            <Name>' . $username . '</Name>
            <Password>' . $password . '</Password>
            <ClientId>' . $_POST['clientid'] . '</ClientId>
            <OrderId>' . $_POST['oid'] . '</OrderId>
            <Type>Auth</Type>
            <Number>' . $_POST['md'] . '</Number>
            <Total>' . $_POST['amount'] . '</Total>
            <Currency>949</Currency>
            <PayerTxnId>' . $_POST['xid'] . '</PayerTxnId>
            <PayerSecurityLevel>' . $_POST['eci'] . '</PayerSecurityLevel>
            <PayerAuthenticationCode>' . $_POST['cavv'] . '</PayerAuthenticationCode>
            </CC5Request>';
        }else{
            $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <CC5Request>
            <Name>' . $username . '</Name>
            <Password>' . $password . '</Password>
            <ClientId>' . $_POST['clientid'] . '</ClientId>
            <OrderId>' . $_POST['oid'] . '</OrderId>
            <Type>Auth</Type>
            <Number>' . $_POST['md'] . '</Number>
            <Total>' . $_POST['amount'] . '</Total>
            <Mode>P</Mode>
            <Taksit>'. $taksit_sayisi .'</Taksit>
            <Currency>949</Currency>
            <PayerTxnId>' . $_POST['xid'] . '</PayerTxnId>
            <PayerSecurityLevel>' . $_POST['eci'] . '</PayerSecurityLevel>
            <PayerAuthenticationCode>' . $_POST['cavv'] . '</PayerAuthenticationCode>
            </CC5Request>';
            file_put_contents('gidenxml.xml', $xml);
        }

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSLVERSION, 6);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml', 'Content-length: ' . strlen($xml)));
            curl_setopt($ch, CURLOPT_POST, true); //POST Metodu kullanarak verileri gönder
            curl_setopt($ch, CURLOPT_HEADER, false); //Serverdan gelen Header bilgilerini önemseme.
            curl_setopt($ch, CURLOPT_URL, 'https://sanalpos.turkiyefinans.com.tr/fim/api'); //Bağlanacağı URL
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Transfer sonuçlarını al.
            $data = curl_exec($ch);
            $xmlResponse = simplexml_load_string($data);
            file_put_contents('gelenxml.xml', $xmlResponse);

            if ($xmlResponse->ProcReturnCode == "00") {              
                $dov_al = str_replace('.', ',', $alis_dolar);
                $dov_sat = str_replace('.', ',', $satis_dolar);

                $currentDateTime = date("d.m.Y H:i:s");
                $degistirme_tarihi = date("d.m.Y H:i:s", strtotime($currentDateTime . " +3 hours"));

                $yantoplam1 = floatval($yantoplam);
                $yantoplam = number_format($yantoplam1, 2, ',', '.');

                $pos_id = 4;
                $basarili = 1;
                $sonucStr = "Ödeme işlemi başarılı: " . $xmlResponse->Response . ' Kod= ' . $xmlResponse->ProcReturnCode;
                $oid = $xmlResponse->ReturnOid;
                $transid = $xmlResponse->TransId;


                $query = "INSERT INTO sanal_pos_odemeler (uye_id, pos_id, islem, islem_turu, tutar, basarili, transid, siparis_no) VALUES (:uye_id, :pos_id, :islem, :islem_turu, :tutar, :basarili, :transid, :siparis_no)";
                $params = ['uye_id' => $uye_id, 'pos_id' => $pos_id, 'islem' => $sonucStr, 'islem_turu' => $cariOdeme, 'tutar' => $yantoplam1, 'basarili' => $basarili, 'transid' => $transid, 'siparis_no' => $oid, ];
                $database->insert($query, $params);

                $inserted_id = $database->lastInsertId();

                dekontOlustur($uye_id, $inserted_id, $firmaUnvani, $maskedCardNo, $cardHolder, $taksit_sayisi, $yantoplam, $degistirme_tarihi);
                posXmlOlustur($uyecarikod, $hesap, $degistirme_tarihi,$degistirme_tarihi,$yantoplam,'',$dov_al,$dov_sat,$siparisNumarasi,$blbnhskodu,$banka_adi,$taksit_sayisi, $doviz, $banka_tanimi);

                $mail_icerik = cariOdeme($firmaUnvani,$yantoplam,$taksit_sayisi);
                mailGonder($uye_mail, 'Cari Ödeme Bildirimi', $mail_icerik,'Nokta Elektronik');
                header("Location: pages/b2b/b2b-sanalpos?w=noktab2b&cari_odeme=");
                exit();

            } else {
                $yantoplam1 = floatval($yantoplam);
                // ProcReturnCode 00 değilse hata mesajı göster veya başka bir işlem yap
                $pos_id = 4;
                $basarili = 0;
                $sonucStr = "Ödeme işlemi başarısız: " . $xmlResponse->ErrMsg . ' Kod= ' . $xmlResponse->ProcReturnCode;

                $query = "INSERT INTO sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)";
                $params = ['uye_id' => $uye_id, 'pos_id' => $pos_id, 'islem' => $sonucStr, 'tutar' => $yantoplam1, 'basarili' => $basarili ];
                $database->insert($query, $params);

                header("Location: pages/b2b/b2b-sanalpos?w=noktab2b&code=".$xmlResponse->ProcReturnCode."&message=".$xmlResponse->ErrMsg);
            }
            curl_close($ch);
        }
        catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
}
?>