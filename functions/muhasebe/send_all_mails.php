<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../vendor/autoload.php'; // Composer autoload
require_once '../db.php';
$mj = new \Mailjet\Client('71eeafe78bebd4ef41bdb89de81e2652', '06960d03ac03eb978282ea0777326a26', true, ['version' => 'v3.1']);

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

    $sql = "SELECT * FROM vadesi_gecmis_borc WHERE email IS NOT NULL AND email != '' AND id = '11331";
    $borclar = $database->fetchAll($sql);

    if (!$borclar) {
        throw new Exception('Mail gönderilecek borç kaydı bulunamadı.');
    }

    $messages = [];
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
        $content = vadeGecikmeHatirlatma($borc, $odemeUrl);

        $messages[] = [
            'From' => [
                'Email' => "tahsilat@noktaelektronik.net",
                'Name'  => "Nokta Net Tahsilat"
            ],
            'To' => [
                [
                    'Email' => $borc['email'],
                    'Name'  => $borc['ticari_unvani']
                ]
            ],
            'Subject' => "Vadesi Geçmiş Borç Hatırlatması",
            'HTMLPart' => $content,
            'CustomID' => "borc_" . $borc['id']
        ];

        $basarili++;
    }

    // Tüm mailleri tek seferde gönder
    $body = ['Messages' => $messages];
    $response = $mj->post(Resources::$Email, ['body' => $body]);

    if ($response->success()) {
        echo json_encode([
            'success' => true,
            'message' => "$basarili mail başarıyla gönderildi.",
            'failures' => $basarisiz
        ]);
    } else {
        echo "<pre>";
        echo "MailJet API Hatası\n";
        echo "HTTP Kod: " . $response->getStatus() . "\n";
        echo "Yanıt Gövdesi:\n";
        print_r($response->getBody());
        echo "</pre>";
        exit; // JSON yerine direkt çıktı ver, hatayı yakalayalım
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
