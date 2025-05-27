<?php
date_default_timezone_set("Europe/Istanbul");
if (isset($_GET["finanslastirma"]) && $_GET["finanslastirma"] === "Onaylandı") {

    // 1. Bankadan dönen verileri al
    $bankData   = $_POST['BankPacket'] ?? '';
    $xid        = $_POST['Xid'] ?? '';
    $amount     = $_POST['Amount'] ?? '';
    $currency   = 'TL'; // Sabit TL varsayıldı
    $merchantId = $_POST['MerchantId'] ?? '';
    include 'config.php';
    $terminalId = TERMINAL_ID;
    $encKey     = ENCKEY; 

    // 2. MAC oluştur (şifreleme sırası çok önemli!)
    function hashString($str) {
        return base64_encode(hash('sha256', $str, true));
    }

    $firstHash = hashString($encKey . ";" . $terminalId);
    $mac = hashString($xid . ";" . $amount . ";" . $currency . ";" . $merchantId . ";" . $firstHash);

    // 3. Finansallaştırma XML'i hazırla
    $xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-9\"?>
    <posnetRequest>
        <mid>{$merchantId}</mid>
        <tid>{$terminalId}</tid>
        <oosTranData>
            <bankData>{$bankData}</bankData>
            <wpAmount>0</wpAmount>
            <mac>{$mac}</mac>
        </oosTranData>
    </posnetRequest>";

    // 4. POST ile POSNET sistemine gönder
    $url = 'https://posnet.yapikredi.com.tr/PosnetWebService/XML';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'xmldata=' . urlencode($xml));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded; charset=utf-8'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    // 5. Cevabı kontrol et
    if (strpos($response, '<approved>1</approved>') !== false) {
        // Finansallaştırma başarılı
        header("Location: ../../../pages/b2b/b2b-sanalpos.php?yapikrediodeme=1");
        exit;
    } else {
        preg_match('/<respCode>(.*?)<\/respCode>/', $response, $codeMatch);
        preg_match('/<respText>(.*?)<\/respText>/', $response, $textMatch);
        $respCode = isset($codeMatch[1]) ? urlencode($codeMatch[1]) : '';
        $respText = isset($textMatch[1]) ? urlencode($textMatch[1]) : '';

        header("Location: ../../../pages/b2b/b2b-sanalpos.php?yapikrediodeme=0&respCode={$respCode}&respText={$respText}");
        exit;
    }
}

