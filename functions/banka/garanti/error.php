<?php
session_start();
require_once(__DIR__ . '/../../db.php');
$database = new Database();

$mdstatus = $_POST['mdstatus'] ?? '';
$mderrormessage = $_POST['mderrormessage'] ?? '';
$orderid = $_POST['orderid'] ?? $_POST['oid'] ?? '';


if (empty($orderid)) {
    header("Location: ../../../pages/b2b/b2b-sanalpos?w=noktab2b&garerr=Eksik orderid");
    exit();
}

$gpos = $database->fetch(
    "SELECT * FROM gpos WHERE is_active = 0 AND siparis_no = :siparis_no",
    ['siparis_no' => $orderid]
);

if (!$gpos) {
    header("Location: ../../../pages/b2b/b2b-sanalpos?w=noktab2b&garerr=Sipariş bulunamadı");
    exit();
}

// 3️⃣ GPOS kaydından verileri al
$uye_id = $gpos['uye_id'];
$tutar = $gpos['toplam'] ?? 0; 
$errmsg = $mderrormessage;
$pos_id = 2;
$basarili = 0;

// 4️ İlgili GPOS kaydını aktif hale getir
$database->update(
    "UPDATE gpos SET is_active = 1 WHERE siparis_no = :siparis_no",
    ['siparis_no' => $orderid]
);

// 5️⃣ b2b_sanal_pos_odemeler tablosuna kayıt ekle
$query = "INSERT INTO b2b_sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) 
          VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)";
$params = [
    'uye_id' => $uye_id,
    'pos_id' => $pos_id,
    'islem' => $errmsg,
    'tutar' => $tutar,
    'basarili' => $basarili
];
$database->insert($query, $params);

// 6️ Yönlendirme (mderrormessage değeriyle birlikte)
$redirectUrl = "../../../pages/b2b/b2b-sanalpos?w=noktab2b&garerr=" . urlencode($mderrormessage);
header("Location: $redirectUrl");
exit();
?>
