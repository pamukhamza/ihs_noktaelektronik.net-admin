<?php
// Veritabanı bağlantısı
try {
    $dbB = new PDO("mysql:host=localhost;dbname=nokta;charset=utf8", "root", "");
    $dbB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanına bağlanılamadı: " . $e->getMessage());
}

try {
    // A veritabanından verileri al
    $queryA = "SELECT id, BLKODU, UrunKodu, UrunAdiTR, UrunAdiEN, genel_ozellikler_TR, genel_ozellikler_EN, 
                      teknik_ozellikler_EN, teknik_ozellikler_TR, MarkaID, uygulamalar_TR, uygulamalar_EN, 
                      KSF4, KSF3, KSF2, KSF1, DSF4, DSF3, DSF2, DSF1, DOVIZ_KULLAN, DOVIZ_BIRIMI, stok, kdv,
                      WEBDE_GORUNSUN, OZELALANTANIM_18, BIRIMI, proje, cok_satan, cok_goren 
               FROM nokta_urunler_net2";
    $stmtA = $dbB->query($queryA);
    $productsA = $stmtA->fetchAll(PDO::FETCH_ASSOC);

    // Her bir kayıt için kontrol yap
    foreach ($productsA as $product) {
        $urunKodu = $product['UrunKodu'];

        // B veritabanında UrunKodu ile eşleşme kontrolü
        $queryB = "SELECT COUNT(*) FROM nokta_urunler WHERE UrunKodu = :urunKodu";
        $stmtB = $dbB->prepare($queryB);
        $stmtB->execute([':urunKodu' => $urunKodu]);
        $exists = $stmtB->fetchColumn();

        if ($exists) {
            // Eşleşiyorsa güncelleme yap
            $updateQuery = "UPDATE nokta_urunler SET id = :id,
                    BLKODU = :BLKODU, UrunAdiTR = :UrunAdiTR, UrunAdiEN = :UrunAdiEN,
                    OzelliklerTR = :genel_ozellikler_TR, OzelliklerEN = :genel_ozellikler_EN,
                    BilgiEN = :teknik_ozellikler_EN, BilgiTR = :teknik_ozellikler_TR,
                    MarkaID = :MarkaID, UygulamalarTr = :uygulamalar_TR, UygulamalarEn = :uygulamalar_EN,
                    KSF4 = :KSF4, KSF3 = :KSF3, KSF2 = :KSF2, KSF1 = :KSF1, DSF4 = :DSF4,
                    DSF3 = :DSF3, DSF2 = :DSF2, DSF1 = :DSF1, DOVIZ_KULLAN = :DOVIZ_KULLAN, DOVIZ_BIRIMI = :DOVIZ_BIRIMI,
                    stok = :stok, kdv = :kdv,  web_comtr = :WEBDE_GORUNSUN,
                    desi = :OZELALANTANIM_18, birim = :BIRIMI, proje = :proje,
                    cok_satan = :cok_satan, cok_goren = :cok_goren
                WHERE UrunKodu = :UrunKodu";

            $stmtUpdate = $dbB->prepare($updateQuery);
            $stmtUpdate->execute([
                ':id' => $product['id'],
                ':BLKODU' => $product['BLKODU'],
                ':UrunAdiTR' => $product['UrunAdiTR'],
                ':UrunAdiEN' => $product['UrunAdiEN'],
                ':genel_ozellikler_TR' => $product['genel_ozellikler_TR'],
                ':genel_ozellikler_EN' => $product['genel_ozellikler_EN'],
                ':teknik_ozellikler_EN' => $product['teknik_ozellikler_EN'],
                ':teknik_ozellikler_TR' => $product['teknik_ozellikler_TR'],
                ':MarkaID' => $product['MarkaID'],
                ':uygulamalar_TR' => $product['uygulamalar_TR'],
                ':uygulamalar_EN' => $product['uygulamalar_EN'],
                ':KSF4' => $product['KSF4'],
                ':KSF3' => $product['KSF3'],
                ':KSF2' => $product['KSF2'],
                ':KSF1' => $product['KSF1'],
                ':DSF4' => $product['DSF4'],
                ':DSF3' => $product['DSF3'],
                ':DSF2' => $product['DSF2'],
                ':DSF1' => $product['DSF1'],
                ':DOVIZ_KULLAN' => $product['DOVIZ_KULLAN'],
                ':DOVIZ_BIRIMI' => $product['DOVIZ_BIRIMI'],
                ':stok' => $product['stok'],
                ':kdv' => $product['kdv'],
                ':WEBDE_GORUNSUN' => $product['WEBDE_GORUNSUN'],
                ':OZELALANTANIM_18' => $product['OZELALANTANIM_18'],
                ':BIRIMI' => $product['BIRIMI'],
                ':proje' => $product['proje'],
                ':cok_satan' => $product['cok_satan'],
                ':cok_goren' => $product['cok_goren'],
                ':UrunKodu' => $product['UrunKodu']
            ]);
            echo "Güncellendi: UrunKodu - $urunKodu\n </br>";
        } else {
            // Eşleşmiyorsa yeni kayıt ekle
            $insertQuery = "INSERT INTO nokta_urunler (id, BLKODU, UrunKodu, UrunAdiTR, UrunAdiEN, OzelliklerTR, OzelliklerEN,
                            BilgiEN, BilgiTR, MarkaID, UygulamalarTr, UygulamalarEn, KSF4, KSF3, KSF2, KSF1,
                            DSF4, DSF3, DSF2, DSF1, DOVIZ_KULLAN, DOVIZ_BIRIMI, stok, kdv, web_comtr, desi, birim,
                            proje, cok_satan, cok_goren) 
                    VALUES (:id, :BLKODU, :UrunKodu, :UrunAdiTR, :UrunAdiEN, :genel_ozellikler_TR, :genel_ozellikler_EN,
                            :teknik_ozellikler_EN, :teknik_ozellikler_TR, :MarkaID, :uygulamalar_TR, :uygulamalar_EN, :KSF4, :KSF3,
                            :KSF2, :KSF1, :DSF4, :DSF3, :DSF2, :DSF1, :DOVIZ_KULLAN, :DOVIZ_BIRIMI, :stok, :kdv,  :WEBDE_GORUNSUN,
                            :OZELALANTANIM_18, :BIRIMI, :proje, :cok_satan, :cok_goren)";

            $stmtInsert = $dbB->prepare($insertQuery);
            $stmtInsert->execute([
                ':id' => $product['id'],
                ':BLKODU' => $product['BLKODU'],
                ':UrunKodu' => $product['UrunKodu'],
                ':UrunAdiTR' => $product['UrunAdiTR'],
                ':UrunAdiEN' => $product['UrunAdiEN'],
                ':genel_ozellikler_TR' => $product['genel_ozellikler_TR'],
                ':genel_ozellikler_EN' => $product['genel_ozellikler_EN'],
                ':teknik_ozellikler_EN' => $product['teknik_ozellikler_EN'],
                ':teknik_ozellikler_TR' => $product['teknik_ozellikler_TR'],
                ':MarkaID' => $product['MarkaID'],
                ':uygulamalar_TR' => $product['uygulamalar_TR'],
                ':uygulamalar_EN' => $product['uygulamalar_EN'],
                ':KSF4' => $product['KSF4'],
                ':KSF3' => $product['KSF3'],
                ':KSF2' => $product['KSF2'],
                ':KSF1' => $product['KSF1'],
                ':DSF4' => $product['DSF4'],
                ':DSF3' => $product['DSF3'],
                ':DSF2' => $product['DSF2'],
                ':DSF1' => $product['DSF1'],
                ':DOVIZ_KULLAN' => $product['DOVIZ_KULLAN'],
                ':DOVIZ_BIRIMI' => $product['DOVIZ_BIRIMI'],
                ':stok' => $product['stok'],
                ':kdv' => $product['kdv'],
                ':WEBDE_GORUNSUN' => $product['WEBDE_GORUNSUN'],
                ':OZELALANTANIM_18' => $product['OZELALANTANIM_18'],
                ':BIRIMI' => $product['BIRIMI'],
                ':proje' => $product['proje'],
                ':cok_satan' => $product['cok_satan'],
                ':cok_goren' => $product['cok_goren']
            ]);
            echo "Eklendi: UrunKodu - $urunKodu\n </br>";
        }
    }

    echo "Veri senkronizasyonu tamamlandı.";
} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
}
