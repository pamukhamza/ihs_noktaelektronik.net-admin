<?php
session_start();
require_once(__DIR__ . '/../../db.php');
$database = new Database();

require_once(__DIR__ . '/../handlers/GarantiPOSHandler.php');

$uye_id = $_POST['uye_id'] ?? 0;
$toplam = $_POST['toplam'] ?? 0;
$hesap = $_POST['hesap'] ?? 0;
$installment = $_POST['installment'] ?? '00';
$cardHolderName = $_POST['cardHolderName'] ?? '';
$ccno = $_POST['ccno'] ?? '';
$expMonth = $_POST['expMonth'] ?? '';
$expYear = $_POST['expYear'] ?? '';
$cvc = $_POST['cvc'] ?? '';
$banka_id = $_POST['banka_id'] ?? 106;

// Veri doğrulama
if (empty($uye_id) || $uye_id == 0) {
    header("Location: ../../../pages/b2b/b2b-sanalpos?w=noktab2b&error=firma_secin");
    exit();
}

if (empty($toplam) || $toplam <= 0) {
    header("Location: ../../../pages/b2b/b2b-sanalpos?w=noktab2b&error=tutar_girin");
    exit();
}

if (empty($cardHolderName) || empty($ccno) || empty($expMonth) || empty($expYear) || empty($cvc)) {
    header("Location: ../../../pages/b2b/b2b-sanalpos?w=noktab2b&error=kart_bilgileri");
    exit();
}

// Üye bilgilerini al
$uye = $database->fetch("SELECT * FROM uyeler WHERE id = :id", ['id' => $uye_id]);
if (!$uye) {
    header("Location: ../../../pages/b2b/b2b-sanalpos?w=noktab2b&error=uye_bulunamadi_ilk");
    exit();
}

// Taksit sayısını hesapla
$taksit_sayisi = ($installment == '00') ? 1 : intval($installment);

// Tutarı kuruş cinsine çevir
$yantoplam = str_replace(',', '.', $toplam) * 100;

// Tarih bilgileri
$currentDateTime = date('Y-m-d H:i:s');
$degistirme_tarihi = date('Y-m-d');

// Sipariş numarası oluştur
$siparisNumarasi = 'NOKGAR' . date('YmdHis') . rand(1000, 9999);

try {
    $database->insert("
        INSERT INTO gpos 
        (uye_id, banka_id, hesap, toplam, taksit_sayisi, kart_sahibi, kart_no, son_kullanma_ay, son_kullanma_yil, cvc, siparis_no, olusturma_tarihi, is_active)
        VALUES 
        (:uye_id, :banka_id, :hesap, :toplam, :taksit_sayisi, :kart_sahibi, :kart_no, :son_kullanma_ay, :son_kullanma_yil, :cvc, :siparis_no, :olusturma_tarihi, :is_active)
    ", [
        'uye_id' => $uye_id,
        'banka_id' => $banka_id,
        'hesap' => $hesap,
        'toplam' => $toplam,
        'taksit_sayisi' => $taksit_sayisi,
        'kart_sahibi' => $cardHolderName,
        'kart_no' => substr($ccno, 0, 6) . '******' . substr($ccno, -4), // Güvenlik için maskeli kayıt
        'son_kullanma_ay' => $expMonth,
        'son_kullanma_yil' => $expYear,
        'cvc' => '***', // Güvenlik için CVC saklanmaz
        'siparis_no' => $siparisNumarasi,
        'olusturma_tarihi' => $currentDateTime,
        'is_active' => 0 // varsayılan olarak pasif
    ]);
} catch (Exception $e) {
    error_log('GPOS kayıt hatası: ' . $e->getMessage());
}

// Garanti ayarlarını yükle
require_once(__DIR__ . '/core/settings/PosSettings.php');
require_once(__DIR__ . '/core/enums/RequestMode.php');
require_once(__DIR__ . '/core/entity/ThreeDPayment.php');
require_once(__DIR__ . '/core/GarantiPaymentProcess.php');

use Gosas\Core\Settings\PosSettings;
use Gosas\Core\Enums\RequestMode;
use Gosas\Core\GarantiPaymentProcess;

