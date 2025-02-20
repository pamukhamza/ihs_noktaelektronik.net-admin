<?php
require_once "../db.php"; 
require_once '../wolvox/cari_duzenle.php';
$database = new Database();

if (isset($_POST['uye_guncelle'])) {
    $id = $_POST["uye_id"];

    $uye_kayit_tipi = $_POST['uyeKayitTipi'];
    $uye_email = $_POST['uye_email'];
    $uye_parola = $_POST['uye_parola'];
    $uye_adi = $_POST['uye_adi'];
    $uye_soyadi = $_POST['uye_soyadi'];
    $uye_kimlik_no = isset($_POST['uye_kimlik_no']) ? $_POST['uye_kimlik_no'] : '';
    $uye_firma_adi = $_POST['uye_firma_adi'];
    $uye_vergi_dairesi = $_POST['uye_vergi_dairesi'];
    $uye_vergi_no = isset($_POST['uye_vergi_no']) ? $_POST['uye_vergi_no'] : '';
    $uye_durum = $_POST['uye_durum'];
    $uye_aktivasyon = $_POST['uye_aktivasyon'];
    $uye_fiyat_no = $_POST['uye_fiyat_no'];
    $uye_satis_temsilcisi = $_POST['uye_satis_temsilcisi'];
    $il = $_POST['il'];
    $ilce = $_POST['ilce'];
    $mahalle = $_POST['mahalle'];
    $uye_adres = $_POST['uye_adres'];
    $uye_posta_kodu = $_POST['uye_posta_kodu'];
    $uye_cep_tel = $_POST['uye_cep_tel'];
    $uye_sabit_tel = $_POST['uye_sabit_tel'];
    $muhasebe_kodu = $_POST['muhasebe_kodu'];
    $MUHKODU_ALIS = $_POST['muhasebe_alis_kodu'];
    $MUHKODU_SATIS = $_POST['muhasebe_satis_kodu'];
    $currentDateTime = date("Y-m-d H:i:s");
    $DEGISTIRME_TARIHI = date("Y-m-d H:i:s", strtotime($currentDateTime . " +3 hours")); // Add 3 hours for the timestamp

    if ($uye_parola == '' || $uye_parola == NULL) {
        $sql = "UPDATE uyeler SET uye_tipi = :uye_tipi, email = :email, ad = :ad, soyad = :soyad, tc_no = :tc_no, firmaUnvani = :firmaUnvani,
            vergi_dairesi = :vergi_dairesi, vergi_no = :vergi_no, aktif = :aktif, aktivasyon = :aktivasyon, fiyat = :fiyat, satis_temsilcisi = :satis_temsilcisi,
            il = :il, ilce = :ilce, mahalle = :mahalle, adres = :adres, posta_kodu = :posta_kodu, tel = :tel, sabit_tel = :sabit_tel, muhasebe_kodu = :muhasebe_kodu,
            DEGISTIRME_TARIHI = :DEGISTIRME_TARIHI, MUHKODU_ALIS = :MUHKODU_ALIS, MUHKODU_SATIS = :MUHKODU_SATIS WHERE id = :id";
            
        $param = [
            'uye_tipi' => $uye_kayit_tipi, 'email' => $uye_email, 'ad' => $uye_adi, 'soyad' => $uye_soyadi, 'tc_no' => $uye_kimlik_no, 
            'firmaUnvani' => $uye_firma_adi, 'vergi_dairesi' => $uye_vergi_dairesi, 'vergi_no' => $uye_vergi_no, 'aktif' => $uye_durum, 
            'aktivasyon' => $uye_aktivasyon, 'fiyat' => $uye_fiyat_no, 'satis_temsilcisi' => $uye_satis_temsilcisi, 'il' => $il, 
            'ilce' => $ilce, 'mahalle' => $mahalle, 'adres' => $uye_adres, 'posta_kodu' => $uye_posta_kodu, 'tel' => $uye_cep_tel, 
            'sabit_tel' => $uye_sabit_tel, 'muhasebe_kodu' => $muhasebe_kodu, 'DEGISTIRME_TARIHI' => $DEGISTIRME_TARIHI, 'MUHKODU_ALIS' => $MUHKODU_ALIS,
            'MUHKODU_SATIS' => $MUHKODU_SATIS, 'id' => $id
        ];
    } else {
        $uye_parola = md5($uye_parola); // Parola şifreleme
        $sql = "UPDATE uyeler SET uye_tipi = :uye_tipi, email = :email, parola = :parola, ad = :ad, soyad = :soyad, tc_no = :tc_no, firmaUnvani = :firmaUnvani, 
            vergi_dairesi = :vergi_dairesi, vergi_no = :vergi_no, aktif = :aktif, aktivasyon = :aktivasyon, fiyat = :fiyat, satis_temsilcisi = :satis_temsilcisi,
            il = :il, ilce = :ilce, mahalle = :mahalle, adres = :adres, posta_kodu = :posta_kodu, tel = :tel, sabit_tel = :sabit_tel, muhasebe_kodu = :muhasebe_kodu,
            DEGISTIRME_TARIHI = :DEGISTIRME_TARIHI, MUHKODU_ALIS = :MUHKODU_ALIS, MUHKODU_SATIS = :MUHKODU_SATIS WHERE id = :id ";
    
        $param = [
            'uye_tipi' => $uye_kayit_tipi, 'email' => $uye_email, 'parola' => $uye_parola, 'ad' => $uye_adi, 'soyad' => $uye_soyadi, 
            'tc_no' => $uye_kimlik_no, 'firmaUnvani' => $uye_firma_adi, 'vergi_dairesi' => $uye_vergi_dairesi, 'vergi_no' => $uye_vergi_no, 
            'aktif' => $uye_durum, 'aktivasyon' => $uye_aktivasyon, 'fiyat' => $uye_fiyat_no, 'satis_temsilcisi' => $uye_satis_temsilcisi, 
            'il' => $il, 'ilce' => $ilce, 'mahalle' => $mahalle, 'adres' => $uye_adres, 'posta_kodu' => $uye_posta_kodu, 'tel' => $uye_cep_tel, 
            'sabit_tel' => $uye_sabit_tel, 'muhasebe_kodu' => $muhasebe_kodu, 'DEGISTIRME_TARIHI' => $DEGISTIRME_TARIHI, 'MUHKODU_ALIS' => $MUHKODU_ALIS,
             'MUHKODU_SATIS' => $MUHKODU_SATIS, 'id' => $id
        ];
    }
    
    $updateSuccess = $database->update($sql, $param);
    
    // Fetch updated user data
    $result = $database->fetch("SELECT * FROM uyeler WHERE id = :id", ['id' => $id]);
    $uyeil = $database->fetch("SELECT * FROM iller WHERE il_id = :il_id", ['il_id' => $result["il"]]);
    $uyeilce = $database->fetch("SELECT * FROM ilceler WHERE ilce_id = :ilce_id", [ 'ilce_id' =>$result["ilce"]]);
    $users = $database->fetch("SELECT full_name FROM users WHERE id = :id", [ 'id' => $result["satis_temsilcisi"] ]);

    if ($users) {
        $user = $users['full_name'];
    } else {
        $user = 'İnternet Satış';
    }
    $degistirme_tarihi = date("d.m.Y H:i:s", strtotime($currentDateTime . " +3 hours"));
    $dosya_adi = 'cariduzenle_' . $result['muhasebe_kodu'] . '.xml';
    $dosya_yolu = 
    CariXmlOlustur(
        $result['muhasebe_kodu'], $result['ad'], $result['soyad'], $result['email'], $result['parola'], $result['tc_no'], $result['ulke'], $uyeil["il_adi"], $uyeilce["ilce_adi"], 
        $result['posta_kodu'], $result['tel'], $result['adres'], $result['firmaUnvani'], $result['vergi_no'], $result['vergi_dairesi'], $result['sabit_tel'], 
        $uye_kayit_tipi, $dosya_adi, date("d.m.Y H:i:s", strtotime($currentDateTime . " +3 hours")), $result['MUHKODU_ALIS'],  $result['MUHKODU_SATIS'], $result['fiyat'], 
        $result['satis_temsilcisi'], $user
    );
  
    header("Location: ../../pages/b2b/b2b-uye-duzenle.php?id=" . $id . "&s=1");
}
?>