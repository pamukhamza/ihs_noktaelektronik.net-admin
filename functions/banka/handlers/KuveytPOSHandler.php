<?php
require_once 'POSHandler.php';

class KuveytPOSHandler extends POSHandler {
    private $apiUrl = 'https://sanalpos.kuveytturk.com.tr/ServiceGateWay/Home/ThreeDModelProvisionGate';
    private $customerId = "93981545";
    private $merchantId = "61899";
    private $username = "kadirbabur";
    private $password = "Dell28736.!";
    
    public function processPayment() {
        try {
            $authResponse = $_POST["AuthenticationResponse"];
            $requestContent = urldecode($authResponse);
            $authXml = simplexml_load_string($requestContent);
            
            if ($authXml->ResponseCode != "00" || $authXml->ResponseMessage != "Kart doğrulandı.") {
                return false;
            }

            // Prepare payment data
            $merchantOrderId = $authXml->VPosMessage->MerchantOrderId;
            $amount = $authXml->VPosMessage->Amount;
            $md = $authXml->MD;
            
            // Create payment request
            $response = $this->sendPaymentRequest($merchantOrderId, $amount, $md);
            
            if ($response->ResponseCode == "00") {
                return $this->handleSuccessfulPayment($response);
            } else {
                return $this->handleFailedPayment($response);
            }
            
        } catch (Exception $e) {
            error_log('Kuveyt POS Payment Error: ' . $e->getMessage());
            return false;
        }
    }

    private function sendPaymentRequest($merchantOrderId, $amount, $md) {
        $hashedPassword = base64_encode(sha1($this->password, "ISO-8859-9"));
        $hashData = base64_encode(sha1($this->merchantId . $merchantOrderId . $amount . $this->username . $hashedPassword, "ISO-8859-9"));
        
        $xml = $this->preparePaymentXml($hashData, $merchantOrderId, $amount, $md);
        
        return $this->sendRequest($xml);
    }

    private function preparePaymentXml($hashData, $merchantOrderId, $amount, $md) {
        return '<KuveytTurkVPosMessage xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
            <APIVersion>TDV2.0.0</APIVersion>
            <HashData>' . $hashData . '</HashData>
            <MerchantId>' . $this->merchantId . '</MerchantId>
            <CustomerId>' . $this->customerId . '</CustomerId>
            <UserName>' . $this->username . '</UserName>
            <TransactionType>Sale</TransactionType>
            <InstallmentCount>' . $this->paymentData['taksit_sayisi'] . '</InstallmentCount>
            <CurrencyCode>0949</CurrencyCode>
            <Amount>' . $amount . '</Amount>
            <MerchantOrderId>' . $merchantOrderId . '</MerchantOrderId>
            <TransactionSecurity>3</TransactionSecurity>
            <KuveytTurkVPosAdditionalData>
                <AdditionalData>
                    <Key>MD</Key>
                    <Data>' . $md . '</Data>
                </AdditionalData>
            </KuveytTurkVPosAdditionalData>
        </KuveytTurkVPosMessage>';
    }

    private function sendRequest($xml) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/xml',
            'Content-length: ' . strlen($xml)
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        if (!$response) {
            throw new Exception('Failed to get response from Kuveyt payment gateway');
        }
        
        return simplexml_load_string($response);
    }

    private function handleSuccessfulPayment($response) {
        $inserted_id = $this->saveTransaction(
            3, // Kuveyt POS ID
            "Ödeme işlemi başarılı: " . $response->ResponseMessage . ' Kod= ' . $response->ResponseCode,
            $this->paymentData['yantoplam'],
            1
        );
        
        $this->handleSuccess($inserted_id);
        $this->redirect("https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b&cari_odeme=");
        return true;
    }

    private function handleFailedPayment($response) {
        $this->saveTransaction(
            3, // Kuveyt POS ID
            "Ödeme işlemi başarısız: " . $response->ResponseMessage . ' Kod= ' . $response->ResponseCode,
            $this->paymentData['yantoplam'],
            0
        );
        
        $this->redirect("https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b&code=" . 
            $response->ResponseCode . "&message=" . $response->ResponseMessage);
        return false;
    }
}