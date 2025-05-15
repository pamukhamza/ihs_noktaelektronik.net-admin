<?php
require_once 'config.php';

function createHash($terminalId, $orderId, $amount, $successUrl, $failUrl, $storeKey, $provUserId, $txType, $instalment) {
    $hashstr = $terminalId . $orderId . $amount . $successUrl . $failUrl . $txType . $instalment . $storeKey;
    return base64_encode(pack('H*', sha1($hashstr)));
}

// Alınan veriler
$orderId = "ORD" . time(); // sipariş numarası
$amount = number_format($_POST['tutar'], 2, '', ''); // Örn: 1000 yerine 100000
$instalment = ""; // taksit sayısı, boşsa tek çekim

$successUrl = RETURN_URL;
$failUrl = RETURN_URL;

$hash = createHash(
    TERMINAL_ID,
    $orderId,
    $amount,
    $successUrl,
    $failUrl,
    STORE_KEY,
    MERCHANT_ID,
    "Auth",
    $instalment
);
?>

<form action="https://entegrasyon.yapikredi.com.tr/3DSWebService/YKBPaymentService" method="post" id="ykbForm">
  <input type="hidden" name="OrderId" value="<?= $orderId ?>">
  <input type="hidden" name="PurchAmount" value="<?= $amount ?>">
  <input type="hidden" name="OkUrl" value="<?= $successUrl ?>">
  <input type="hidden" name="FailUrl" value="<?= $failUrl ?>">
  <input type="hidden" name="TxnType" value="Auth">
  <input type="hidden" name="InstallmentCount" value="<?= $instalment ?>">
  <input type="hidden" name="MerchantId" value="<?= MERCHANT_ID ?>">
  <input type="hidden" name="UserId" value="xxxx"> <!-- Prov user -->
  <input type="hidden" name="SecureType" value="3DPay">
  <input type="hidden" name="Hash" value="<?= $hash ?>">
</form>

<script>document.getElementById('ykbForm').submit();</script>
