<?php
function CariXmlOlustur(
    $CARIKODU, $ADI, $SOYADI, $E_MAIL, $WEB_USER_PASSW, $TC_KIMLIK_NO, $ULKESI_1, $ILI_1, $ILCESI_1, 
    $POSTA_KODU_1, $CEP_TEL, $ADRESI_1, $TICARI_UNVANI, $VERGI_NO, $VERGI_DAIRESI, $TEL1, 
    $uye_tipi, $dosya_ad, $DEGISTIRME_TARIHI, $MUHKODU_ALIS, $MUHKODU_SATIS, $STOK_FIYATI, 
    $PAZ_BLCRKODU, $PAZ_ADI
) {
    // Yeni XML belgesi oluştur
    $xmlDoc = new DOMDocument('1.0', 'UTF-8');
    $xmlDoc->formatOutput = true;
    $root = $xmlDoc->createElement('WCR');
    $xmlDoc->appendChild($root);

    // AYAR ALANI
    $ayar = $xmlDoc->createElement('AYAR');
    $root->appendChild($ayar);
    
    $ayarElements = [
        'TRSVER' => 'ASWCR1.02.03',
        'DBNAME' => 'WOLVOX',
        'PERSUSER' => 'sa',
        'SUBE_KODU' => '3402'
    ];
    foreach ($ayarElements as $key => $value) {
        $element = $xmlDoc->createElement($key);
        $element->appendChild($xmlDoc->createCDATASection($value));
        $ayar->appendChild($element);
    }

    // CARI BILGI ALANI
    $cari = $xmlDoc->createElement('CARI');
    $root->appendChild($cari);

    $cariElements = [
        'CARIKODU' => $CARIKODU,
        'OZEL_KODU1' => 'B2B',
        'OZEL_KODU2' => $PAZ_ADI,
        'OZEL_KODU3' => $uye_tipi,
        'MUHKODU_ALIS' => $MUHKODU_ALIS,
        'MUHKODU_SATIS' => $MUHKODU_SATIS,
        'STOK_FIYATI' => $STOK_FIYATI,
        'PAZ_BLCRKODU' => $PAZ_BLCRKODU,
        'ADI' => $ADI,
        'SOYADI' => $SOYADI,
        'E_MAIL' => $E_MAIL,
        'WEB_USER_NAME' => $E_MAIL,
        'WEB_USER_PASSW' => $WEB_USER_PASSW,
        'TC_KIMLIK_NO' => $TC_KIMLIK_NO,
        'ULKESI_1' => $ULKESI_1,
        'ILI_1' => $ILI_1,
        'ILCESI_1' => $ILCESI_1,
        'POSTA_KODU_1' => $POSTA_KODU_1,
        'CEP_TEL' => $CEP_TEL,
        'ADRESI_1' => $ADRESI_1,
        'TICARI_UNVANI' => $TICARI_UNVANI,
        'VERGI_NO' => $VERGI_NO,
        'VERGI_DAIRESI' => $VERGI_DAIRESI,
        'TEL1' => $TEL1,
        'DEGISTIRME_TARIHI' => $DEGISTIRME_TARIHI
    ];

    foreach ($cariElements as $key => $value) {
        $element = $xmlDoc->createElement($key);
        $element->appendChild($xmlDoc->createCDATASection($value));
        $cari->appendChild($element);
    }

    // XML dosyasını kaydet
    $xmlFileName = $dosya_ad;
    $filePath = __DIR__ . '/../../assets/xml/cari/' . $xmlFileName;
    $xmlDoc->save($filePath);

    return $filePath;
}
?>