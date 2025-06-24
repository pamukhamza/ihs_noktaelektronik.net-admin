<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../../mail/PHPMailer.php';
require_once '../../mail/SMTP.php';
require_once '../../mail/Exception.php';
require_once '../db.php';
require_once '../../mail/tahsilat_mail_gonder.php';

header('Content-Type: application/json');

try {
    $database = new Database();

    // Emaili dolu olan tüm kayıtları çek
    $sql = "SELECT * FROM vadesi_gecmis_borc WHERE email IS NOT NULL AND email != ''";
    $borclar = $database->fetchAll($sql);

    if (!$borclar) {
        throw new Exception('Mail gönderilecek borç kaydı bulunamadı.');
    }

    // PHPMailer nesnesini başlat
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'cp05.ihscp.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'tahsilat@mail.noktaelektronik.com.tr';
    $mail->Password = 'Dell28736.!'; // <<< E-posta şifresini buraya girin
    $mail->SMTPSecure = 'ssl'; // SSL bağlantı için
    $mail->Port = 465;
    $mail->SMTPKeepAlive = true;
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->setFrom('tahsilat@noktaelektronik.com.tr', 'Nokta Net Tahsilat');
    $mail->addBCC('muhasebe@noktaelektronik.net');
    $mail->isHTML(true);

    $basarili = 0;
    $basarisiz = [];

    foreach ($borclar as $borc) {
        try {
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
            $mailContent = vadeGecikmeHatirlatma($borc, $odemeUrl);

            // Alıcıları sıfırla ve yeni alıcıyı ekle
            $mail->clearAddresses();
            $mail->addAddress($borc['email']);

            $mail->Subject = "Vadesi Geçmiş Borç Hatırlatması";
            $mail->Body = $mailContent;

            $mail->send();
            $basarili++;
        } catch (Exception $e) {
            $basarisiz[] = $borc['id'];
            error_log("Mail gönderilemedi - ID: {$borc['id']} - Hata: " . $mail->ErrorInfo);
            continue;
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
