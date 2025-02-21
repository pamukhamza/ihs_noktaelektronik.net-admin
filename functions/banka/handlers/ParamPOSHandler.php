<?php
require_once 'POSHandler.php';

class ParamPOSHandler extends POSHandler {
    public function processPayment() {
        try {
            $sonucStr = $_POST['TURKPOS_RETVAL_Sonuc_Str'];
            $dekont = $_POST['TURKPOS_RETVAL_Dekont_ID'];
            $tutar = str_replace(',', '.', $_POST['TURKPOS_RETVAL_Tahsilat_Tutari']);
            
            $inserted_id = $this->saveTransaction(1, $sonucStr, $tutar, 1);
            $this->handleSuccess($inserted_id);
            
            $this->redirect("https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b&cari_odeme=");
            return true;
        } catch (Exception $e) {
            error_log('Param POS Payment Error: ' . $e->getMessage());
            return false;
        }
    }
}