try {
    // Garanti ödeme işlemini başlat
    $settings = new PosSettings(RequestMode::Prod);
    $paymentProcess = new GarantiPaymentProcess();
    
    // Sipariş bilgilerini hazırla
    $paymentProcess->PrepareOrder();
    $paymentProcess->request->order->orderId = $siparisNumarasi;
    $paymentProcess->request->order->amount = $yantoplam;
    $paymentProcess->request->order->currency = 949; // TL
    
    // Müşteri bilgilerini hazırla
    $paymentProcess->PrepareCustomer();
    $paymentProcess->request->customer->emailAddress = $uye['email'] ?? 'test@example.com';
    $paymentProcess->request->customer->ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    
    
    // 3D Secure ödeme parametrelerini hazırla
    $params = $paymentProcess->PrepareThreeDPayment(
        $siparisNumarasi,
        $yantoplam,
        949, // TL
        $taksit_sayisi,
        'sales'
    );
    // 3D Secure formunu oluştur
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <title>3D Secure Ödeme</title>
            <meta charset="utf-8">
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
            </style>
        </head>
        <body>
            <p>Bankanın ödeme sayfasına yönlendiriliyorsunuz ...</p>
            <form id="payment-form" method="post" action="https://sanalposprov.garanti.com.tr/servlet/gt3dengine">
                <input type="hidden" name="mode" value="PROD" />
                <input type="hidden" name="apiversion" value="512" />
                <input type="hidden" name="terminalprovuserid" value="<?php echo $settings->provUserId ?>" />
                <input type="hidden" name="terminaluserid" value="<?php echo $settings->provUserId3DS ?>" />
                <input type="hidden" name="terminalmerchantid" value="<?php echo $settings->merchantId ?>" />
                <input type="hidden" name="txntype" value="<?php echo $params->type ?>" />
                <input type="hidden" name="txncurrencycode" value="<?php echo $params->currency ?>" />
                <input type="hidden" name="txninstallmentcount" value="<?php echo $taksit_sayisi ?>" />
                <input type="hidden" name="txnamount" value="<?php echo $yantoplam ?>" />
                <input type="hidden" name="orderid" value="<?php echo $siparisNumarasi ?>" />
                <input type="hidden" name="terminalid" value="<?php echo $settings->terminalId ?>" />
                <input type="hidden" name="successurl" value="<?php echo $params->successUrl ?>" />
                <input type="hidden" name="errorurl" value="<?php echo $params->errorUrl ?>" />
                <input type="hidden" name="customeremailaddress" value="<?php echo $paymentProcess->request->customer->emailAddress ?>" />
                <input type="hidden" name="customeripaddress" value="<?php echo $paymentProcess->request->customer->ipAddress ?>" />
                <input type="hidden" name="companyname" value="NOKTA ELEKTRONİK" />
                <input type="hidden" name="lang" value="tr" />
                <input type="hidden" name="txntimestamp" value="<?php echo date("h:i:sa") ?>" />
                <input type="hidden" name="refreshtime" value="1" />
                <input type="hidden" name="secure3dhash" value="<?php echo $params->hashedData ?>" />
                <input type="hidden" name="secure3dsecuritylevel" value="3D_PAY" />
                
                <input type="hidden" name="cardholdername" value="<?php echo htmlspecialchars($cardHolderName) ?>" readonly />
                <input type="hidden" name="cardnumber" value="<?php echo htmlspecialchars($ccno) ?>" readonly />
                <input type="hidden" name="cardexpiredatemonth" value="<?php echo htmlspecialchars($expMonth) ?>" readonly />
                <input type="hidden" name="cardexpiredateyear" value="<?php echo htmlspecialchars($expYear) ?>" readonly />
                <input type="hidden" name="cardcvv2" value="<?php echo htmlspecialchars($cvc) ?>" readonly />
                <input type="hidden" value="<?php echo number_format($toplam, 2) ?> TL" readonly />
                <input type="hidden" value="<?php echo $taksit_sayisi == 1 ? 'Pein' : $taksit_sayisi . ' Taksit' ?>" readonly />
                <button style="visibility: hidden;" type="submit">Ödemeyi Tamamla</button>
            </form>
            <script>
                document.getElementById('payment-form').submit();
            </script>
        </body>
    </html>
    <?php
    
} catch (Exception $e) {
    error_log('Garanti Payment Request Error: ' . $e->getMessage());
    header("Location: ../../../pages/b2b/b2b-sanalpos?w=noktab2b&error=system&message=" . urlencode($e->getMessage()));
    exit();
}
?>