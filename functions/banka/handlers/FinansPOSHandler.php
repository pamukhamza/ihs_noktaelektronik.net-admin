<?php
require_once 'POSHandler.php';

class FinansPOSHandler extends POSHandler {
    private $apiUrl = 'https://sanalpos.turkiyefinans.com.tr/fim/api';
    private $username = 'noktaadmin';
    private $password = 'NEBsis28736.!';

    public function processPayment() {
        if ($_POST["mdStatus"] != "1") {
            return false;
        }

        try {
            $xml = $this->prepareXML();
            $response = $this->sendRequest($xml);
            
            if ($response->ProcReturnCode == "00") {
                return $this->handleSuccessfulPayment($response);
            } else {
                return $this->handleFailedPayment($response);
            }
        } catch (Exception $e) {
            error_log('Finans POS Payment Error: ' . $e->getMessage());
            return false;
        }
    }

    private function prepareXML() {
        $baseXml = '<?xml version="1.0" encoding="UTF-8"?>
        <CC5Request>
            <Name>' . $this->username . '</Name>
            <Password>' . $this->password . '</Password>
            <ClientId>' . $_POST['clientid'] . '</ClientId>
            <OrderId>' . $_POST['oid'] . '</OrderId>
            <Type>Auth</Type>
            <Number>' . $_POST['md'] . '</Number>
            <Total>' . $_POST['amount'] . '</Total>
            <Currency>949</Currency>
            <PayerTxnId>' . $_POST['xid'] . '</PayerTxnId>
            <PayerSecurityLevel>' . $_POST['eci'] . '</PayerSecurityLevel>
            <PayerAuthenticationCode>' . $_POST['cavv'] . '</PayerAuthenticationCode>';

        if ($this->paymentData['taksit_sayisi'] > 1) {
            $baseXml .= '
            <Mode>P</Mode>
            <Taksit>' . $this->paymentData['taksit_sayisi'] . '</Taksit>';
        }

        $baseXml .= '</CC5Request>';
    
        return $baseXml;
    }

    private function sendRequest($xml) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml', 'Content-length: ' . strlen($xml)));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        if (!$response) {
            throw new Exception('Failed to get response from payment gateway');
        }
        
        return simplexml_load_string($response);
    }

    private function handleSuccessfulPayment($response) {
        $inserted_id = $this->saveTransaction(
            4,
            "Ödeme işlemi başarılı: " . $response->Response . ' Kod= ' . $response->ProcReturnCode,
            $this->paymentData['yantoplam'],
            1,
            $response->TransId,
            $response->ReturnOid
        );
        
        $this->handleSuccess($inserted_id);
        $this->redirect("https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b&cari_odeme=");
        return true;
    }

    private function handleFailedPayment($response) {
        $this->saveTransaction(
            4,
            "Ödeme işlemi başarısız: " . $response->ErrMsg . ' Kod= ' . $response->ProcReturnCode,
            $this->paymentData['yantoplam'],
            0
        );
        
        $this->redirect("https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b&code=".$response->ProcReturnCode."&message=".$response->ErrMsg);
        return false;
    }
}