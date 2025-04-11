<?php
include ('../php/kargo_barkod.php');
$host = "noktanetdb.cbuq6a2265j6.eu-central-1.rds.amazonaws.com";
$username = "nokta";
$password = "Dell28736.!";
$database = "noktanetdb";

try {
    $db = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set UTF-8 charset
    $db->exec("set names utf8");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
$sip_id = $_POST["sip_id"];
$durum = 3;
error_reporting(E_ALL);
ini_set('display_errors', 1);

$q = $db->prepare("SELECT * FROM b2b_siparisler WHERE id = $sip_id ");
$q->execute();
$sip = $q->fetch(PDO::FETCH_ASSOC);

$uye_id = $sip["uye_id"];
$siparisNumarasi = $sip["siparis_no"];

$q = $db->prepare("SELECT * FROM uyeler WHERE id = $uye_id ");
$q->execute();
$uye = $q->fetch(PDO::FETCH_ASSOC);

$q = $db->prepare("SELECT * FROM b2b_adresler WHERE uye_id = $uye_id AND aktif = '1'");
$q->execute();
$adressorgu = $q->fetch(PDO::FETCH_ASSOC);

$uyeAdSoyad = $uye["ad"] . ' ' . $uye["soyad"];
$il_id = $adressorgu["il"];
$ilce_id = $adressorgu["ilce"];
$tel = $adressorgu["telefon"];
$adres = $adressorgu["adres"];
$firmaUnvani = $uye["firmaUnvani"];
$uye_email = $uye["email"];
$siparis_tarih = $sip["tarih"];

$q = $db->prepare("SELECT * FROM iller WHERE il_id = $il_id ");
$q->execute();
$iller = $q->fetch(PDO::FETCH_ASSOC);

$il = $iller["il_adi"];

$q = $db->prepare("SELECT * FROM ilceler WHERE ilce_id = $ilce_id ");
$q->execute();
$ilceler = $q->fetch(PDO::FETCH_ASSOC);

$ilce = $ilceler["ilce_adi"];

//----------------------------------//
$count = $sip['koli'];
$desi = $sip['desi'];

$currentDate = date("Ymd");
$randomChars = substr(str_shuffle("0123456789"), 0, 6);
$cargoKey = $currentDate . "5908" . $randomChars;
$invoiceKey = "FTR" . $currentDate . $randomChars;
$irsaliyeno = "NEBSIS" . $cargoKey;

$updateQuery = "UPDATE b2b_siparisler SET durum = ?, barkod = ? WHERE id = ?";
$updateStmt = $db->prepare($updateQuery);
$updateStmt->execute([$durum, $cargoKey, $sip_id]);

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
curl_setopt($ch, CURLOPT_POST, 1);
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
