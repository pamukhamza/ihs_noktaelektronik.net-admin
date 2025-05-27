<?php

class YapiKrediPOSHandler
{
    private $db;
    private $paymentData;

    public function __construct($db, $paymentData)
    {
        $this->db = $db;
        $this->paymentData = $paymentData;
    }

    public function processPayment()
    {
        $bankData   = $_POST['BankPacket'] ?? '';
        $xid        = $_POST['Xid'] ?? '';
        $amount     = $_POST['Amount'] ?? '';
        $currency   = 'TL'; // Sabit TL varsayıldı
        $merchantId = $_POST['MerchantId'] ?? '';
        include_once 'yapikredi_test/config.php';
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
            header("Location: ../../pages/b2b/b2b-sanalpos.php?yapikrediodeme=1");
            exit;
        } else {
            preg_match('/<respCode>(.*?)<\/respCode>/', $response, $codeMatch);
            preg_match('/<respText>(.*?)<\/respText>/', $response, $textMatch);
            $respCode = isset($codeMatch[1]) ? urlencode($codeMatch[1]) : '';
            $respText = isset($textMatch[1]) ? urlencode($textMatch[1]) : '';

            header("Location: ../../pages/b2b/b2b-sanalpos.php?yapikrediodeme=0&respCode={$respCode}&respText={$respText}");
            exit;
        }

    }
}