if(isset($_POST["adminCariOdeme"])){
    include 'config.php';

    $lang = $_POST['lang'];
    $uye_id = $_POST['uye_id'];
    $toplam = $_POST['toplam'];
    $amountInput = str_replace(',', '.', $_POST['toplam']); // 12,34 -> 12.34
    $amount = (int) round(floatval($amountInput) * 100); // kuruş cinsinden
    $hesap = $_POST['hesap'];
    $installment = $_POST['installment'];
    $cardHolderName = $_POST['cardHolderName'];
    $ccno = $_POST['ccno'];
    $expMonth = $_POST['expMonth'];
    $expYear = $_POST['expYear'];
    $expDate = $expYear . $expMonth ;
    $cvc = $_POST['cvc'];
    $tip = $_POST['tip'];
    $vade = $_POST['vade'];
    $banka_id = $_POST['banka_id'];

    $orderID = substr(md5(uniqid()), 0, 20); // 24 karaktere kadar benzersiz ID
    $currencyCode = 'TL';
    
    $verimiz = [
        "cardHolder" => $cardHolderName,
        "cardNo" => $ccno,
        "yantoplam" => $toplam,
        "banka_id" => $banka_id,
        "hesap" => $hesap,
        "taksit" => $installment,
        "uye_id" => $uye_id,
        "lang" => $lang,
    ];
    $verimizB64 = base64_encode(json_encode($verimiz));


    // XML hazırlığı
    $xml = <<<XML
    <?xml version="1.0" encoding="ISO-8859-9"?>
    <posnetRequest>
        <mid>{MERCHANT_ID}</mid>
        <tid>{TERMINAL_ID}</tid>
        <oosRequestData>
            <posnetid>{POSNET_ID}</posnetid>
            <XID>{$orderID}</XID>
            <amount>{$amount}</amount>
            <currencyCode>{$currencyCode}</currencyCode>
            <installment>{$installment}</installment>
            <tranType>Sale</tranType>
            <cardHolderName>{$cardHolderName}</cardHolderName>
            <ccno>{$ccno}</ccno>
            <expDate>{$expDate}</expDate>
            <cvc>{$cvc}</cvc>
        </oosRequestData>
    </posnetRequest>
    XML;
    
    $xml = str_replace(
        ['{MERCHANT_ID}', '{TERMINAL_ID}', '{POSNET_ID}'], 
        [MERCHANT_ID, TERMINAL_ID, POSNET_ID], 
        $xml
    );
    
    // Bankaya POST gönder
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, POSNET_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'xmldata=' . urlencode($xml));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $merchant_id = MERCHANT_ID;
    $terminal_id = TERMINAL_ID;
    $posnet_id   = POSNET_ID;
    $headers = [
        'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
        'X-MERCHANT-ID: ' . MERCHANT_ID,
        'X-TERMINAL-ID: ' . TERMINAL_ID,
        'X-POSNET-ID: ' . POSNET_ID,
        'X-CORRELATION-ID: ' . $orderID
    ];
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    /*
    // Header'ları göster
    echo "<h3>Gönderilen Headers:</h3>";
    echo "<pre>";
    print_r($headers);
    echo "</pre>";
    
    // URL'yi göster
    echo "<h3>Gönderilen URL:</h3>";
    echo "<pre>" . POSNET_URL . "</pre>";
    */
    $response = curl_exec($ch);
    
    // Hata ayıklama bilgileri
    //echo "<h3>Gönderilen XML:</h3>";
    //echo "<pre>" . htmlspecialchars($xml) . "</pre>";
    
    //echo "<h3>CURL Bilgileri:</h3>";
    //echo "<pre>";
    //print_r(curl_getinfo($ch));
    //echo "</pre>";
    
    
    if (curl_errno($ch)) {
        echo "<h3>CURL Hatası:</h3>";
        echo curl_error($ch);
    }
    curl_close($ch);
    
    // Yanıtı işle
    if ($response) {
        //echo "<h3>Bankadan Gelen Yanıt:</h3>";
        //echo "<pre>" . htmlspecialchars($response) . "</pre>";
        
        $xmlResponse = simplexml_load_string($response);
        //echo "<h3>İşlenmiş Yanıt:</h3>";
        //echo "<pre>"; print_r($xmlResponse); echo "</pre>";
    
        
        if ((string)$xmlResponse->approved === '1') {
                $data1 = (string)$xmlResponse->oosRequestDataResponse->data1;
                $data2 = (string)$xmlResponse->oosRequestDataResponse->data2;
                $sign  = (string)$xmlResponse->oosRequestDataResponse->sign;
            
                $redirectForm = <<<HTML
                <!DOCTYPE html>
                <html lang="tr">
                <head>
                    <meta charset="utf-8" />
                    <title>Yapı Kredi Ödeme Yönlendirme</title>
                    <script type="text/javascript" src="https://posnet.yapikredi.com.tr/3DSWebService/scriptler/posnet.js"></script>
                    <script type="text/javascript">
                        function submitFormEx(Form, OpenNewWindowFlag, WindowName) {
                            submitForm(Form, OpenNewWindowFlag, WindowName);
                            Form.submit();
                        }
            
                        window.onload = function() {
                            var form = document.forms['formName'];
                            submitFormEx(form, 0, 'YKBWindow');
                        };
                    </script>
                </head>
                <body>
                    <form name="formName" method="post" action="https://posnet.yapikredi.com.tr/3DSWebService/YKBPaymentService" target="_self"> 
                        <input name="mid" type="hidden" value="{$merchant_id}" />
                        <input name="posnetID" type="hidden" value="{$posnet_id}" /> 
                        <input name="posnetData" type="hidden" value="{$data1}" />
                        <input name="posnetData2" type="hidden" value="{$data2}" />
                        <input name="digest" type="hidden" value="{$sign}" />
                        <input name="vftCode" type="hidden" value="" />
                        <input name="merchantSessionId" type="hidden" value="{$orderID}" />
                        <input name="merchantReturnURL" type="hidden" value="https://www.noktaelektronik.net/admin/functions/banka/yapikredi_test/payment_request.php?finanslastirma=Onaylandı" />
                        <input name="url" type="hidden" value="https://www.noktaelektronik.net/admin/functions/banka/manuelodeme?cariveriYapiKredi={$verimizB64}" />
                        <input name="lang" type="hidden" value="tr" />
                        <input name="openANewWindow" type="hidden" value="0" />
                        <noscript>
                            <input type="submit" value="Devam Et (Tarayıcınız otomatik yönlendirmeyi desteklemiyor)" />
                        </noscript>
                    </form>
                </body>
                </html>
                HTML;
            
                echo $redirectForm;
                exit;
        } else {
            echo "<div style='color: red; font-weight: bold;'>❌ Hata Oluştu:</div>";

            // xmlResponse içeriğini okunabilir şekilde yazdır
            echo "<pre>";
            print_r($xmlResponse);
            echo "</pre>";
        
            // Daha önceki hata yazdırma işlemi de kalsın
            if (!empty($xmlResponse->errors) && is_array($xmlResponse->errors)) {
                echo "<ul>";
                foreach ($xmlResponse->errors as $error) {
                    echo "<li>" . htmlspecialchars($error) . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>" . htmlspecialchars($xmlResponse->respText) . "</p>";
            }
        
            // Formu gizli şekilde hazırla ama otomatik gönderme
            echo "<form id='redirectForm' method='POST' action='https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b&hata='>
                    <input type='hidden' name='error_message' value='" . htmlspecialchars($xmlResponse->respText) . "'>
                    <button type='submit'>Formu Gönder</button> <!-- Kullanıcı manuel gönderir -->
                  </form>";
        }
    } else {
        $errorMessage = "❌ Bankaya bağlanılamadı.";
        echo "<form id='redirectForm' method='POST' action='https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b'>
                <input type='hidden' name='error_message' value='" . htmlspecialchars($successMessage) . "'>
                </form>
                <script>
                document.getElementById('redirectForm').submit();
                </script>";
    }
}