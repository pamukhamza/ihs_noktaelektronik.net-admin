<?php
require_once 'POSHandler.php';

class GarantiPOSHandler extends POSHandler
{
    public function __construct($database, $paymentData)
    {
        parent::__construct($database, $paymentData);
    }

    public function processPayment()
    {
        try {
            // Garanti 3D Secure response parametrelerini al
            $mdStatus = $_POST['mdStatus'] ?? '';
            $mdErrorMessage = $_POST['mdErrorMessage'] ?? '';
            $xid = $_POST['xid'] ?? '';
            $eci = $_POST['eci'] ?? '';
            $cavv = $_POST['cavv'] ?? '';
            $amount = $_POST['amount'] ?? '';
            $currency = $_POST['currency'] ?? '';
            $orderId = $_POST['orderid'] ?? '';
            $response = $_POST['Response'] ?? '';
            $procreturncode = $_POST['procreturncode'] ?? '';
            $transid = $_POST['transid'] ?? '';
            $hostrefnum = $_POST['hostrefnum'] ?? '';
            $authcode = $_POST['authcode'] ?? '';
            $rnd = $_POST['rnd'] ?? '';
            $hashparams = $_POST['hashparams'] ?? '';
            $hashparamsval = $_POST['hashparamsval'] ?? '';
            $hash = $_POST['hash'] ?? '';

            // Hash doğrulaması yap
            if (!$this->verifyHash($hashparams, $hashparamsval, $hash)) {
                $this->saveTransaction(
                    106, // Garanti POS ID
                    "Hash doğrulama hatası",
                    $this->paymentData['yantoplam'],
                    0
                );
                $this->redirect("https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b&garantiodeme=0&error=hash");
                return false;
            }

            // 3D Secure doğrulama sonucunu kontrol et
            if ($mdStatus == '1' || $mdStatus == '2' || $mdStatus == '3' || $mdStatus == '4') {
                // 3D Secure doğrulama başarılı, ödeme işlemini tamamla
                if ($response == 'Approved') {
                    // Ödeme başarılı
                    $inserted_id = $this->saveTransaction(
                        106, // Garanti POS ID
                        "Ödeme işlemi başarılı - AuthCode: " . $authcode,
                        $this->paymentData['yantoplam'],
                        1,
                        $transid,
                        $orderId
                    );
                    
                    $this->handleSuccess($inserted_id);
                    $this->redirect("https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b&garantiodeme=1");
                    return true;
                } else {
                    // Ödeme başarısız
                    $errorMessage = "Ödeme işlemi başarısız - Response: " . $response;
                    if ($procreturncode) {
                        $errorMessage .= " - ProcReturnCode: " . $procreturncode;
                    }
                    
                    $this->saveTransaction(
                        106, // Garanti POS ID
                        $errorMessage,
                        $this->paymentData['yantoplam'],
                        0,
                        $transid,
                        $orderId
                    );
                    
                    $this->redirect("https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b&garantiodeme=0&error=" . 
                        urlencode($errorMessage));
                    return false;
                }
            } else {
                // 3D Secure doğrulama başarısız
                $errorMessage = "3D Secure doğrulama başarısız - MDStatus: " . $mdStatus;
                if ($mdErrorMessage) {
                    $errorMessage .= " - " . $mdErrorMessage;
                }
                
                $this->saveTransaction(
                    106, // Garanti POS ID
                    $errorMessage,
                    $this->paymentData['yantoplam'],
                    0,
                    $transid,
                    $orderId
                );
                
                $this->redirect("https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b&garantiodeme=0&error=" . 
                    urlencode($errorMessage));
                return false;
            }
        } catch (Exception $e) {
            error_log('Garanti POS Payment Error: ' . $e->getMessage());
            $this->saveTransaction(
                106, // Garanti POS ID
                "Sistem hatası: " . $e->getMessage(),
                $this->paymentData['yantoplam'],
                0
            );
            $this->redirect("https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b&garantiodeme=0&error=system");
            return false;
        }
    }

    /**
     * Hash doğrulaması yapar
     */
    private function verifyHash($hashparams, $hashparamsval, $hash)
    {
        try {
            // Garanti ayarlarını yükle
            require_once(__DIR__ . '/../garanti/core/settings/PosSettings.php');
            require_once(__DIR__ . '/../garanti/core/enums/RequestMode.php');
            
            $settings = new PosSettings(RequestMode::Prod);
            
            // Hash parametrelerini ayır
            $params = explode(':', $hashparams);
            $values = explode(':', $hashparamsval);
            
            // Hash string oluştur
            $hashString = '';
            for ($i = 0; $i < count($params); $i++) {
                if (isset($values[$i])) {
                    $hashString .= $values[$i];
                }
            }
            
            // Store key ile hash oluştur
            $calculatedHash = base64_encode(sha1($hashString . $settings->storeKey, true));
            
            return $calculatedHash === $hash;
        } catch (Exception $e) {
            error_log('Hash verification error: ' . $e->getMessage());
            return false;
        }
    }
}
