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
    if($konu == "Cari Ödeme Bildirimi"){
        $mail->addBCC("muhasebe@noktaelektronik.net");
    }
    if($konu != "Arızalı Cihaz Durumu!"){
        $mail->addBCC("h.pamuk@noktaelektronik.net");
        $mail->addBCC("kadir@noktaelektronik.net");
    }
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
                    <a href="mailto:b2b@noktaelektronik.net" style="color: #f1f1f1; text-decoration: none;">b2b@noktaelektronik.net</a> |
                    <a href="tel:08503330208" style="color: #f1f1f1; text-decoration: none;">0850 333 02 08</a> |
                    <a href="https://noktaelektronik.com.tr/" style="color: #f1f1f1; text-decoration: none;">www.noktaelektronik.com.tr</a>
                </span>
                <!-- Sosyal medya ikonları buraya gelecek -->
            </td>
        </tr>
    </table>';
}
function cariOdeme($uye,$fiyat,$taksit){
    $content = "
    <table style='margin-top: 10px; width: 100%; max-width: 750px;'>
        <tbody>
        <tr>
            <td align='center' style='width: 100%; height: 35px; line-height: 35px; max-width: 750px;'><img src='https://ci5.googleusercontent.com/proxy/F8CvHq6tqRXdMWR2SJ6TZ4mgz1ToO4x4hjadwMx9DJPdylF_gApmvzsh_p2z5APOkhEb3iMwfDSaxatv3BSgr8mp9XaMJZSvPcjR96Bz1r4g1hU144Gej1sWUA=s0-d-e1-ft#https://images.hepsiburada.net/banners/0/imageUrl2089_20200917121500.png' width='48' /></td>
        </tr>
        <tr style='margin-top: 20px;'>
            <td align='center' style='margin-top: 20px; width: 100%; height: 30px; line-height: 30px; max-width: 750px; text-align: center; min-width: 350px; font-size: 20px;'><span style='font-size:20px;'>Sayın<strong> {$uye} </strong>,</span></td>
        </tr>
        <tr>
            <td align='center' style='width: 100%;  line-height: 30px; max-width: 750px; text-align: center; min-width: 350px; font-size: 20px;'><span style='font-size:20px;'>Ödemeniz için teşekkür ederiz.<br />
        </tr>
        <tr>
            <td align='center' style='width: 100%;  line-height: 30px; max-width: 750px; text-align: center; min-width: 350px; font-size: 20px;'><span style='font-size:20px;'>Ödenen Tutar: {$fiyat} <br/>
        </tr>
        <tr>
            <td align='center' style='width: 100%;  line-height: 30px; max-width: 750px; text-align: center; min-width: 350px; font-size: 20px;'><span style='font-size:20px;'>Taksit: {$taksit} <br />
        </tr>
        </tbody>
    </table>    
    ";
    
    return getMailTemplate($content, 'Cari Ödeme Bildirimi');
}
function siparisKargolamaAsamasinda($uye, $siparis_no, $siparis_tarihi){
    $content = "
    <div style='font-size: 20px; line-height: 30px;'>
        <p>Sayın $uye;</p>
        <p>Siparişiniz kargolama aşamasına geçmiştir.</p>
        <p>Sipariş No: $siparis_no</p>
        <p>Sipariş Tarihi: $siparis_tarihi</p>
    </div>";
    
    return getMailTemplate($content);
}
function siparisKargolandi($uye, $siparis_no, $siparis_tarihi, $kargo_no, $kargo_firma){
    $content = "
    <div style='font-size: 20px; line-height: 30px;'>
        <p>Sayın $uye;</p>
        <p>Siparişiniz $kargo_no kargo numarası ile $kargo_firma firmasına teslim edilmiştir.</p>
        <p>Sipariş No: $siparis_no</p>
        <p>Sipariş Tarihi: $siparis_tarihi</p>
        <p><a href='https://www.yurticikargo.com/tr/online-servisler/gonderi-sorgula?code=$kargo_no'>Buraya tıklayarak</a> siparişinizi takip edebilirsiniz.</p>
    </div>";
    
    return getMailTemplate($content);
}
function siparisTeslimEdildi($uye, $siparis_no, $siparis_tarihi){
    $content = "
    <div style='font-size: 20px; line-height: 30px;'>
        <p>Sayın $uye;</p>
        <p>Aşağıda detayları verilen siparişiniz teslimat adresinize teslim edilmiştir.</p>
        <p>Sipariş No: $siparis_no</p>
        <p>Sipariş Tarihi: $siparis_tarihi</p>
    </div>";
    
    return getMailTemplate($content);
}
function arizaKayitMail($uye, $takip) {
    $content = "
    <div style='font-size: 20px; line-height: 30px;'>
        <p>Sayın $uye;</p>
        <p><strong>Arıza Kaydınız Oluşturulmuştur.</strong></p>
        <p>Takip Kodu: $takip</p>
        <p><a href='https://www.noktaelektronik.com.tr/tr/teknik-destek'>Buraya tıklayarak</a> ürünlerinizin durumunu takip edebilirsiniz.</p>
    </div>";

    return getMailTemplate($content);
}
function islemiBitenAriza($uye, $takip, $urun_durumu, $urun_kodu) {
    $content = "
    <div style='font-size: 20px; line-height: 30px;'>
        <p>Sayın $uye;</p>
        <ul>
            <li><strong>$urun_kodu</strong> kodlu ürününüzün işlemi tamamlanmıştır.</li>
            <li><strong>Ürün Durumu:</strong> $urun_durumu</li>
        </ul>
        <p>Takip Kodu: $takip</p>
        <p><a href='https://www.noktaelektronik.com.tr/tr/teknik-destek'>Buraya tıklayarak</a> ürünlerinizin durumunu takip edebilirsiniz.</p>
    </div>";

    return getMailTemplate($content);
}
function sepetHatirlat($uyeid){
    $db = new Database();
    $query = "SELECT ad, soyad, fiyat FROM uyeler WHERE id = :uye_id";
    $result = $db->fetch($query, ['uye_id' => $uyeid]);
    $uyefiyat = $result['fiyat'];

    $dolar = $db->fetch("SELECT satis FROM b2b_kurlar WHERE id = 2");
    $dolarFiyat = $dolar['satis'];

    $euro = $db->fetch("SELECT satis FROM b2b_kurlar WHERE id = 3");
    $euroFiyat = $euro['satis'];
    ob_start();
    ?>
    <table style=" margin-left: auto; margin-right: auto;  height:auto; width: 100%; min-width: 350px; max-width: 750px; font-family: &quot;Source Sans Pro&quot;, Arial, Tahoma, Geneva, sans-serif;">
    </table>

    <table style="max-width: 750px; width: 100%; background-color: rgb(72,4,102);">
        <tbody>
        <tr>
            <td align="center" style="padding-top: 20px; padding-bottom: 20px; width: 100%; max-width: 750px; background-color: rgb(72,4,102);"><a href="www.noktaelektronik.com.tr" style="text-decoration: none; align-items: center; width: 250px;" target="_blank"><img src="https://www.noktaelektronik.com.tr/assets/images/nokta-logo-beyaz.png" width="30%"/></a></td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 10px; width: 100%; max-width: 750px;">
        <tbody>
        <tr>
            <td align="center" style="width: 100%; height: 35px; line-height: 35px; max-width: 750px;"><img src="https://ci5.googleusercontent.com/proxy/F8CvHq6tqRXdMWR2SJ6TZ4mgz1ToO4x4hjadwMx9DJPdylF_gApmvzsh_p2z5APOkhEb3iMwfDSaxatv3BSgr8mp9XaMJZSvPcjR96Bz1r4g1hU144Gej1sWUA=s0-d-e1-ft#https://images.hepsiburada.net/banners/0/imageUrl2089_20200917121500.png" width="48" /></td>
        </tr>
        <tr>
            <td align="center" style="width: 100%; height: 35px; line-height: 35px; max-width: 750px; text-align: center; min-width: 350px; margin-top: 25px; font-size: 30px;"><strong>Sepetiniz Tükenmeden Alın!</strong></td>
        </tr>
        <tr style="margin-top: 20px;">
            <td align="center" style="margin-top: 20px; width: 100%; height: 30px; line-height: 30px; max-width: 750px; text-align: center; min-width: 350px; font-size: 20px;"><span style="font-size:20px;"><strong>Sayın; <?= $result['ad'] ?> <?= $result['soyad'] ?> </strong></span></td>
        </tr>
        <tr>
            <td align="center" style="width: 100%;  line-height: 30px; max-width: 750px; text-align: center; min-width: 350px; font-size: 20px;"><span style="font-size:20px;">Sepetinizdeki ürünler tükenmeden hemen almak için <a href="https://www.noktaelektronik.com.tr">tıklayınız</a>.</span></td>
        </tr>
        </tbody>
    </table>
    <?php
        $urunlar = $db->fetchAll("SELECT su.*, nu.*, (SELECT nr.foto FROM nokta_urunler_resimler AS nr WHERE nr.urun_id = nu.BLKODU LIMIT 1) AS foto FROM b2b_uye_sepet AS su 
         LEFT JOIN nokta_urunler AS nu ON su.urun_id = nu.id
         WHERE su.uye_id = :uye_id " ,['uye_id' => $uyeid]);

    ?>
    <table style="margin-top: 10px; width: 100%; max-width: 750px; border: 1px solid #ccc; border-collapse: collapse; background-color: #f9f9f9;">
        <thead>
        <tr style="background-color: #430666">
            <th style="border: 1px solid #ccc; color:white">Fotoğraf</th>
            <th style="border: 1px solid #ccc; color:white">Ürün Adı</th>
            <th style="border: 1px solid #ccc; color:white">Stok Kodu</th>
            <th style="border: 1px solid #ccc; color:white">Miktar</th>
            <th style="border: 1px solid #ccc; color:white">Birim Fiyat</th>
            <th style="border: 1px solid #ccc; color:white">Toplam Fiyat</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($urunlar as $urun): ?>
            <tr>
                <td style="border: 1px solid #ccc; width: 60px">
                    <?php
                    // Fetch the image from the URL
                    $imageUrl = "https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/" . $urun["foto"];
                    $imageData = @file_get_contents($imageUrl);
                    // Initialize variables to store image data and base64 string
                    $base64Image = '';
                    // Check if image data was successfully fetched
                    if ($imageData !== false) {
                        // Get image size information, including MIME type
                        $imageInfo = @getimagesizefromstring($imageData);
                        // Check if image information was obtained successfully
                        if ($imageInfo !== false) {
                            $imageType = $imageInfo['mime'];
                            $base64Image = 'data:' . $imageType . ';base64,' . base64_encode($imageData);
                        } else {
                            // Handle the case where image information cannot be obtained
                            // Display an error message or take appropriate action
                        }
                    } else {
                        // Handle the case where the image data cannot be fetched
                        // Display an error message or take appropriate action
                    }
                    ?>
                    <?php if (!empty($base64Image)): ?>
                        <div><img width="100%" src="<?= $base64Image ?>"/></div>
                    <?php else: ?>
                        <div>Image not found</div>
                    <?php endif; ?>
                </td>
                <td style="border: 1px solid #ccc; text-align: center;"><?= $urun["UrunAdiTR"] ?></td>
                <td style="border: 1px solid #ccc; text-align: center;"><?= $urun["UrunKodu"] ?></td>
                <td style="border: 1px solid #ccc; text-align: center;"><?= $urun["adet"] ?></td>
                <td style="border: 1px solid #ccc; text-align: center;">
                    <?php
                    if(!empty($urun["DSF4"])){
                        if($urun["DOVIZ_BIRIMI"] == '$'){
                            $dovizimiz = $dolarFiyat;
                        }elseif ($urun["DOVIZ_BIRIMI"] == '€'){
                            $dovizimiz = $euroFiyat;
                        }
                        $birim_fiyat = $urun["DSF".$uyefiyat] * $dovizimiz;
                        $toplam_birim_fiyat = $urun["DSF".$uyefiyat] * $dovizimiz * $urun["adet"];
                        $formatted_birim_fiyat = number_format($birim_fiyat, 2, ',', '.');
                        $formatted_toplam_birim_fiyatDvz = number_format($toplam_birim_fiyat, 2, ',', '.');
                        echo $formatted_birim_fiyat . "₺ + KDV";
                    }else{
                        $birim_fiyat = $urun["KSF".$uyefiyat];
                        $toplam_birim_fiyat = $urun["KSF".$uyefiyat] * $urun["adet"];
                        $formatted_birim_fiyat = number_format($birim_fiyat, 2, ',', '.');
                        $formatted_toplam_birim_fiyatTL = number_format($toplam_birim_fiyat, 2, ',', '.');
                        echo $formatted_birim_fiyat . "₺ + KDV";
                    }
                    ?>
                </td>
                <td style="border: 1px solid #ccc; text-align: center;">
                    <?php
                    if(!empty($urun["DSF1"] || !empty($urun["DSF2"]) || !empty($urun["DSF3"]) || !empty($urun["DSF4"]) )){
                        echo $formatted_toplam_birim_fiyatDvz . "₺ + KDV";
                    }else{
                        echo $formatted_toplam_birim_fiyatTL . "₺ + KDV";
                    }
                    ?>

                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>
    <table style="margin-top: 30px; width: 100%; max-width: 750px;" width="750">
        <tbody>
        <tr>
            <td align="center" style="background-color: rgb(70, 70, 70);" valign="top">&nbsp;
                <table border="0" cellpadding="0" cellspacing="0" style="width: 100% !important; min-width: 100%; max-width: 100%;" width="100%">
                    <tbody>
                    <tr>
                        <td align="center" valign="top">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                <tr>
                                    <td align="center" valign="top">
                                        <div style="height: 34px; line-height: 34px; font-size: 14px;">&nbsp;</div>
                                        <span style="font-size:12px;"><span style="font-family:tahoma,geneva,sans-serif;"><font color="#f1f1f1" style="font-size: 17px; line-height: 16px;"><span style="line-height: 16px;"><a href="mailto:b2b@noktaelektronik.net" style="text-decoration: none; color: #f1f1f1;">b2b@noktaelektronik.net</a> |
                                        <a href="tel:08503330208" style="text-decoration: none; color: #f1f1f1;">0850 333 02 08</a> |
                                        <a href="https://noktaelektronik.com.tr/" style="text-decoration: none; color: #f1f1f1;">www.noktaelektronik.com.tr</a></span> </font></span></span>

                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tbody>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top"><a href="https://twitter.com/NEBSIS" style="display: block; max-width: 20px; text-decoration: none; color:#f1f1f1;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/25_x.png" width="30" style="display: block; width: 20px; " /></a></td>
                                                <td style="width: 10px; max-width: 10px; min-width: 10px;" width="10">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.facebook.com/nebsis" style="display: block; max-width: 20px; text-decoration: none; color:#f1f1f1;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/02_facebook.png" width="30" style="display: block; width: 20px; " /></a></td>
                                                <td style="width: 10px; max-width: 10px; min-width: 10px;" width="10">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.youtube.com/c/NoktaElektronikLTD" style="display: block; max-width: 20px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/03_youtube.png" width="30" style="display: block; width: 20px;" /></a></td>
                                                <td style="width: 10px; max-width: 10px; min-width: 10px;" width="10">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.instagram.com/noktaelektronik/" style="display: block; max-width: 20px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/10_instagram.png" width="30" style="display: block; width: 20px;" /></a></td>
                                                <td style="width: 10px; max-width: 10px; min-width: 10px;" width="10">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.linkedin.com/in/nokta-elektronik-57107b128/" style="display: block; max-width: 20px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/07_linkedin.png" width="30" style="display: block; width: 20px;" /></a></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>

    <?php
    $content = ob_get_contents(); // Tamponlanan içeriği al
    ob_end_clean(); // Tamponlamayı temizle ve kapat

    return $content;
}