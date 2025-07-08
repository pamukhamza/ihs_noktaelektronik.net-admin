<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once ('../db.php');
include_once ('kargo_barkod.php');
$database = new Database();

$sip_id = $_POST["sip_id"];
$durum = 3;

$sip = $database->fetch("SELECT * FROM b2b_siparisler WHERE id = :id ", ['id' => $sip_id]);
$uye_id = $sip["uye_id"];
$siparisNumarasi = $sip["siparis_no"];
$siparis_tarih = $sip["tarih"];
$count = $sip['koli'];
$desi = $sip['desi'];

$uye = $database->fetch("SELECT * FROM uyeler WHERE id = :id ", ['id' => $uye_id]);
$firmaUnvani = $uye["firmaUnvani"];
$uye_email = $uye["email"];
$uyeAdSoyad = $uye["ad"] . ' ' . $uye["soyad"];

$adressorgu = $database->fetch("SELECT * FROM b2b_adresler WHERE uye_id = :id AND aktif = :aktif", ['id' => $uye_id,'aktif' => '1']);
$il_id = $adressorgu["il"];
$ilce_id = $adressorgu["ilce"];
$tel = $adressorgu["telefon"];
$adres = $adressorgu["adres"];

$iller = $database->fetch("SELECT * FROM iller WHERE il_id = :id", ['id' => $il_id]);
$il = $iller["il_adi"];

$ilceler = $database->fetch("SELECT * FROM ilceler WHERE ilce_id = :id ", ['id' => $ilce_id]);
$ilce = $ilceler["ilce_adi"];

// Bugünün tarihini al
$currentDate = date("Ymd");
// Rastgele 6 karakter oluştur
$randomChars = substr(str_shuffle("0123456789"), 0, 6);
// $cargoKey oluştur
$cargoKey = $currentDate . "5459" . $randomChars;
$invoiceKey = "FTR" . $currentDate . $randomChars;

$irsaliyeno = "NEBSIS" . $cargoKey;



$requestXml = '
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ship="http://yurticikargo.com.tr/ShippingOrderDispatcherServices">
   <soapenv:Header/>
   <soapenv:Body>
      <ship:createShipment>
         <wsUserName>1104N187205434G</wsUserName>
         <wsPassword>i101h487Ww128g24</wsPassword>                            
         <userLanguage>TR</userLanguage>
         <ShippingOrderVO>
            <cargoCount>'.$count.'</cargoCount>
            <cargoKey>'.$cargoKey.'</cargoKey>
            <cityName>'.$il.'</cityName>
            <dcCreditRule/>
            <dcSelectedCredit/>
            <invoiceKey>'.$invoiceKey.'</invoiceKey>
            <receiverAddress>'.$adres.'</receiverAddress>
            <receiverCustName>'.$firmaUnvani.'</receiverCustName>
            <receiverPhone1>'.$tel.'</receiverPhone1>
            <townName>'.$ilce.'</townName>
            <ttDocumentId/>
            <waybillNo>'.$irsaliyeno.'</waybillNo>
         </ShippingOrderVO>';

$requestXml .= '
      </ship:createShipment>
   </soapenv:Body>
</soapenv:Envelope>';

$ch = curl_init();

$url = "https://ws.yurticikargo.com/KOPSWebServices/ShippingOrderDispatcherServices?wsdl";
curl_setopt($ch, CURLOPT_URL, $url);
// Set method to POST
curl_setopt($ch, CURLOPT_POST, 1);
// Set request headers
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: text/xml",
    "SOAPAction: createShipment"
));
curl_setopt($ch, CURLOPT_POSTFIELDS, $requestXml);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
if(curl_errno($ch)){
    echo 'Curl error: ' . curl_error($ch);
}

/* Set the appropriate headers to display the response as XML
header('Content-Type: text/xml');
 Echo the response directly
echo $response; */

curl_close($ch);
$updateQuery = "UPDATE b2b_siparisler SET durum = :durum, barkod = :barkod WHERE id = :id";
$params = ['durum' => $durum,'barkod' => $cargoKey,'id' => $sip_id];
$updateStmt = $database->update($updateQuery, $params);
kargopdf($uye_id, $sip_id, $cargoKey);
exit;
?>
