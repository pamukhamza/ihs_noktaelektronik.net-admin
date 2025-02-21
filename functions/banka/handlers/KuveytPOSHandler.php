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
            if (!isset($_POST["AuthenticationResponse"])) {
                throw new Exception('Authentication response not found');
            }

            $authResponse = trim($_POST["AuthenticationResponse"]);
            if (empty($authResponse)) {
                throw new Exception('Empty authentication response');
            }

            $requestContent = urldecode($authResponse);
            
            // Validate XML string before loading
            if (!$this->isValidXML($requestContent)) {
                throw new Exception('Invalid XML in authentication response');
            }

            $authXml = simplexml_load_string($requestContent);
            if ($authXml === false) {
                throw new Exception('Failed to parse authentication XML');
            }
            
            if ($authXml->ResponseCode != "00" || $authXml->ResponseMessage != "Kart doğrulandı.") {
                error_log('Authentication failed: ' . $authXml->ResponseMessage);
                return false;
            }

            // Prepare payment data
            $merchantOrderId = (string)$authXml->VPosMessage->MerchantOrderId;
            $amount = (string)$authXml->VPosMessage->Amount;
            $md = (string)$authXml->MD;
            
            // Create payment request
            $response = $this->sendPaymentRequest($merchantOrderId, $amount, $md);
            
            if ($response && $response->ResponseCode == "00") {
                return $this->handleSuccessfulPayment($response);
            } else {
                return $this->handleFailedPayment($response);
            }
            
        } catch (Exception $e) {
            error_log('Kuveyt POS Payment Error: ' . $e->getMessage());
            return false;
        }
    }

    private function isValidXML($xml) {
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($xml);
        $errors = libxml_get_errors();
        libxml_clear_errors();
        return $doc !== false && empty($errors);
    }

    private function sendPaymentRequest($merchantOrderId, $amount, $md) {
        try {
            $hashedPassword = base64_encode(sha1($this->password, "ISO-8859-9"));
            $hashData = base64_encode(sha1($this->merchantId . $merchantOrderId . $amount . $this->username . $hashedPassword, "ISO-8859-9"));
            
            $xml = $this->preparePaymentXml($hashData, $merchantOrderId, $amount, $md);
            
            return $this->sendRequest($xml);
        } catch (Exception $e) {
            error_log('Payment request error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function preparePaymentXml($hashData, $merchantOrderId, $amount, $md) {
        // Create XML using DOMDocument for proper encoding
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;
        
        // Create root element
        $root = $doc->createElement('KuveytTurkVPosMessage');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
        $doc->appendChild($root);
        
        // Add child elements
        $elements = [
            'APIVersion' => 'TDV2.0.0',
            'HashData' => $hashData,
            'MerchantId' => $this->merchantId,
            'CustomerId' => $this->customerId,
            'UserName' => $this->username,
            'TransactionType' => 'Sale',
            'InstallmentCount' => $this->paymentData['taksit_sayisi'],
            'CurrencyCode' => '0949',
            'Amount' => $amount,
            'MerchantOrderId' => $merchantOrderId,
            'TransactionSecurity' => '3'
        ];
        
        foreach ($elements as $name => $value) {
            $element = $doc->createElement($name);
            $element->appendChild($doc->createTextNode($value));
            $root->appendChild($element);
        }
        
        // Add KuveytTurkVPosAdditionalData
        $additionalData = $doc->createElement('KuveytTurkVPosAdditionalData');
        $adData = $doc->createElement('AdditionalData');
        $key = $doc->createElement('Key', 'MD');
        $data = $doc->createElement('Data', $md);
        
        $adData->appendChild($key);
        $adData->appendChild($data);
        $additionalData->appendChild($adData);
        $root->appendChild($additionalData);
        
        return $doc->saveXML();
    }

    private function sendRequest($xml) {
        $ch = curl_init();
        
        // Set CURL options
        $curlOptions = [
            CURLOPT_SSLVERSION => 6,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Content-type: application/xml',
                'Content-length: ' . strlen($xml)
            ],
            CURLOPT_POST => true,
            CURLOPT_HEADER => false,
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_POSTFIELDS => $xml,
            CURLOPT_RETURNTRANSFER => true
        ];
        
        curl_setopt_array($ch, $curlOptions);
        
        $response = curl_exec($ch);
        
        if ($error = curl_error($ch)) {
            curl_close($ch);
            throw new Exception('CURL Error: ' . $error);
        }
        
        curl_close($ch);
        
        if (!$response) {
            throw new Exception('Empty response from Kuveyt payment gateway');
        }
        
        // Validate response XML
        if (!$this->isValidXML($response)) {
            throw new Exception('Invalid XML response from payment gateway');
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
        $message = $response ? $response->ResponseMessage : 'Unknown error';
        $code = $response ? $response->ResponseCode : 'UNKNOWN';
        
        $this->saveTransaction(
            3, // Kuveyt POS ID
            "Ödeme işlemi başarısız: " . $message . ' Kod= ' . $code,
            $this->paymentData['yantoplam'],
            0
        );
        
        $this->redirect("https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b&code=" . 
            urlencode($code) . "&message=" . urlencode($message));
        return false;
    }
}