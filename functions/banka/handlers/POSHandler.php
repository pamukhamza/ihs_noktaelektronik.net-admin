<?php
abstract class POSHandler {
    protected $database;
    protected $paymentData;
    
    public function __construct($database, $paymentData) {
        $this->database = $database;
        $this->paymentData = $paymentData;
    }

    abstract public function processPayment();
    
    protected function saveTransaction($pos_id, $sonucStr, $tutar, $basarili, $transid = null, $siparis_no = null) {
        $query = "INSERT INTO b2b_sanal_pos_odemeler (uye_id, pos_id, islem, islem_turu, tutar, basarili, transid, siparis_no) 
                 VALUES (:uye_id, :pos_id, :islem, :islem_turu, :tutar, :basarili, :transid, :siparis_no)";
        
        $params = [
            'uye_id' => $this->paymentData['uye_id'],
            'pos_id' => $pos_id,
            'islem' => $sonucStr,
            'islem_turu' => 'cari',
            'tutar' => $tutar,
            'basarili' => $basarili,
            'transid' => $transid,
            'siparis_no' => $siparis_no
        ];
        
        $this->database->insert($query, $params);
        return $this->database->lastInsertId();
    }

    protected function handleSuccess($inserted_id) {
        dekontOlustur(
            $this->paymentData['uye_id'],
            $inserted_id,
            $this->paymentData['firmaUnvani'],
            $this->paymentData['maskedCardNo'],
            $this->paymentData['cardHolder'],
            $this->paymentData['taksit_sayisi'],
            $this->paymentData['yantoplam'],
            $this->paymentData['degistirme_tarihi']
        );

        posXmlOlustur(
            $this->paymentData['uyecarikod'],
            $this->paymentData['hesap'],
            $this->paymentData['degistirme_tarihi'],
            $this->paymentData['currentDateTime'],
            $this->paymentData['yantoplam'],
            '',
            $this->paymentData['dov_al'],
            $this->paymentData['dov_sat'],
            $this->paymentData['siparisNumarasi'],
            $this->paymentData['blbnhskodu'],
            $this->paymentData['banka_adi'],
            $this->paymentData['taksit_sayisi'],
            $this->paymentData['doviz'],
            $this->paymentData['banka_tanimi']
        );

        $mail_icerik = cariOdeme(
            $this->paymentData['firmaUnvani'],
            $this->paymentData['yantoplam'],
            $this->paymentData['taksit_sayisi']
        );
        
        mailGonder($this->paymentData['uye_mail'], 'Cari Ödeme Bildirimi', $mail_icerik, 'Nokta Elektronik');
    }

    protected function redirect($url) {
        header("Location: " . $url);
        exit();
    }
}