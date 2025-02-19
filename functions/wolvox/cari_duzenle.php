<?php
    function CariXmlOlustur($CARIKODU, $ADI, $SOYADI, $E_MAIL, $WEB_USER_PASSW, $TC_KIMLIK_NO, $ULKESI_1, $ILI_1, $ILCESI_1, $POSTA_KODU_1, $CEP_TEL, $ADRESI_1, $TICARI_UNVANI, $VERGI_NO, $VERGI_DAIRESI, $TEL1, $uye_tipi, $dosya_ad, $DEGISTIRME_TARIHI, $MUHKODU_ALIS, $MUHKODU_SATIS, $STOK_FIYATI, $PAZ_BLCRKODU, $PAZ_ADI) {
        // Create a new XML document
        $xmlDoc = new DOMDocument('1.0', 'UTF-8');
        $xmlDoc->formatOutput = true;
        $root = $xmlDoc->createElement('WCR');
        $xmlDoc->appendChild($root);
    
        // AYAR ALANI BASLANGIC
            $ayar = $xmlDoc->createElement('AYAR');
            $root->appendChild($ayar);
            $trsver = $xmlDoc->createElement('TRSVER');
            $trsver->appendChild($xmlDoc->createCDATASection('ASWCR1.02.03'));
            $ayar->appendChild($trsver);
            $dbname = $xmlDoc->createElement('DBNAME');
            $dbname->appendChild($xmlDoc->createCDATASection('WOLVOX'));
            $ayar->appendChild($dbname);
            $peruser = $xmlDoc->createElement('PERSUSER');
            $peruser->appendChild($xmlDoc->createCDATASection('sa'));
            $ayar->appendChild($peruser);
            $sube_kodu = $xmlDoc->createElement('SUBE_KODU');
            $sube_kodu->appendChild($xmlDoc->createCDATASection('3402'));
            $ayar->appendChild($sube_kodu);
        //AYAR ALANI SON
    
        // CARI BILGI ALANI BASLANGIC
        $cari = $xmlDoc->createElement('CARI');
        $root->appendChild($cari);
    
        $carikoduXML = $xmlDoc->createElement('CARIKODU');
        $carikoduXML->appendChild($xmlDoc->createCDATASection($CARIKODU));
        $cari->appendChild($carikoduXML);
        $ozel_kodu1 = $xmlDoc->createElement('OZEL_KODU1');
        $ozel_kodu1->appendChild($xmlDoc->createCDATASection('B2B'));
        $cari->appendChild($ozel_kodu1);
        $ozel_kodu2 = $xmlDoc->createElement('OZEL_KODU2');
        $ozel_kodu2->appendChild($xmlDoc->createCDATASection($PAZ_ADI));
        $cari->appendChild($ozel_kodu2);
        $ozel_kodu3 = $xmlDoc->createElement('OZEL_KODU3');
        $ozel_kodu3->appendChild($xmlDoc->createCDATASection($uye_tipi));
        $cari->appendChild($ozel_kodu3);
        $muhkodualis = $xmlDoc->createElement('MUHKODU_ALIS');
        $muhkodualis->appendChild($xmlDoc->createCDATASection($MUHKODU_ALIS));
        $cari->appendChild($muhkodualis);
        $muhkodusatis = $xmlDoc->createElement('MUHKODU_SATIS');
        $muhkodusatis->appendChild($xmlDoc->createCDATASection($MUHKODU_SATIS));
        $cari->appendChild($muhkodusatis);
        $stokfiyati = $xmlDoc->createElement('STOK_FIYATI');
        $stokfiyati->appendChild($xmlDoc->createCDATASection($STOK_FIYATI));
        $cari->appendChild($stokfiyati);
        $paz_blcrkodu = $xmlDoc->createElement('PAZ_BLCRKODU');
        $paz_blcrkodu->appendChild($xmlDoc->createCDATASection($PAZ_BLCRKODU));
        $cari->appendChild($paz_blcrkodu);
        $adi = $xmlDoc->createElement('ADI');
        $adi->appendChild($xmlDoc->createCDATASection($ADI));
        $cari->appendChild($adi);
        $soyadi = $xmlDoc->createElement('SOYADI');
        $soyadi->appendChild($xmlDoc->createCDATASection($SOYADI));
        $cari->appendChild($soyadi);
        $emailxml = $xmlDoc->createElement('E_MAIL');
        $emailxml->appendChild($xmlDoc->createCDATASection($E_MAIL));
        $cari->appendChild($emailxml);
        $kullaniciadixml = $xmlDoc->createElement('WEB_USER_NAME');
        $kullaniciadixml->appendChild($xmlDoc->createCDATASection($E_MAIL));
        $cari->appendChild($kullaniciadixml);
        $parolaxml = $xmlDoc->createElement('WEB_USER_PASSW');
        $parolaxml->appendChild($xmlDoc->createCDATASection($WEB_USER_PASSW));
        $cari->appendChild($parolaxml);
        $tcxml = $xmlDoc->createElement('TC_KIMLIK_NO');
        $tcxml->appendChild($xmlDoc->createCDATASection($TC_KIMLIK_NO));
        $cari->appendChild($tcxml);
        $ulkexml = $xmlDoc->createElement('ULKESI_1');
        $ulkexml->appendChild($xmlDoc->createCDATASection($ULKESI_1));
        $cari->appendChild($ulkexml);
        $ilxml = $xmlDoc->createElement('ILI_1');
        $ilxml->appendChild($xmlDoc->createCDATASection($ILI_1));
        $cari->appendChild($ilxml);
        $ilcexml = $xmlDoc->createElement('ILCESI_1');
        $ilcexml->appendChild($xmlDoc->createCDATASection($ILCESI_1));
        $cari->appendChild($ilcexml);
        $postakoduxml = $xmlDoc->createElement('POSTA_KODU_1');
        $postakoduxml->appendChild($xmlDoc->createCDATASection($POSTA_KODU_1));
        $cari->appendChild($postakoduxml);
        $cep_tel = $xmlDoc->createElement('CEP_TEL');
        $cep_tel->appendChild($xmlDoc->createCDATASection($CEP_TEL));
        $cari->appendChild($cep_tel);
        $adresxml = $xmlDoc->createElement('ADRESI_1');
        $adresxml->appendChild($xmlDoc->createCDATASection($ADRESI_1));
        $cari->appendChild($adresxml);
        $firmaUnvani = $xmlDoc->createElement('TICARI_UNVANI');
        $firmaUnvani->appendChild($xmlDoc->createCDATASection($TICARI_UNVANI));
        $cari->appendChild($firmaUnvani);
        $verginoxml = $xmlDoc->createElement('VERGI_NO');
        $verginoxml->appendChild($xmlDoc->createCDATASection($VERGI_NO));
        $cari->appendChild($verginoxml);
        $vergidairesixml = $xmlDoc->createElement('VERGI_DAIRESI');
        $vergidairesixml->appendChild($xmlDoc->createCDATASection($VERGI_DAIRESI));
        $cari->appendChild($vergidairesixml);
        $telxml = $xmlDoc->createElement('TEL1');
        $telxml->appendChild($xmlDoc->createCDATASection($TEL1));
        $cari->appendChild($telxml);
        $degistirme_tarihxml = $xmlDoc->createElement('DEGISTIRME_TARIHI');
        $degistirme_tarihxml->appendChild($xmlDoc->createCDATASection($DEGISTIRME_TARIHI));
        $cari->appendChild($degistirme_tarihxml);
    
        $xmlFileName = $dosya_ad . $CARIKODU . '.xml';
        $xmlDoc->save('../../assets/cari/' . $xmlFileName);
    }