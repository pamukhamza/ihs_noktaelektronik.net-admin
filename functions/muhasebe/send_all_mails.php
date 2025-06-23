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

header('Content-Type: application/json');

function getMailTemplate($content, $title = '') {
    ob_start();
    ?>
    <table style="margin-left: auto; margin-right: auto; height:auto; width: 100%; min-width: 350px; max-width: 750px; font-family: 'Source Sans Pro', Arial, Tahoma, Geneva, sans-serif;"></table>

    <table style="max-width: 750px; width: 100%; background-color: rgb(72,4,102);">
        <tbody>
            <tr>
                <td align="center" style="padding: 20px 0; width: 100%;">
                    <a href="www.noktaelektronik.com.tr" target="_blank">
                        <img src="https://www.noktaelektronik.com.tr/assets/images/nokta-logo-beyaz.png" width="30%" />
                    </a>
                </td>
            </tr>
        </tbody>
    </table>

    <?php if($title): ?>
    <table style="margin-top: 10px; width: 100%; max-width: 750px;">
        <tbody>
            <tr>
                <td align="center" style="font-size: 30px;"><strong><?= $title ?></strong></td>
            </tr>
        </tbody>
    </table>
    <?php endif; ?>
    <?= $content ?>
    <?= getMailFooter() ?>
    <?php
    return ob_get_clean();
}
function getMailFooter() {
    return '
    <table style="margin-top: 16px; width: 100%; max-width: 750px;">
        <tr>
            <td align="center" style="background-color: rgb(70, 70, 70); padding: 20px;">
                <span style="color: #f1f1f1; font-size: 17px;">
                    <a href="mailto:muhasebe@noktaelektronik.net" style="color: #f1f1f1; text-decoration: none;">muhasebe@noktaelektronik.net</a> |
                    <a href="tel:08503330208" style="color: #f1f1f1; text-decoration: none;">0850 333 02 08</a> |
                    <a href="https://noktaelektronik.com.tr/" style="color: #f1f1f1; text-decoration: none;">www.noktaelektronik.com.tr</a>
                </span>
                <!-- Sosyal medya ikonları buraya gelecek -->
            </td>
        </tr>
    </table>';
}
function vadeGecikmeHatirlatma($borc, $odemeUrl) {

    $content = "
    <table style='margin-top: 10px; width: 100%; max-width: 750px; font-family: Arial, sans-serif;'>
        <tr>
            <td align='center' style='font-size: 20px;'>Sayın <strong>{$borc['ticari_unvani']}</strong>,</td>
        </tr>
        <tr>
            <td align='center' style='padding: 10px 0; font-size: 18px;'>
                Vadesi geçmiş borcunuz bulunmaktadır.
            </td>
        </tr>
        <tr>
            <td align='center' style='font-size: 16px; color: red;'>
                Vadesi Geçmiş Borç: <strong>" . number_format($borc['geciken_tutar'], 2, ',', '.') . " ₺</strong>
            </td>
        </tr>
        <tr>
            <td align='center' style='font-size: 16px; color: #000;'>
                Tüm Borcunuz: <strong>" . number_format($borc['borc_bakiye'], 2, ',', '.') . " ₺</strong>
            </td>
        </tr>
        <tr>
            <td align='center' style='padding: 20px;'>
                <a href='{$odemeUrl}' style='
                    background-color: #4CAF50;
                    color: white;
                    padding: 10px 20px;
                    text-decoration: none;
                    border-radius: 5px;
                    font-size: 16px;
                    margin-right: 10px;
                    display: inline-block;
                '>
                    💳 Kart ile Ödeme Yapmak için Tıklayın
                </a></br>
                <a href='https://noktanet.s3.eu-central-1.amazonaws.com/uploads/muhasebe/ibanbilgilerimiz.pdf' style='
                    background-color: #2196F3;
                    color: white;
                    padding: 10px 20px;
                    text-decoration: none;
                    border-radius: 5px;
                    font-size: 16px;
                    display: inline-block;
                    margin-top: 10px;
                '>
                    🏦 Havale / EFT ile Ödeme için Tıklayın
                </a>
            </td>
        </tr>
    </table>
    ";
    return getMailTemplate($content, 'Vadesi Geçmiş Borç Hatırlatması');
}


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
    $mail->Host = 'mail.noktaelektronik.net';
    $mail->SMTPAuth = true;
    $mail->Username = 'nokta\tahsilat';
    $mail->Password = 'Nktths2025!?!*';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
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
    $mail->setFrom('tahsilat@noktaelektronik.net', 'Nokta Net Tahsilat');
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
