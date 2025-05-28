<?php
require_once 'POSHandler.php';

class YapiKrediPOSHandler extends POSHandler
{
    public function __construct($database, $paymentData)
    {
        parent::__construct($database, $paymentData);
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
            $inserted_id = $this->saveTransaction(
                4, // Yapı Kredi POS ID
                "Ödeme işlemi başarılı",
                $this->paymentData['yantoplam'],
                1
            );
            
            $this->handleSuccess($inserted_id);
            $this->redirect("https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b&cari_odeme=");
            return true;
        } else {
            preg_match('/<respCode>(.*?)<\/respCode>/', $response, $codeMatch);
            preg_match('/<respText>(.*?)<\/respText>/', $response, $textMatch);
            $respCode = isset($codeMatch[1]) ? $codeMatch[1] : '';
            $respText = isset($textMatch[1]) ? $textMatch[1] : '';

            $this->saveTransaction(
                4, // Yapı Kredi POS ID
                "Ödeme işlemi başarısız: " . $respText . ' Kod= ' . $respCode,
                $this->paymentData['yantoplam'],
                0
            );

            $this->redirect("https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b&code=" . 
                urlencode($respCode) . "&message=" . urlencode($respText));
            return false;
        }
    }
}