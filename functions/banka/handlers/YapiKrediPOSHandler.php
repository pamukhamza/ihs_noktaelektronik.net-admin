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
        // 3D'den dönen POST verileri
        $bankData    = $_POST['BankPacket'] ?? '';
        $merchantData = $_POST['MerchantPacket'] ?? '';
        $sign        = $_POST['Sign'] ?? '';

        if (!$bankData || !$merchantData || !$sign) {
            echo "❌ Hatalı 3D dönüş verisi.";
            return;
        }

        // POSNET bilgileri (config'ten gelmeli)
        include_once 'yapikredi_test/config.php';

        $xml = <<<XML
            <?xml version="1.0" encoding="ISO-8859-9"?>
            <posnetRequest>
                <mid>{MERCHANT_ID}</mid>
                <tid>{TERMINAL_ID}</tid>
                <oosResolveMerchantData>
                    <bankData>{$bankData}</bankData>
                    <merchantData>{$merchantData}</merchantData>
                    <sign>{$sign}</sign>
                </oosResolveMerchantData>
            </posnetRequest>
        XML;

        $xml = str_replace(
            ['{MERCHANT_ID}', '{TERMINAL_ID}'],
            [MERCHANT_ID, TERMINAL_ID],
            $xml
        );

        // cURL ile isteği gönder
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, POSNET_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'xmldata=' . urlencode($xml));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo "CURL Hatası: " . curl_error($ch);
            curl_close($ch);
            return;
        }

        curl_close($ch);

        // Yanıtı işle
        $xmlResponse = simplexml_load_string($response);

        if ((string)$xmlResponse->approved === '1') {
            // Başarılı ödeme → kayıt/işlem yapılabilir
            echo "<h2>✅ Ödeme başarılı!</h2>";
            echo "<p>HostLogKey: " . $xmlResponse->hostlogkey . "</p>";
            
            // Burada veri kaydı yapılabilir (örn. dekont, mail, fatura vb.)
            // örn: $this->db->insert(...) veya dekont oluştur vs.
        } else {
            echo "<h2>❌ Ödeme başarısız.</h2>";
            echo "<p>Hata: " . $xmlResponse->respText . "</p>";
        }
    }
}
