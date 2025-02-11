<?php
include ('../db.php');
include ('kargo_barkod.php');

$database = new Database();

$sip_id = $_POST["sip_id"];
$durum = 3;
error_reporting(E_ALL);
ini_set('display_errors', 1);

$q = $db->prepare("SELECT * FROM b2b_siparisler WHERE id = :id ");
$params = [
    'id' => $sip_id
];
$sip = $database->fetch($q, $params);


$uye_id = $sip["uye_id"];
$siparisNumarasi = $sip["siparis_no"];

$q = $db->prepare("SELECT * FROM uyeler WHERE id = :id ");
$params = [
    'id' => $uye_id
];
$uye = $q->fetch($q, $params);

$q = $db->prepare("SELECT * FROM adresler WHERE uye_id = :id AND aktif = :aktif");
$params = [
    'id' => $uye_id,
    'aktif' => '1'
];
$adressorgu = $database->fetch($q, $params);


$uyeAdSoyad = $uye["ad"] . ' ' . $uye["soyad"];
$il_id = $adressorgu["il"];
$ilce_id = $adressorgu["ilce"];
$tel = $adressorgu["telefon"];
$adres = $adressorgu["adres"];
$firmaUnvani = $uye["firmaUnvani"];
$uye_email = $uye["email"];
$siparis_tarih = $sip["tarih"];

$q = $db->prepare("SELECT * FROM iller WHERE il_id = :id");
$params = [
    'id' => $il_id,
];
$iller = $database->fetch($q, $params);

$il = $iller["il_adi"];

$q = $db->prepare("SELECT * FROM ilceler WHERE ilce_id = :id ");
$params = [
    'id' => $ilce_id,
];
$ilceler = $database->fetch($q, $params);

$ilce = $ilceler["ilce_adi"];

//----------------------------------//
$count = $sip['koli'];
$desi = $sip['desi'];

// Bugünün tarihini al
$currentDate = date("Ymd");
// Rastgele 6 karakter oluştur
$randomChars = substr(str_shuffle("0123456789"), 0, 6);
// $cargoKey oluştur
$cargoKey = $currentDate . "4228" . $randomChars;
$invoiceKey = "FTR" . $currentDate . $randomChars;

$irsaliyeno = "NEBSIS" . $cargoKey;

$updateQuery = "UPDATE b2b_siparisler SET durum = :durum, barkod = :barkod WHERE id = :id";
$params = [
    'durum' => $durum,
    'barkod' => $cargoKey,
    'id' => $sip_id
];
$updateStmt = $database->update($updateQuery, $params);


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

kargopdf($uye_id, $sip_id, $cargoKey);

?>
