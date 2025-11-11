<?php
session_start();

require_once(__DIR__ . '/../../db.php');
$database = new Database();

$mdstatus = $_POST['mdstatus'] ?? '';
$mderrormessage = $_POST['mderrormessage'] ?? '';
$orderid = $_POST['orderid'] ?? $_POST['oid'] ?? '';

if (empty($orderid)) {
    header("Location: ../../../pages/b2b/b2b-sanalpos?w=noktab2b&garantiodeme=0&error=Eksik orderid");
    exit();
}

$gpos = $database->fetch("SELECT * FROM gpos WHERE is_active = 0 AND siparis_no = :siparis_no", [
    'siparis_no' => $orderid
]);

if (!$gpos) {
    //echo $orderid;
    header("Location: ../../../pages/b2b/b2b-sanalpos?w=noktab2b&garantiodeme=0&error=Sipariş bulunamad");
    exit();
}

$kur = $database->fetch("SELECT * FROM b2b_kurlar WHERE id = :id", ['id' => '2']);
$satis_dolar = $kur['satis'];
$alis_dolar = $kur['alis'];

$dov_al = str_replace('.', ',', $alis_dolar);
$dov_sat = str_replace('.', ',', $satis_dolar);

$uye_id = $gpos['uye_id'];
$uye = $database->fetch("SELECT firmaUnvani, BLKODU, email FROM uyeler WHERE id = :id", ['id' => $uye_id]);

$cardNo = $gpos["kart_no"];
$cardHolder = $gpos["kart_sahibi"];
$banka_id = $gpos["banka_id"];
$hesap = $gpos["hesap"];
$taksit_sayisi = $gpos["taksit_sayisi"];
$lang = "tr";
$siparisNumarasi = $orderid;
$tutar = $gpos['toplam'] ?? 0;
$currentDateTime = date("d.m.Y H:i:s");
$degistirme_tarihi = date("d.m.Y H:i:s", strtotime($currentDateTime . " +3 hours"));
$doviz = ($hesap == 1) ? "$" : "TL";

$banka_pos = $database->fetch("SELECT * FROM b2b_banka_pos_listesi WHERE id = :id", ['id' => $banka_id]);

$paymentData = [
    'uye_id' => $uye_id,
    'firmaUnvani' => $uye['firmaUnvani'],
    'maskedCardNo' => $cardNo,
    'cardHolder' => $cardHolder,
    'taksit_sayisi' => $taksit_sayisi,
    'yantoplam' => $tutar,
    'degistirme_tarihi' => $degistirme_tarihi,
    'uyecarikod' => $uye['BLKODU'],
    'hesap' => $hesap,
    'currentDateTime' => $currentDateTime,
    'dov_al' => $dov_al,
    'dov_sat' => $dov_sat,
    'siparisNumarasi' => $siparisNumarasi,
    'blbnhskodu' => $banka_pos["BLBNHSKODU"],
    'banka_adi' => $banka_pos["BANKA_ADI"],
    'doviz' => $doviz,
    'banka_tanimi' => $banka_pos["TANIMI"],
    'uye_mail' => $uye['email']
];

if ($_POST['mdstatus'] == '1') {
    //echo "Ödeme başarılı!";
    include_once(__DIR__ . '/../../functions.php'); 
    include_once(__DIR__ . '/../../../mail/mail_gonder.php'); 
    include_once(__DIR__ . '/../../banka/dekontolustur.php'); 
    require_once(__DIR__ . '/../../wolvox/pos_olustur.php'); 
    require_once(__DIR__ . '/../handlers/POSHandler.php');

    // Anonim sınıf ile direkt POSHandler kullanımı
    $handler = new class($database, $paymentData) extends POSHandler {
        public function processPayment() {
            // POST mdStatus ve Response kontrolü
            $mdStatus = $_POST['mdstatus'] ?? '';
            $response = $_POST['Response'] ?? '';
    
            if ($mdStatus == '1' ) {
                // Ödeme başarlı
                $inserted_id = $this->saveTransaction(
                    106,
                    "Ödeme işlemi başarılı",
                    $this->paymentData['yantoplam'],
                    1,
                    $_POST['transid'] ?? null,
                    $_POST['orderid'] ?? null
                );
    
                $this->handleSuccess($inserted_id);
                $this->redirect("https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b&garantiodeme=1");
            } else {
                // Ödeme başarısz
                $errorMessage = "Ödeme başarısız - MDStatus: $mdStatus - Response: $response";
                $this->saveTransaction(
                    106,
                    $errorMessage,
                    $this->paymentData['yantoplam'],
                    0,
                    $_POST['transid'] ?? null,
                    $_POST['orderid'] ?? null
                );
                $this->redirect("https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b&garerr=" . urlencode($errorMessage));
            }
        }
    };
    
    $handler->processPayment();    
} else {
    echo "Ödeme başarısız: " . htmlspecialchars($result['message']);
}
?>