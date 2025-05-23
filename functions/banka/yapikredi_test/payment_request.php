<?php
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
date_default_timezone_set("Europe/Istanbul");

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

    $orderID = substr(md5(uniqid()), 0, 24); // 24 karaktere kadar benzersiz ID
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
    /*
    // Hata ayıklama bilgileri
    echo "<h3>Gönderilen XML:</h3>";
    echo "<pre>" . htmlspecialchars($xml) . "</pre>";
    
    echo "<h3>CURL Bilgileri:</h3>";
    echo "<pre>";
    print_r(curl_getinfo($ch));
    echo "</pre>";
    
    */
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
                <html lang="en" xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta charset="utf-8" />
                        <title></title>
                        <script type="text/javascript" src="https://posnet.yapikredi.com.tr/3DSWebService/scriptler/posnet.js"></script>
                        <script type="text/javascript">
                            function submitFormEx(Form, OpenNewWindowFlag, WindowName) {
                                submitForm(Form, OpenNewWindowFlag, WindowName)
                                Form.submit();
                            }
                    </script>
                    </head>
                <body>
            <form name="formName" method="post" action="https://setmpos.ykb.com/3DSWebService/YKBPaymentService" target="_self"> 
                <input name="mid" type="hidden" id="mid" value="{$merchant_id}" />
                <input name="posnetID" type="hidden" id="PosnetID" value="{$posnet_id}" /> 
                <input type="" name="posnetData" value="{$data1}" />
                <input type="" name="posnetData2" value="{$data2}" />
                <input type="" name="digest" value="{$sign}" />
                <input type="" name="vftCode" value="" />
                <input type="" name="merchantSessionId" value="{$orderID}" />
                <input name="merchantReturnURL" type="hidden" id=" merchantReturnURL" value="https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b" />
                <input type="" name="url" value="https://www.noktaelektronik.net/admin/functions/banka/manuelodeme?cariveriYapiKredi={$verimizB64}" />
                <input type="" name="lang" value="tr" />
                <input name="openANewWindow" type="" id="openANewWindow" value="0" />
                <input type="submit" name="Submit" value="Doğrulama Yap" onclick="submitFormEx(formName, 0, 'YKBWindow')" /> 
            </form>
            HTML;

            echo $redirectForm;
            exit;
        } else {
            $errorMessage = "❌ Hata: {$xmlResponse->respText}";
            echo "<form id='redirectForm' method='POST' action='https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b'>
                    <input type='hidden' name='error_message' value='" . htmlspecialchars($successMessage) . "'>
                  </form>
                  <script>
                    document.getElementById('redirectForm').submit();
                  </script>";
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