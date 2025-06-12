<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function mailGonder($alici, $konu, $mesaj_icerik, $mailbaslik){
    include 'Exception.php';
    include 'PHPMailer.php';
    include 'SMTP.php';
    $mail = new PHPMailer(true);
    //Server settings
    $mail->SMTPDebug = 0; // Enable verbose debug output (set to 2 for maximum detail)
    $mail->isSMTP(); // Set mailer to use SMTP
    $mail->Host = 'mail.noktaelektronik.net';
    $mail->SMTPAuth = true;
    $mail->Username = 'nokta\b2b';
    $mail->Password = 'Nktbb2023*';
    $mail->SMTPSecure = 'tls'; // veya 'tls'
    $mail->Port = 587; // TLS için 587, SSL için 465
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    //Recipients
    $mail->setFrom('b2b@noktaelektronik.net', $mailbaslik);
    $mail->addAddress($alici); // Add a recipient
    $mail->addBCC("muhasebe@noktaelektronik.net");
    //Content
    $mail->Subject = $konu;
    $mail->Body = "$mesaj_icerik";
    // Set email format to HTML
    $mail->isHTML(true);
    // Try to send the email
    try {
        $mail->send();
    } catch (Exception $e) {
    }
}
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

