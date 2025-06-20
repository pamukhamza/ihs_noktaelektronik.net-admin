<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../db.php';
include_once '../../mail/tahsilat_mail_gonder.php';

header('Content-Type: application/json');

try {
    $database = new Database();

    // Emaili dolu olan tüm kayıtları çek
    $sql = "SELECT * FROM vadesi_gecmis_borc WHERE email IS NOT NULL AND email != ''";
    $borclar = $database->fetchAll($sql);

    if (!$borclar) {
        throw new Exception('Mail gönderilecek borç kaydı bulunamadı.');
    }

    $basarili = 0;
    $basarisiz = [];

    foreach ($borclar as $borc) {
        $veri = [
            'cari_kodu'     => $borc['cari_kodu'],
            'ticari_unvani' => $borc['ticari_unvani'],
            'geciken_tutar' => $borc['geciken_tutar'],
            'borc_bakiye'   => $borc['borc_bakiye'],
            'bilgi_kodu'    => $borc['bilgi_kodu']
        ];

        $sifreli = base64_encode(json_encode($veri));

        // Linki güncelle
        $updateSql = "UPDATE vadesi_gecmis_borc SET odeme_link = :odeme_link WHERE id = :id";
        $update = $database->insert($updateSql, [
            'odeme_link' => $sifreli,
            'id' => $borc['id']
        ]);

        if (!$update) {
            $basarisiz[] = $borc['id'];
            continue;
        }

        $odemeUrl = "https://www.noktaelektronik.com.tr/tr/tahsilat.php?l=" . urlencode($sifreli);
        $subject = "Vadesi Geçmiş Borç Hatırlatması";
        $mailContent = vadeGecikmeHatirlatma($borc, $odemeUrl);
        $mailBaslik = "Nokta Net Tahsilat";

        $mailResult = mailGonder($borc['email'], $subject, $mailContent, $mailBaslik);

        if ($mailResult) {
            $basarili++;
        } else {
            $basarisiz[] = $borc['id'];
        }
    }

    echo json_encode([
        'success' => true,
        'message' => "$basarili mail başarıyla gönderildi.",
        'failures' => $basarisiz
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
