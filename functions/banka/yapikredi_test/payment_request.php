<?php
date_default_timezone_set("Europe/Istanbul");
include 'config.php';

// Formdan gelen veriler
$ccno = $_POST['ccno'];
$cardHolderName = $_POST['cardHolderName'];
$expDate = $_POST['expDate'];
$cvc = $_POST['cvc'];
$amountInput = str_replace(',', '.', $_POST['amount']); // 12,34 -> 12.34
$amount = (int) round(floatval($amountInput) * 100); // kuruş cinsinden
$installment = $_POST['installment'];
$orderID = substr(md5(uniqid()), 0, 24); // 24 karaktere kadar benzersiz ID
$currencyCode = 'TL';

// XML hazırlığı
$xml = <<<XML
<?xml version="1.0" encoding="ISO-8859-9"?>
<posnetRequest>
    <mid>{MERCHANT_ID}</mid>
    <tid>{TERMINAL_ID}</tid>
    <oosRequestData>
        <posnetid>{POSNET_ID}</posnetid>
        <XID>{$orderID}</XID>
        <amount>{$amount}</amount>
        <currencyCode>{$currencyCode}</currencyCode>
        <installment>{$installment}</installment>
        <tranType>Sale</tranType>
        <cardHolderName>{$cardHolderName}</cardHolderName>
        <ccno>{$ccno}</ccno>
        <expDate>{$expDate}</expDate>
        <cvc>{$cvc}</cvc>
    </oosRequestData>
</posnetRequest>
XML;

$xml = str_replace(
    ['{MERCHANT_ID}', '{TERMINAL_ID}', '{POSNET_ID}'], 
    [MERCHANT_ID, TERMINAL_ID, POSNET_ID], 
    $xml
);

// Bankaya POST gönder
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, POSNET_URL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'xmldata=' . urlencode($xml));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$headers = [
    'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
    'X-MERCHANT-ID: ' . MERCHANT_ID,
    'X-TERMINAL-ID: ' . TERMINAL_ID,
    'X-POSNET-ID: ' . POSNET_ID,
    'X-CORRELATION-ID: ' . $orderID
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Header'ları göster
echo "<h3>Gönderilen Headers:</h3>";
echo "<pre>";
print_r($headers);
echo "</pre>";

// URL'yi göster
echo "<h3>Gönderilen URL:</h3>";
echo "<pre>" . POSNET_URL . "</pre>";

$response = curl_exec($ch);

// Hata ayıklama bilgileri
echo "<h3>Gönderilen XML:</h3>";
echo "<pre>" . htmlspecialchars($xml) . "</pre>";

echo "<h3>CURL Bilgileri:</h3>";
echo "<pre>";
print_r(curl_getinfo($ch));
echo "</pre>";

if (curl_errno($ch)) {
    echo "<h3>CURL Hatası:</h3>";
    echo curl_error($ch);
}

curl_close($ch);

// Yanıtı işle
if ($response) {
    echo "<h3>Bankadan Gelen Yanıt:</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    $xmlResponse = simplexml_load_string($response);
    echo "<h3>İşlenmiş Yanıt:</h3>";
    echo "<pre>"; print_r($xmlResponse); echo "</pre>";

    if ((string)$xmlResponse->approved === '1') {
        echo "✅ İşlem Başarılı. HostLogKey: {$xmlResponse->hostlogkey}";
    } else {
        echo "❌ Hata: {$xmlResponse->respText}";
    }
} else {
    echo "❌ Bankaya bağlanılamadı.";
}
