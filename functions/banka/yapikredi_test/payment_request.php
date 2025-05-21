<?php
date_default_timezone_set("Europe/Istanbul");
include 'config.php';

// Formdan gelen veriler
$ccno = $_POST['ccno'];
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
    <tranDateRequired>1</tranDateRequired>
    <sale>
        <amount>{$amount}</amount>
        <ccno>{$ccno}</ccno>
        <currencyCode>{$currencyCode}</currencyCode>
        <cvc>{$cvc}</cvc>
        <expDate>{$expDate}</expDate>
        <orderID>{$orderID}</orderID>
        <installment>{$installment}</installment>
    </sale>
</posnetRequest>
XML;

$xml = str_replace(['{MERCHANT_ID}', '{TERMINAL_ID}'], [MERCHANT_ID, TERMINAL_ID], $xml);

// Bankaya POST gönder
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, POSNET_URL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'xmldata=' . urlencode($xml));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
    'X-MERCHANT-ID: ' . MERCHANT_ID,
    'X-TERMINAL-ID: ' . TERMINAL_ID,
    'X-POSNET-ID: ' . POSNET_ID,
    'X-CORRELATION-ID: ' . $orderID
]);

$response = curl_exec($ch);
curl_close($ch);

// Yanıtı işle
if ($response) {
    $xmlResponse = simplexml_load_string($response);
    echo "<pre>"; print_r($xmlResponse); echo "</pre>";

    if ((string)$xmlResponse->approved === '1') {
        echo "✅ İşlem Başarılı. HostLogKey: {$xmlResponse->hostlogkey}";
    } else {
        echo "❌ Hata: {$xmlResponse->respText}";
    }
} else {
    echo "❌ Bankaya bağlanılamadı.";
}
