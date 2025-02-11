<?php
require_once "../db.php"; // Include the Database class
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
    $currentDateTime = date("Y-m-d H:i:s");
    $DEGISTIRME_TARIHI = date("Y-m-d H:i:s", strtotime($currentDateTime . " +3 hours")); // Add 3 hours for the timestamp

    if ($uye_parola == '' || $uye_parola == NULL) {
        $sql = "UPDATE uyeler SET uye_tipi = :uye_tipi, email = :email, ad = :ad, soyad = :soyad, tc_no = :tc_no, firmaUnvani = :firmaUnvani,
            vergi_dairesi = :vergi_dairesi, vergi_no = :vergi_no, aktif = :aktif, aktivasyon = :aktivasyon, fiyat = :fiyat, satis_temsilcisi = :satis_temsilcisi,
            il = :il, ilce = :ilce, mahalle = :mahalle, adres = :adres, posta_kodu = :posta_kodu, tel = :tel, sabit_tel = :sabit_tel, muhasebe_kodu = :muhasebe_kodu,
            DEGISTIRME_TARIHI = :DEGISTIRME_TARIHI WHERE id = :id";
            
        $param = [
            'uye_tipi' => $uye_kayit_tipi, 'email' => $uye_email, 'ad' => $uye_adi, 'soyad' => $uye_soyadi, 'tc_no' => $uye_kimlik_no, 
            'firmaUnvani' => $uye_firma_adi, 'vergi_dairesi' => $uye_vergi_dairesi, 'vergi_no' => $uye_vergi_no, 'aktif' => $uye_durum, 
            'aktivasyon' => $uye_aktivasyon, 'fiyat' => $uye_fiyat_no, 'satis_temsilcisi' => $uye_satis_temsilcisi, 'il' => $il, 
            'ilce' => $ilce, 'mahalle' => $mahalle, 'adres' => $uye_adres, 'posta_kodu' => $uye_posta_kodu, 'tel' => $uye_cep_tel, 
            'sabit_tel' => $uye_sabit_tel, 'muhasebe_kodu' => $muhasebe_kodu, 'DEGISTIRME_TARIHI' => $DEGISTIRME_TARIHI, 'id' => $id
        ];
    } else {
        $uye_parola = md5($uye_parola); // Parola şifreleme
        $sql = "UPDATE uyeler SET uye_tipi = :uye_tipi, email = :email, parola = :parola, ad = :ad, soyad = :soyad, tc_no = :tc_no, firmaUnvani = :firmaUnvani, 
            vergi_dairesi = :vergi_dairesi, vergi_no = :vergi_no, aktif = :aktif, aktivasyon = :aktivasyon, fiyat = :fiyat, satis_temsilcisi = :satis_temsilcisi,
            il = :il, ilce = :ilce, mahalle = :mahalle, adres = :adres, posta_kodu = :posta_kodu, tel = :tel, sabit_tel = :sabit_tel, muhasebe_kodu = :muhasebe_kodu,
            DEGISTIRME_TARIHI = :DEGISTIRME_TARIHI WHERE id = :id ";
    
        $param = [
            'uye_tipi' => $uye_kayit_tipi, 'email' => $uye_email, 'parola' => $uye_parola, 'ad' => $uye_adi, 'soyad' => $uye_soyadi, 
            'tc_no' => $uye_kimlik_no, 'firmaUnvani' => $uye_firma_adi, 'vergi_dairesi' => $uye_vergi_dairesi, 'vergi_no' => $uye_vergi_no, 
            'aktif' => $uye_durum, 'aktivasyon' => $uye_aktivasyon, 'fiyat' => $uye_fiyat_no, 'satis_temsilcisi' => $uye_satis_temsilcisi, 
            'il' => $il, 'ilce' => $ilce, 'mahalle' => $mahalle, 'adres' => $uye_adres, 'posta_kodu' => $uye_posta_kodu, 'tel' => $uye_cep_tel, 
            'sabit_tel' => $uye_sabit_tel, 'muhasebe_kodu' => $muhasebe_kodu, 'DEGISTIRME_TARIHI' => $DEGISTIRME_TARIHI, 'id' => $id
        ];
    }
    
    $updateSuccess = $database->update($sql, $param);
    
    // Fetch updated user data
    $result = $database->fetch("SELECT * FROM uyeler WHERE id = :id", ['id' => $id]);
    $uyeil = $database->fetch("SELECT * FROM iller WHERE il_id = :il_id", ['il_id' => $result["il"]]);
    $uyeilce = $database->fetch("SELECT * FROM ilceler WHERE ilce_id = :ilce_id", [ 'ilce_id' =>$result["ilce"]]);

    // Generate XML
    $xmlDoc = new DOMDocument('1.0', 'UTF-8');
    $xmlDoc->formatOutput = true;
    $root = $xmlDoc->createElement('WCR');
    $xmlDoc->appendChild($root);

    // AYAR ALANI
    $ayar = $xmlDoc->createElement('AYAR');
    $root->appendChild($ayar);
    $ayarFields = ['TRSVER' => 'ASWCR1.02.03', 'DBNAME' => 'WOLVOX', 'PERSUSER' => 'sa', 'SUBE_KODU' => '3402'];
    foreach ($ayarFields as $key => $value) {
        $element = $xmlDoc->createElement($key);
        $element->appendChild($xmlDoc->createCDATASection($value));
        $ayar->appendChild($element);
    }

    // CARI BILGI ALANI
    $cari = $xmlDoc->createElement('CARI');
    $root->appendChild($cari);
    $cariFields = [
        'CARIKODU' => $result['muhasebe_kodu'], 
        'OZEL_KODU1' => 'B2B', 
        'OZEL_KODU3' => 'Bayi',
        'MUHKODU_ALIS' => $result['MUHKODU_ALIS'] ?? "120 01 50000", 
        'MUHKODU_SATIS' => $result['MUHKODU_SATIS'] ?? "320 01 50000",
        'STOK_FIYATI' => $result['fiyat'] ?? "", 
        'PAZ_BLCRKODU' => $result['satis_temsilcisi'] ?? "",
        'E_MAIL' => $result['email'] ?? "", 
        'WEB_USER_NAME' => $result['email'] ?? "", 
        'WEB_USER_PASSW' => $result['parola'] ?? "",
        'TC_KIMLIK_NO' => $result['tc_no'] ?? "", 
        'ULKESI_1' => $result['ulke'] ?? "", 
        'ILI_1' => $uyeil["il_adi"] ?? "",
        'ILCESI_1' => $uyeilce["ilce_adi"] ?? "", 
        'POSTA_KODU_1' => $result['posta_kodu'] ?? "", 
        'CEP_TEL' => $result['tel'] ?? "",
        'ADRESI_1' => $result['adres'] ?? "", 
        'ADI' => $result['ad'], 
        'SOYADI' => $result['soyad'],
        'TICARI_UNVANI' => $result['firmaUnvani'] ?? "", 
        'VERGI_NO' => $result['vergi_no'] ?? "",
        'VERGI_DAIRESI' => $result['vergi_dairesi'] ?? "", 
        'TEL1' => $result['sabit_tel'] ?? "",
        'DEGISTIRME_TARIHI' => date("d.m.Y H:i:s", strtotime($currentDateTime . " +3 hours")),
        'DOVIZ_KULLAN' => $result['DOVIZ_KULLAN'] ?? "", 
        'DOVIZ_BIRIMI' => $result['DOVIZ_BIRIMI'] ?? ""
    ];
    foreach ($cariFields as $key => $value) {
        $element = $xmlDoc->createElement($key);
        $element->appendChild($xmlDoc->createCDATASection($value));
        $cari->appendChild($element);
    }
    // Save XML to file
    $xmlFileName = '../../assets/xml/cari/cariduzenle_' . $id . '.xml';
    $xmlDoc->save(  $xmlFileName);
    // Redirect to the update page
    header("Location: ../../pages/b2b/b2b-uye-duzenle.php?id=" . $id . "&s=1");
}
?>
