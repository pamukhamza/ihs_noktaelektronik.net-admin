<?php
include_once '../db.php';
include_once '../../mail/tahsilat_mail_gonder.php'; // mailGonder ve vadeGecikmeHatirlatma fonksiyonları burada tanımlı olmalı

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    
    $id = $_POST['id'] ?? null;

    if ($id) {
        $sql = "SELECT * FROM vadesi_gecmis_borc WHERE id = :id";
        $params = ['id' => $id];
        $borc = $database->fetch($sql, $params);

        if ($borc) {
            // 🔐 Şifrelenecek veriler
            $veri = [
                'cari_kodu'      => $borc['cari_kodu'],
                'ticari_unvani'  => $borc['ticari_unvani'],
                'geciken_tutar'  => $borc['geciken_tutar'],
                'borc_bakiye'    => $borc['borc_bakiye'],
                'bilgi_kodu'     => $borc['bilgi_kodu']
            ];

            // 🔁 JSON + base64 encode
            $sifreli = base64_encode(json_encode($veri));

            // 📝 odeme_link alanını güncelle
            $updateSql = "UPDATE vadesi_gecmis_borc SET odeme_link = :odeme_link WHERE id = :id";
            $database->execute($updateSql, ['odeme_link' => $sifreli, 'id' => $id]);

            // 🔗 Link adresi
            $odemeUrl = "https://www.siteniz.com/odeme.php?data=" . urlencode($sifreli);

            // 📧 Mail içeriği
            $subject = "Vadesi Geçmiş Borç Hatırlatması";
            $mailContent = vadeGecikmeHatirlatma($borc, $odemeUrl);
            $mailBaslik = "Nokta Net Tahsilat";
            $aliciMail = $borc['email'];

            // ✉️ Mail gönder
            mailGonder($aliciMail, $subject, $mailContent, $mailBaslik);

            echo json_encode(['success' => true]);
            exit;
        }
    }
}

echo json_encode(['success' => false]);
