<?php
$gelen = $_POST['bin'];

$binXml = '<?xml version="1.0" encoding="utf-8"?> 
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"> 
<soap:Body>
<BIN_SanalPos xmlns="https://turkpos.com.tr/">
<G>
<CLIENT_CODE>10738</CLIENT_CODE>
<CLIENT_USERNAME>Test</CLIENT_USERNAME>
<CLIENT_PASSWORD>Test</CLIENT_PASSWORD>
</G>
<BIN>' . $gelen . '</BIN>
</BIN_SanalPos>
</soap:Body>
</soap:Envelope>';

$posURL = 'https://test-dmz.param.com.tr/turkpos.ws/service_turkpos_test.asmx';

$ch = curl_init($posURL);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: text/xml',
    'SOAPAction: "https://turkpos.com.tr/BIN_SanalPos"'
));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $binXml);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);

// cURL hata kontrolü
if ($response === false) {
    echo "cURL Hatası: " . curl_error($ch);
    exit;
}

curl_close($ch);

// API yanıtı boş mu?
if (!$response) {
    echo "API yanıtı boş.";
    exit;
}

// XML'i doğrula
$responseXml = new DOMDocument();
if (!$responseXml->loadXML($response)) {
    echo "API yanıtı geçerli bir XML değil: " . htmlspecialchars($response);
    exit;
}

// XPath işlemleri
$xpath = new DOMXPath($responseXml);
$xpath->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
$xpath->registerNamespace('', 'https://turkpos.com.tr/');

$kartBanka = $xpath->evaluate('string(//Kart_Banka)');
$kartMarka = $xpath->evaluate('string(//Kart_Marka)');
$kartOrg = $xpath->evaluate('string(//Kart_Org)');

// Eğer API'den gelen değerler boşsa, hatayı yazdır
if (empty($kartBanka) && empty($kartMarka) && empty($kartOrg)) {
    echo "API yanıtı beklenmeyen formatta: " . htmlspecialchars($response);
    exit;
}

// Sonuçları döndür
echo "$kartBanka,$kartMarka,$kartOrg";
?>
