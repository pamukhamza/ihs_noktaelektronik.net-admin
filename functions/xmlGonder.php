<?php
define("DB_SERVER", "noktanetdb.cbuq6a2265j6.eu-central-1.rds.amazonaws.com");
define("DB_USERNAME", "nokta");
define("DB_PASSWORD", "Dell28736.!");
define("DB_NAME", "noktanetdb");
$newDate = date('Y-m-d H:i:s', strtotime('+3 hours'));
// mb_convert_encoding() kullanarak Türkçe karakterleri düzgün şekilde dönüştürmek
function duzenleString($str) {
    // Diğer işlemleri uygula
    $replaceChars = array(
        'ç' => 'c', 'ğ' => 'g', 'ı' => 'i', 'i' => 'i',
        'ö' => 'o', 'ş' => 's', 'ü' => 'u', 'Ç' => 'C',
        'Ğ' => 'G', 'I' => 'I', 'İ' => 'I', 'Ö' => 'O',
        'Ş' => 'S', 'Ü' => 'U', ' ' => '-', '"' => '',
        "'" => '', '`' => '', '.' => '', ',' => '',
        ':' => '', ';' => '', '(' => '', ')' => '',
        '[' => '', ']' => '', '{' => '', '}' => '',
        '+' => '', '&' => '', '\\' => ''
    );
    $str = strtr($str, $replaceChars);
    $str = strtolower($str);// Büyük harfleri küçük harfe çevir
    $str = trim($str);// Başındaki ve sonundaki boşlukları sil
    $str = preg_replace('/\s+/', '-', $str); // Ortadaki boşlukları - ile değiştir
    $str = str_replace( ['---','--'], ['-','-'], $str );
    return $str;
}
function gelenFiyatDuzenle($sayi) {
    if (empty($sayi)) {
        return null;
    }
    // Virgül varsa noktaya çevir
    $sayi = str_replace(',', '.', $sayi);
    // Sayının formatını kontrol et
    if (!preg_match('/^\d+(\.\d{1,4})?$/', $sayi)) {
        return null;
    }
    // Sayıyı DECIMAL(13,4) formatına getir
    $sayi = number_format((float)$sayi, 4, '.', '');
    return $sayi;
}
function connectToDatabase() {
    $mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_error) {
        echo "Connection failed: " . $mysqli->connect_error;
    }
    $mysqli->set_charset("utf8");
    return $mysqli;
}
function connectToDatabasePDO() {
    $host = 'noktanetdb.cbuq6a2265j6.eu-central-1.rds.amazonaws.com'; // Veritabanı sunucusu
    $dbname = 'noktanetdb'; // Veritabanı adı
    $username = 'nokta'; // Veritabanı kullanıcı adı
    $password = 'Dell28736.!'; // Veritabanı şifresi
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        echo "Veritabanı bağlantısı başarısız: " . $e->getMessage();
        die();
    }
}


function stokMiktar($xmlData) {
    $pdo = connectToDatabasePDO();
    $xml = simplexml_load_string($xmlData);
    global $newDate;
    echo '<br>' . $newDate . ': Stok Miktar Tarama Başladı.<br>';
    if ($xml === false) {
        echo "Failed to parse XML Stok Envanter.";
        return;
    }
    if (!isset($xml->table->row)) {
        echo $newDate . ': Stok Miktar Tarama Tamamlandı.</br>';
        return;
    }
    try {
        // Build the update query with multiple value sets
        $updateQuery = "UPDATE nokta_urunler SET MIKTAR_KULBILIR = CASE BLKODU ";
        $valueSets = array();
        foreach ($xml->table->row as $row) {
            $BLKODU = (int)$row->BLKODU;
            $MIKTAR_KULBILIR = (int)$row->MIKTAR_KULBILIR;
            $MIKTAR_TERMIN = (int)$row->MIKTAR_TERMIN;
            $toplamMiktar = $MIKTAR_KULBILIR - $MIKTAR_TERMIN;
            $valueSets[] = "WHEN $BLKODU THEN '$toplamMiktar'";
        }
        $updateQuery .= implode(' ', $valueSets);
        $updateQuery .= " END, stok = CASE BLKODU ";
        $valueSets = array();
        foreach ($xml->table->row as $row) {
            $BLKODU = (int)$row->BLKODU;
            $MIKTAR_KULBILIR = (int)$row->MIKTAR_KULBILIR;
            $MIKTAR_TERMIN = (int)$row->MIKTAR_TERMIN;
            $toplamMiktar = $MIKTAR_KULBILIR - $MIKTAR_TERMIN;
            $valueSets[] = "WHEN $BLKODU THEN '$toplamMiktar'";
        }
        $updateQuery .= implode(' ', $valueSets);
        $updateQuery .= " END WHERE BLKODU IN (";
        $BLKODUs = array();
        foreach ($xml->table->row as $row) {
            $BLKODUs[] = (int)$row->BLKODU;
        }
        $updateQuery .= implode(',', $BLKODUs) . ")";
        // Execute the update query
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute();
        echo "$newDate: Stok Miktar Tarama Tamamlandı. <br>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}


//KULLANILAN FONKSİYONLAR
function odemeGonder() {
    global $newDate;
    $folderPath = "../assets/xml/pos/";
    $files = scandir($folderPath);

    if ($files === false) {
        echo json_encode(["hata" => "$newDate: XML dosyaları bulunamadı"]);
        return;
    }

    $xmlArray = array();

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;

        $filePath = $folderPath . $file;

        if (is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'xml') {
            libxml_use_internal_errors(true);
            $xml = simplexml_load_file($filePath);

            if ($xml === false) {
                $hatalar = [];
                foreach (libxml_get_errors() as $error) {
                    $hatalar[] = htmlspecialchars($error->message);
                }
                libxml_clear_errors();
                $xmlArray[$file] = ["hata" => $hatalar];
            } else {
                $xmlArray[$file] = $xml->asXML(); // RAW XML string dön
                $filePath = "../assets/xml/pos/$file";
                if (is_file($filePath)) {
                    unlink($filePath); // Dosyayı sil
                }
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($xmlArray);
}
function cariGonder() {
    global $newDate;
    $folderPath = "../assets/xml/cari/";
    $files = scandir($folderPath);

    if ($files === false) {
        echo json_encode(["hata" => "$newDate: XML dosyaları bulunamadı"]);
        return;
    }

    $xmlArray = array();

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;

        $filePath = $folderPath . $file;

        if (is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'xml') {
            libxml_use_internal_errors(true);
            $xml = simplexml_load_file($filePath);

            if ($xml === false) {
                $hatalar = [];
                foreach (libxml_get_errors() as $error) {
                    $hatalar[] = htmlspecialchars($error->message);
                }
                libxml_clear_errors();
                $xmlArray[$file] = ["hata" => $hatalar];
            } else {
                $xmlArray[$file] = $xml->asXML(); // RAW XML string dön
                $filePath = "../assets/xml/cari/$file";
                if (is_file($filePath)) {
                    unlink($filePath); // Dosyayı sil
                }
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($xmlArray);
}
function getAccountList($xmlData) {
    $mysqli = connectToDatabase();
    $xml = simplexml_load_string($xmlData);
    global $newDate;
    echo "$newDate: Cari Tarama Başladı. <br>";
    if ($xml === false) {
        echo "Failed to parse XML Cari Liste.";
        return;
    }
    if (!isset($xml->table->row)) {
        echo "$newDate: Cari Tarama Tamamlandı. <br>";
        return;
    }
    foreach ($xml->table->row as $row) {
        $BLKODU = (int)$row->BLKODU;
        $CARIKODU = $mysqli->real_escape_string((string)$row-> CARIKODU);
        $ADI = $mysqli->real_escape_string((string)$row-> ADI);
        $SOYADI = $mysqli->real_escape_string((string)$row-> SOYADI);
        $TICARI_UNVANI = $mysqli->real_escape_string((string)$row-> TICARI_UNVANI);
        $VERGI_DAIRESI = $mysqli->real_escape_string((string)$row-> VERGI_DAIRESI);
        $VERGI_NO = $mysqli->real_escape_string((string)$row-> VERGI_NO);
        $CEP_TEL = $mysqli->real_escape_string((string)$row-> CEP_TEL);
        $AKTIF = $mysqli->real_escape_string((string)$row-> AKTIF);
        $STOK_FIYATI = $mysqli->real_escape_string((string)$row-> STOK_FIYATI);
        $DOVIZ_KULLAN = $mysqli->real_escape_string((string)$row-> DOVIZ_KULLAN);
        $DOVIZ_BIRIMI = $mysqli->real_escape_string((string)$row-> DOVIZ_BIRIMI);
        $MUHKODU_ALIS = $mysqli->real_escape_string((string)$row-> MUHKODU_ALIS);
        $MUHKODU_SATIS = $mysqli->real_escape_string((string)$row-> MUHKODU_SATIS);
        $ADRESI_1 = $mysqli->real_escape_string((string)$row-> ADRESI_1);
        $ILI_1 = $mysqli->real_escape_string((string)$row-> ILI_1);
        $ILCESI_1 = $mysqli->real_escape_string((string)$row-> ILCESI_1);
        $POSTA_KODU_1 = $mysqli->real_escape_string((string)$row-> POSTA_KODU_1);
        $WEB_USER_NAME = $mysqli->real_escape_string((string)$row-> WEB_USER_NAME);
        $WEB_USER_PASSW = $mysqli->real_escape_string((string)$row-> WEB_USER_PASSW);
        $ULKESI_1 = $mysqli->real_escape_string((string)$row-> ULKESI_1);
        $ADI_SOYADI = $mysqli->real_escape_string((string)$row-> ADI_SOYADI);
        $CINSIYETI = $mysqli->real_escape_string((string)$row-> CINSIYETI);
        $TC_KIMLIK_NO = $mysqli->real_escape_string((string)$row-> TC_KIMLIK_NO);
        $PAZ_BLCRKODU = $mysqli->real_escape_string((string)$row-> PAZ_BLCRKODU);
        $OZEL_KODU3 = $mysqli->real_escape_string((string)$row-> OZEL_KODU3);
        $OZELALANTANIM_3 = $mysqli->real_escape_string((string)$row-> OZELALANTANIM_3);
        $OZELALANTANIM_27 = $mysqli->real_escape_string((string)$row-> OZELALANTANIM_27);
        $EFATURA_SENARYO = $mysqli->real_escape_string((string)$row-> EFATURA_SENARYO);
        $EFATURA_KULLAN = $mysqli->real_escape_string((string)$row-> EFATURA_KULLAN);
        $ALICI_GRUBU = $mysqli->real_escape_string((string)$row-> ALICI_GRUBU);
        $EIRSALIYE_KULLAN = $mysqli->real_escape_string((string)$row-> EIRSALIYE_KULLAN);
        $GRUBU = $mysqli->real_escape_string((string)$row-> GRUBU);
        $DEGISTIRME_TARIHI = date('Y-m-d H:i:s', strtotime((string)$row->DEGISTIRME_TARIHI));
        $currentDateTime = date("Y-m-d H:i:s");
        $KAYIT_TARIHI = date("Y-m-d H:i:s", strtotime($currentDateTime . " +3 hours"));

        $checkQueryIl = "SELECT * FROM iller WHERE il_adi = ?";
        $checkStatementIl = $mysqli->prepare($checkQueryIl);
        $checkStatementIl->bind_param("s", $ILI_1);
        $checkStatementIl->execute();
        $resultIl = $checkStatementIl->get_result();
        $checkStatementIl->close();
        $resultIlRow = $resultIl->fetch_assoc();
            if ($resultIl && $resultIl->num_rows > 0){
                $il = $resultIlRow["il_id"];
            }else {
                $il = "";
            }
        $checkQueryIlce = "SELECT * FROM ilceler WHERE ilce_adi = ? AND il_adi = ?";
        $checkStatementIlce = $mysqli->prepare($checkQueryIlce);
        $checkStatementIlce->bind_param("ss", $ILCESI_1, $ILI_1);
        $checkStatementIlce->execute();
        $resultIlce = $checkStatementIlce->get_result();
        $checkStatementIlce->close();
        $resultIlceRow = $resultIlce->fetch_assoc();
            if ($resultIlce && $resultIlce->num_rows > 0) {
                $ilce = $resultIlceRow["ilce_id"];
            } else {
                $ilce = ""; // Set $il to empty if there is no match
            }
        $aktif = 1;
        $checkQuery = "SELECT DEGISTIRME_TARIHI, id FROM uyeler WHERE BLKODU = ?";
        $stmt = $mysqli->prepare($checkQuery);
        $stmt->bind_param("i", $BLKODU);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        $stmt->close();
        if ($checkResult->num_rows > 0) {
            $existingRow = $checkResult->fetch_assoc();
            $existingDegistirmeTarihi = $existingRow['DEGISTIRME_TARIHI'];
            $uyeid = $existingRow["id"];
            if ($existingDegistirmeTarihi != $DEGISTIRME_TARIHI) {
                $updateQuery = "UPDATE uyeler 
                                SET muhasebe_kodu = ?, ad = ?, soyad = ?, firmaUnvani = ?, vergi_dairesi = ?, vergi_no = ?, tel = ?, aktif = ?, fiyat = ?, DOVIZ_KULLAN = ?,
                                    DOVIZ_BIRIMI = ?, MUHKODU_ALIS = ?, MUHKODU_SATIS = ?, adres = ?, il = ?, ilce = ?, posta_kodu = ?, email = ?, ulke = ?, ADI_SOYADI = ?,
                                    cinsiyet = ?, tc_no = ?, satis_temsilcisi = ?, uye_tipi = ?, OZELALANTANIM_3 = ?, OZELALANTANIM_27 = ?, DEGISTIRME_TARIHI = ?, EFATURA_SENARYO = ?,
                                    EFATURA_KULLAN = ?, ALICI_GRUBU = ?, EIRSALIYE_KULLAN = ?, GRUBU = ? WHERE BLKODU = ?";
                $stmt = $mysqli->prepare($updateQuery);
                $stmt->bind_param("ssssssssssssssssssssssssssssssssi", $CARIKODU, $ADI, $SOYADI, $TICARI_UNVANI, $VERGI_DAIRESI, $VERGI_NO, $CEP_TEL, $AKTIF, $STOK_FIYATI,
                    $DOVIZ_KULLAN, $DOVIZ_BIRIMI, $MUHKODU_ALIS, $MUHKODU_SATIS, $ADRESI_1, $il, $ilce, $POSTA_KODU_1, $WEB_USER_NAME, $ULKESI_1,
                    $ADI_SOYADI, $CINSIYETI, $TC_KIMLIK_NO, $PAZ_BLCRKODU, $OZEL_KODU3, $OZELALANTANIM_3, $OZELALANTANIM_27, $DEGISTIRME_TARIHI,
                    $EFATURA_SENARYO, $EFATURA_KULLAN, $ALICI_GRUBU, $EIRSALIYE_KULLAN, $GRUBU, $BLKODU);
                $stmt->execute();
                $stmt->close();

                $checkAddressQuery = "SELECT id FROM b2b_adresler WHERE uye_id = ?";
                $stmt = $mysqli->prepare($checkAddressQuery);
                $stmt->bind_param("i", $uyeid);
                $stmt->execute();
                $checkAddressResult = $stmt->get_result();
                $adres_turu = "teslimat";

                if ($checkAddressResult->num_rows > 0) {
                    $updateAddressQuery = "UPDATE b2b_adresler SET ad = ?, soyad = ?, adres_turu = ?, firma_adi = ?, il = ?, ilce = ?, vergi_dairesi = ?, vergi_no = ?, telefon = ?, aktif = ?, adres = ?, posta_kodu = ?, ulke = ?, tc_no = ? WHERE uye_id = ?";
                    $stmt = $mysqli->prepare($updateAddressQuery);
                    $stmt->bind_param("ssssiisssissssi", $ADI, $SOYADI, $adres_turu, $TICARI_UNVANI, $il, $ilce, $VERGI_DAIRESI, $VERGI_NO, $CEP_TEL, $aktif, $ADRESI_1, $POSTA_KODU_1, $ULKESI_1, $TC_KIMLIK_NO, $uyeid);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    $insertAddressQuery = "INSERT INTO b2b_adresler (uye_id, adres_turu, il, ilce, ad, soyad, firma_adi, vergi_dairesi, vergi_no, telefon, aktif, adres, posta_kodu, ulke, tc_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $mysqli->prepare($insertAddressQuery);
                    $stmt->bind_param("isiissssssissss", $uyeid, $adres_turu, $il, $ilce, $ADI, $SOYADI, $TICARI_UNVANI, $VERGI_DAIRESI, $VERGI_NO, $CEP_TEL, $aktif, $ADRESI_1, $POSTA_KODU_1, $ULKESI_1, $TC_KIMLIK_NO);
                    $stmt->execute();
                    $stmt->close();
                }
                echo "$newDate: Güncellenen Cari Kodu: $CARIKODU <br>";
            }
        } else {
                $checkQuery = "SELECT DEGISTIRME_TARIHI, id FROM uyeler WHERE muhasebe_kodu = ?";
                $stmt = $mysqli->prepare($checkQuery);
                $stmt->bind_param("s", $CARIKODU);
                $stmt->execute();
                $checkResult = $stmt->get_result();
                $stmt->close();
            if ($checkResult->num_rows > 0) {
                $existingRow = $checkResult->fetch_assoc();
                $existingDegistirmeTarihi = $existingRow['DEGISTIRME_TARIHI'];
                $uyeid = $existingRow["id"];
                if ($existingDegistirmeTarihi != $DEGISTIRME_TARIHI) {
                    $updateQuery = "UPDATE uyeler 
                                SET BLKODU = ?, ad = ?, soyad = ?, firmaUnvani = ?, vergi_dairesi = ?, vergi_no = ?, tel = ?, aktif = ?, fiyat = ?, DOVIZ_KULLAN = ?,
                                    DOVIZ_BIRIMI = ?, MUHKODU_ALIS = ?, MUHKODU_SATIS = ?, adres = ?, il = ?, ilce = ?, posta_kodu = ?, email = ?,  ulke = ?, ADI_SOYADI = ?,
                                    cinsiyet = ?, tc_no = ?, satis_temsilcisi = ?, uye_tipi = ?, OZELALANTANIM_3 = ?, OZELALANTANIM_27 = ?, DEGISTIRME_TARIHI = ?, EFATURA_SENARYO = ?,
                                    EFATURA_KULLAN = ?, ALICI_GRUBU = ?, EIRSALIYE_KULLAN = ?, GRUBU = ? WHERE muhasebe_kodu = ?";
                    $stmt = $mysqli->prepare($updateQuery);
                    $stmt->bind_param("issssssssssssssssssssssssssssssss", $BLKODU, $ADI, $SOYADI, $TICARI_UNVANI, $VERGI_DAIRESI, $VERGI_NO, $CEP_TEL, $AKTIF, $STOK_FIYATI,
                        $DOVIZ_KULLAN, $DOVIZ_BIRIMI, $MUHKODU_ALIS, $MUHKODU_SATIS, $ADRESI_1, $il, $ilce, $POSTA_KODU_1, $WEB_USER_NAME,  $ULKESI_1,
                        $ADI_SOYADI, $CINSIYETI, $TC_KIMLIK_NO, $PAZ_BLCRKODU, $OZEL_KODU3, $OZELALANTANIM_3, $OZELALANTANIM_27, $DEGISTIRME_TARIHI,
                        $EFATURA_SENARYO, $EFATURA_KULLAN, $ALICI_GRUBU, $EIRSALIYE_KULLAN, $GRUBU, $CARIKODU);
                    $stmt->execute();
                    $stmt->close();

                    // Check if the record already exists in adresler table
                    $checkAddressQuery = "SELECT id FROM b2b_adresler WHERE uye_id = ?";
                    $stmt = $mysqli->prepare($checkAddressQuery);
                    $stmt->bind_param("i", $uyeid);
                    $stmt->execute();
                    $checkAddressResult = $stmt->get_result();
                    $adres_turu = "teslimat";

                    if ($checkAddressResult->num_rows > 0) {
                        // Update the existing record in adresler table
                        $updateAddressQuery = "UPDATE b2b_adresler SET ad = ?, soyad = ?, adres_turu = ?, firma_adi = ?, il = ?, ilce = ?, vergi_dairesi = ?, vergi_no = ?, telefon = ?, aktif = ?, adres = ?, posta_kodu = ?, ulke = ?, tc_no = ? WHERE uye_id = ?";
                        $stmt = $mysqli->prepare($updateAddressQuery);
                        $stmt->bind_param("ssssiisssissssi", $ADI, $SOYADI, $adres_turu, $TICARI_UNVANI, $il, $ilce, $VERGI_DAIRESI, $VERGI_NO, $CEP_TEL, $aktif, $ADRESI_1, $POSTA_KODU_1, $ULKESI_1, $TC_KIMLIK_NO, $uyeid);
                        $stmt->execute();
                        $stmt->close();
                    } else {
                        $insertAddressQuery = "INSERT INTO b2b_adresler (uye_id, adres_turu, il, ilce, ad, soyad, firma_adi, vergi_dairesi, vergi_no, telefon, aktif, adres, posta_kodu, ulke, tc_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $mysqli->prepare($insertAddressQuery);
                        $stmt->bind_param("isiissssssissss", $uyeid, $adres_turu, $il, $ilce, $ADI, $SOYADI, $TICARI_UNVANI, $VERGI_DAIRESI, $VERGI_NO, $CEP_TEL, $aktif, $ADRESI_1, $POSTA_KODU_1, $ULKESI_1, $TC_KIMLIK_NO);
                        $stmt->execute();
                        $stmt->close();
                    }
                    echo "$newDate: Güncellenen Cari Kodu: $CARIKODU <br>";
                }
            }
            else {
                /* BURADA MUHASEBE KODUNU KONTROL ET EĞER AYNI İSE GÜNCELLEME YAP VE BLKODUNU DA GÜNCELLE DEĞİLSE INSERT YAP */
                $insertQuery = "INSERT INTO uyeler (BLKODU, muhasebe_kodu, ad, soyad, firmaUnvani, vergi_dairesi, vergi_no, tel, aktif, fiyat, DOVIZ_KULLAN, DOVIZ_BIRIMI, MUHKODU_ALIS,
                MUHKODU_SATIS, adres, il, ilce, posta_kodu, email, parola, ulke, ADI_SOYADI, cinsiyet, tc_no, satis_temsilcisi, uye_tipi, OZELALANTANIM_3, OZELALANTANIM_27,
                DEGISTIRME_TARIHI, EFATURA_SENARYO, EFATURA_KULLAN, ALICI_GRUBU, EIRSALIYE_KULLAN, kayit_tarihi, GRUBU)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($insertQuery);
                $stmt->bind_param("issssssssssssssssssssssssssssssssss", $BLKODU, $CARIKODU, $ADI, $SOYADI, $TICARI_UNVANI, $VERGI_DAIRESI, $VERGI_NO, $CEP_TEL, $AKTIF, $STOK_FIYATI,
                    $DOVIZ_KULLAN, $DOVIZ_BIRIMI, $MUHKODU_ALIS, $MUHKODU_SATIS, $ADRESI_1, $il, $ilce, $POSTA_KODU_1, $WEB_USER_NAME, $WEB_USER_PASSW, $ULKESI_1, $ADI_SOYADI, $CINSIYETI,
                    $TC_KIMLIK_NO, $PAZ_BLCRKODU, $OZEL_KODU3, $OZELALANTANIM_3, $OZELALANTANIM_27, $DEGISTIRME_TARIHI, $EFATURA_SENARYO, $EFATURA_KULLAN, $ALICI_GRUBU, $EIRSALIYE_KULLAN, $KAYIT_TARIHI, $GRUBU);

                if (!$stmt->execute()) {
                    echo "$newDate: INSERT sorgusunu yürütme hatası: " . $stmt->error;
                } else {
                    $lastInsertedID = $mysqli->insert_id;
                }
                $stmt->close();

                $adres_turu = "teslimat";
                // Insert a new record in adresler table
                $insertAddressQuery = "INSERT INTO b2b_adresler (uye_id, adres_turu, il, ilce, ad, soyad, firma_adi, vergi_dairesi, vergi_no, telefon, aktif, adres, posta_kodu, ulke, tc_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($insertAddressQuery);
                $stmt->bind_param("isiissssssissss", $lastInsertedID, $adres_turu, $il, $ilce, $ADI, $SOYADI, $TICARI_UNVANI, $VERGI_DAIRESI, $VERGI_NO, $CEP_TEL, $aktif, $ADRESI_1, $POSTA_KODU_1, $ULKESI_1, $TC_KIMLIK_NO);
                $stmt->execute();
                $stmt->close();

                echo "$newDate: Yeni Kayıt Eklendi. Cari Kodu: $CARIKODU <br>";
            }
        }
    }
    $mysqli->close();
    echo "$newDate: Cari Tarama Tamamlandı. <br>";
}
function getAccountBalanceList($xmlData) {
    $mysqli = connectToDatabase();
    $xml = simplexml_load_string($xmlData);
    global $newDate;
    echo "$newDate: Hesap Bakiye Taraması Başladı. <br>";
    if ($xml === false) {
        echo "Failed to parse XML Cari Bakiye Liste.";
        return;
    }
    if (!isset($xml->table->row)) {
        echo "$newDate: Hesap Bakiye Taraması Tamamlandı. <br>";
        return;
    }
    foreach ($xml->table->row as $row) {
        $BLKODU = (int)$row->BLKODU;
        $HESAP = $mysqli->real_escape_string((string)$row-> HESAP);
        $TPL_BRC = $mysqli->real_escape_string((string)$row-> TPL_BRC);
        $TPL_ALC = $mysqli->real_escape_string((string)$row-> TPL_ALC);
        $TPL_BKY = $mysqli->real_escape_string((string)$row-> TPL_BKY);
        $TPL_BTR = $mysqli->real_escape_string((string)$row-> TPL_BTR);
        $DVZ_HESAP = $mysqli->real_escape_string((string)$row-> DVZ_HESAP);
        $DVZ_TPLBRC = $mysqli->real_escape_string((string)$row-> DVZ_TPLBRC);
        $DVZ_TPLALC = $mysqli->real_escape_string((string)$row-> DVZ_TPLALC);
        $DVZ_BAKIYE = $mysqli->real_escape_string((string)$row-> DVZ_BAKIYE);
        $DVZ_BTR = $mysqli->real_escape_string((string)$row-> DVZ_BTR);
        $CARIKODU = $mysqli->real_escape_string((string)$row-> CARIKODU);

        $checkQuery = "SELECT TPL_BKY, DVZ_BAKIYE FROM cari_bakiye WHERE BLKODU = ?";
        $stmt = $mysqli->prepare($checkQuery);
        $stmt->bind_param("i", $BLKODU);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        $stmt->close();
        if ($checkResult->num_rows > 0) {
            $existingRow = $checkResult->fetch_assoc();
            $mevcutTplBakiye = $existingRow['TPL_BKY'];
            $mevcutDvzBakiye = $existingRow['DVZ_BAKIYE'];
            if ($mevcutTplBakiye != $TPL_BKY || $mevcutDvzBakiye != $DVZ_BAKIYE) {
                $updateQuery = "UPDATE cari_bakiye 
                            SET HESAP = ?, TPL_BRC = ?, TPL_ALC = ?, TPL_BKY = ?, TPL_BTR = ?, DVZ_HESAP = ?, DVZ_TPLBRC = ?, DVZ_TPLALC = ?, DVZ_BAKIYE = ?, DVZ_BTR = ?, CARIKODU = ? 
                            WHERE BLKODU = ?";
                $stmt = $mysqli->prepare($updateQuery);
                $stmt->bind_param("sssssssssssi",
                    $HESAP, $TPL_BRC, $TPL_ALC, $TPL_BKY, $TPL_BTR, $DVZ_HESAP, $DVZ_TPLBRC, $DVZ_TPLALC, $DVZ_BAKIYE, $DVZ_BTR, $CARIKODU, $BLKODU);
                $stmt->execute();
                $stmt->close();
                echo "$newDate: Güncellenen Hesap Bakiyesi: $CARIKODU <br>";
            }
        } else {
            $insertQuery = "INSERT INTO cari_bakiye (HESAP ,TPL_BRC ,TPL_ALC ,TPL_BKY ,TPL_BTR ,DVZ_HESAP ,DVZ_TPLBRC ,DVZ_TPLALC ,DVZ_BAKIYE ,DVZ_BTR ,BLKODU ,CARIKODU )
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($insertQuery);
            $stmt->bind_param("ssssssssssis", $HESAP ,$TPL_BRC ,$TPL_ALC ,$TPL_BKY ,$TPL_BTR ,$DVZ_HESAP ,$DVZ_TPLBRC ,$DVZ_TPLALC ,$DVZ_BAKIYE ,$DVZ_BTR ,$BLKODU ,$CARIKODU);
            $stmt->execute();
            $stmt->close();
            echo "$newDate: Yeni Hesap Bakiyesi : $CARIKODU <br>";
        }
    }
    $mysqli->close();
    echo "$newDate: Hesap Bakiye Taraması Tamamlandı. <br>";
}
function getAccountTransactionList($xmlData) {
    $mysqli = connectToDatabase();
    $xml = simplexml_load_string($xmlData);
    global $newDate;
    echo "$newDate: Evrak Taraması Başladı. <br>";
    if ($xml === false) {
        echo "Failed to parse XML Cari Hareket Liste.";
        return;
    }
    if (!isset($xml->table->row)) {
        echo "$newDate: Evrak taraması tamamlandı.  <br>";
        return;
    }
    foreach ($xml->table->row as $row) {
        $BLKODU = (int)$row->BLKODU;
        $BLCRKODU = $mysqli->real_escape_string((string)$row-> BLCRKODU);
        $EVRAK_NO = $mysqli->real_escape_string((string)$row-> EVRAK_NO);
        $TARIHI = $mysqli->real_escape_string((string)$row-> TARIHI);
        $MUH_DURUM = $mysqli->real_escape_string((string)$row-> MUH_DURUM);
        $MUH_HESKODU = $mysqli->real_escape_string((string)$row-> MUH_HESKODU);
        $DOVIZ_KULLAN = $mysqli->real_escape_string((string)$row-> DOVIZ_KULLAN);
        $DOVIZ_ALIS = $mysqli->real_escape_string((string)$row-> DOVIZ_ALIS);
        $DOVIZ_SATIS = $mysqli->real_escape_string((string)$row-> DOVIZ_SATIS);
        $KPBDVZ = $mysqli->real_escape_string((string)$row-> KPBDVZ);
        $DOVIZ_BIRIMI = $mysqli->real_escape_string((string)$row-> DOVIZ_BIRIMI);
        $DOVIZ_HES_ISLE = (int)$row->DOVIZ_HES_ISLE;
        $ACIKLAMA = $mysqli->real_escape_string((string)$row-> ACIKLAMA);
        $KASA_ADI = $mysqli->real_escape_string((string)$row-> KASA_ADI);
        $BANKA_ADI = $mysqli->real_escape_string((string)$row-> BANKA_ADI);
        $KKARTI_DETAY = $mysqli->real_escape_string((string)$row-> KKARTI_DETAY);
        $ENTEGRASYON = $mysqli->real_escape_string((string)$row-> ENTEGRASYON);
        $KPB_BTUT = $mysqli->real_escape_string((string)$row-> KPB_BTUT);
        $KPB_ATUT = $mysqli->real_escape_string((string)$row-> KPB_ATUT);
        $DVZ_BTUT = $mysqli->real_escape_string((string)$row-> DVZ_BTUT);
        $DVZ_ATUT = $mysqli->real_escape_string((string)$row-> DVZ_ATUT);
        $ISLEM_TURU = (int)$row->ISLEM_TURU;
        $SILINDI = (int)$row->SILINDI;
        $DEGISTIRME_TARIHI = date('Y-m-d H:i:s', strtotime((string)$row->DEGISTIRME_TARIHI));
        $VADESI = date('Y-m-d', strtotime((string)$row->VADESI));

        $checkQuery = "SELECT * FROM uyeler_hareket WHERE EVRAK_NO = ?";
        $stmt = $mysqli->prepare($checkQuery);
        $stmt->bind_param("s", $EVRAK_NO);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        $stmt->close();
        if ($checkResult->num_rows > 0) {
            $existingRow = $checkResult->fetch_assoc();
            $existingDegistirmeTarihi = $existingRow['DEGISTIRME_TARIHI'];
            if ($existingDegistirmeTarihi != $DEGISTIRME_TARIHI) {
                $updateQuery = "UPDATE uyeler_hareket 
                                SET BLCRKODU = ?, EVRAK_NO = ?, TARIHI = ?, VADESI = ?, MUH_DURUM = ?, MUH_HESKODU = ?, DOVIZ_KULLAN = ?, DOVIZ_ALIS = ?, DOVIZ_SATIS = ?, KPBDVZ = ?,
                                    DOVIZ_BIRIMI = ?, DOVIZ_HES_ISLE = ?, ACIKLAMA = ?, KASA_ADI = ?, BANKA_ADI = ?, KKARTI_DETAY = ?, ENTEGRASYON = ?, KPB_BTUT = ?, KPB_ATUT = ?,
                                    DVZ_BTUT = ?, DVZ_ATUT = ?, DEGISTIRME_TARIHI = ?, ISLEM_TURU = ?, SILINDI = ? WHERE EVRAK_NO = ?";
                $stmt = $mysqli->prepare($updateQuery);
                $stmt->bind_param("sssssssssssissssssssssiis", $BLCRKODU, $EVRAK_NO, $TARIHI, $VADESI, $MUH_DURUM, $MUH_HESKODU, $DOVIZ_KULLAN, $DOVIZ_ALIS,
                    $DOVIZ_SATIS, $KPBDVZ, $DOVIZ_BIRIMI, $DOVIZ_HES_ISLE, $ACIKLAMA, $KASA_ADI, $BANKA_ADI, $KKARTI_DETAY, $ENTEGRASYON, $KPB_BTUT, $KPB_ATUT, $DVZ_BTUT, $DVZ_ATUT,
                    $DEGISTIRME_TARIHI, $ISLEM_TURU, $SILINDI, $EVRAK_NO);
                $stmt->execute();
                $stmt->close();
                echo "$newDate: Güncellenen Evrak Numarası: $EVRAK_NO <br>";
            }
        } else {
            $insertQuery = "INSERT INTO uyeler_hareket (BLKODU ,BLCRKODU ,EVRAK_NO ,TARIHI ,VADESI ,MUH_DURUM ,MUH_HESKODU ,DOVIZ_KULLAN ,DOVIZ_ALIS ,DOVIZ_SATIS ,KPBDVZ ,DOVIZ_BIRIMI ,
                           DOVIZ_HES_ISLE ,ACIKLAMA ,KASA_ADI ,BANKA_ADI ,KKARTI_DETAY ,ENTEGRASYON ,KPB_BTUT ,KPB_ATUT ,DVZ_BTUT ,DVZ_ATUT ,DEGISTIRME_TARIHI ,ISLEM_TURU, SILINDI)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $mysqli->prepare($insertQuery);
            $stmt->bind_param("isssssssssssissssssssssii", $BLKODU ,$BLCRKODU ,$EVRAK_NO ,$TARIHI ,$VADESI ,$MUH_DURUM ,$MUH_HESKODU ,$DOVIZ_KULLAN ,
                $DOVIZ_ALIS ,$DOVIZ_SATIS ,$KPBDVZ ,$DOVIZ_BIRIMI ,$DOVIZ_HES_ISLE ,$ACIKLAMA ,$KASA_ADI ,$BANKA_ADI ,$KKARTI_DETAY ,$ENTEGRASYON ,$KPB_BTUT ,$KPB_ATUT ,$DVZ_BTUT ,
                $DVZ_ATUT ,$DEGISTIRME_TARIHI ,$ISLEM_TURU, $SILINDI);
            $stmt->execute();
            $stmt->close();
            echo "$newDate: Yeni Evrak Numarası: $EVRAK_NO <br>";
        }
    }
    $mysqli->close();
    echo "$newDate: Evrak taraması tamamlandı. <br>";
}
function getAccountTransactionSil($xmlData) {
    $pdo = connectToDatabasePDO();
    $xml = simplexml_load_string($xmlData);
    global $newDate;
    echo "$newDate: Evrak Silindi Kontrol Başladı. <br>";

    if ($xml === false) {
        echo "XML Cari Hareket Liste Silindi parse edilemedi.";
        return;
    }
    if (!isset($xml->table->row)) {
        echo "$newDate: Evrak Silindi Kontrol tamamlandı. <br>";
        return;
    }
    try {
        // Start a transaction
        $pdo->beginTransaction();
        // Prepare the update query once
        $updateQuery = "UPDATE uyeler_hareket SET SILINDI = 1 WHERE EVRAK_NO = :EVRAK_NO";
        $updateStmt = $pdo->prepare($updateQuery);

        foreach ($xml->table->row as $row) {
            $EVRAK_NO = $row->EVRAK_NO;

            $updateStmt->execute([':EVRAK_NO' => $EVRAK_NO]);
        }
        $pdo->commit();
        echo "$newDate: Evrak Silindi Kontrol tamamlandı. <br>";
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $pdo->rollBack();
        echo "$newDate: Evrak Silindi Kontrol tamamlandı. <br>";
    }
}
function getStockBLKODU(){
    echo "$newDate: Stok BLKODU Kontrol Başladı. <br>";
    $mysqli = connectToDatabase();
    $query = "SELECT BLKODU FROM nokta_urunler WHERE BLKODU IS NOT NULL AND BLKODU != '' AND BLKODU != '0'";

    $result = $mysqli->query($query);
    $BLKODU = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $BLKODU[] = $row['BLKODU'];
        }
    }
    print_r($BLKODU);
    return $BLKODU;
}



function getStockList($xmlData) {
    $mysqli = connectToDatabase();
    $xml = simplexml_load_string($xmlData);
    global $newDate;
    echo "$newDate: Fiyat Tarama Başladı. <br>";
    if ($xml === false) {
        echo "Failed to parse XML Fiyat Liste.";
        return;
    }
    if (!isset($xml->table->row)) {
        echo "$newDate: Fiyat Tarama Tamamlandı.<br>";
        return;
    }
    foreach ($xml->table->row as $row) {
        $BLKODU = (int)$row->BLKODU;
        $STOKKODU = $mysqli->real_escape_string((string)$row->STOKKODU);
        $DOVIZ_KULLAN = $mysqli->real_escape_string((string)$row->DOVIZ_KULLAN);
        $DOVIZ_BIRIMI = $mysqli->real_escape_string((string)$row->DOVIZ_BIRIMI);
        $KSF1 = $mysqli->real_escape_string((string)$row->KSF1);
        $KSF2 = $mysqli->real_escape_string((string)$row->KSF2);
        $KSF3 = $mysqli->real_escape_string((string)$row->KSF3);
        $KSF4 = $mysqli->real_escape_string((string)$row->KSF4);
        $DSF1 = $mysqli->real_escape_string((string)$row->DSF1);
        $DSF2 = $mysqli->real_escape_string((string)$row->DSF2);
        $DSF3 = $mysqli->real_escape_string((string)$row->DSF3);
        $DSF4 = $mysqli->real_escape_string((string)$row->DSF4);
        $KSF1 = gelenFiyatDuzenle($KSF1);
        $KSF2 = gelenFiyatDuzenle($KSF2);
        $KSF3 = gelenFiyatDuzenle($KSF3);
        $KSF4 = gelenFiyatDuzenle($KSF4);
        $DSF1 = gelenFiyatDuzenle($DSF1);
        $DSF2 = gelenFiyatDuzenle($DSF2);
        $DSF3 = gelenFiyatDuzenle($DSF3);
        $DSF4 = gelenFiyatDuzenle($DSF4);
        $DEGISTIRME_TARIHI = date('Y-m-d H:i:s', strtotime((string)$row->DEGISTIRME_TARIHI));
        $checkQuery = "SELECT * FROM nokta_urunler WHERE BLKODU = ?";
        $stmt = $mysqli->prepare($checkQuery);
        $stmt->bind_param("i", $BLKODU);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        $stmt->close();
        if ($checkResult->num_rows > 0) {
            $existingRow = $checkResult->fetch_assoc();
            $existingDegistirmeTarihi = $existingRow['DEGISTIRME_TARIHI'];
            if ($existingDegistirmeTarihi != $DEGISTIRME_TARIHI) {
                $updateQuery = "UPDATE nokta_urunler 
                                SET DOVIZ_KULLAN = ?, DOVIZ_BIRIMI = ?, KSF1 = ?, KSF2 = ?, KSF3 = ?,KSF4 = ?, DSF1 = ?, DSF2 = ?, DSF3 = ?, DSF4 = ?, DEGISTIRME_TARIHI = ?
                                WHERE BLKODU = ?";
                $stmt = $mysqli->prepare($updateQuery);
                $stmt->bind_param("sssssssssssi", $DOVIZ_KULLAN, $DOVIZ_BIRIMI, $KSF1, $KSF2, $KSF3, $KSF4, $DSF1, $DSF2, $DSF3, $DSF4, $DEGISTIRME_TARIHI, $BLKODU);
                $stmt->execute();
                $stmt->close();

                echo "$newDate: Fiyatı güncellenen stok kodu: $STOKKODU <br>";
            }
        }
    }
    $mysqli->close();
    echo "$newDate: Fiyat Tarama Tamamlandı. <br>";
}

$xml_data_stock_inventory = isset($_POST['xml_data_stok_envanter']) ? $_POST['xml_data_stok_envanter'] : '';
$xml_data_stock = isset($_POST['xml_data_stok_adet']) ? $_POST['xml_data_stok_adet'] : '';
$xml_data_stock_list = isset($_POST['xml_data_stok_liste']) ? $_POST['xml_data_stok_liste'] : '';
$xml_data_account_list = isset($_POST['xml_data_cari_liste']) ? $_POST['xml_data_cari_liste'] : '';
$xml_data_account_transaction_list = isset($_POST['xml_data_cari_hareket_liste']) ? $_POST['xml_data_cari_hareket_liste'] : '';
$xml_data_account_transaction_sil = isset($_POST['xml_data_cari_hareket_sil']) ? $_POST['xml_data_cari_hareket_sil'] : '';
$xml_data_account_balance_list = isset($_POST['xml_data_cari_bakiye_liste']) ? $_POST['xml_data_cari_bakiye_liste'] : '';
$xml_odeme_sorgula = isset($_POST['xml_odeme_sorgula']) ? $_POST['xml_odeme_sorgula'] : '';
$xml_cari_gonder = isset($_POST['xml_cari_gonder']) ? $_POST['xml_cari_gonder'] : '';
$xml_cari_blkodu = isset($_POST['xml_cari_blkodu']) ? $_POST['xml_cari_blkodu'] : '';
$xml_cari_kodu   = isset($_POST['xml_cari_kodu']) ? $_POST['xml_cari_kodu'] : '';
$xml_data_stok_BLKODU   = isset($_POST['xml_data_stok_BLKODU']) ? $_POST['xml_data_stok_BLKODU'] : '';


if (!empty($xml_odeme_sorgula)) { odemeGonder(); }
elseif (!empty($xml_cari_gonder)) { cariGonder(); }
elseif (!empty($xml_data_account_list)) { getAccountList($xml_data_account_list); }
elseif (!empty($xml_data_account_balance_list)) { getAccountBalanceList($xml_data_account_balance_list); }
elseif (!empty($xml_data_account_transaction_list)) { getAccountTransactionList($xml_data_account_transaction_list); }
elseif (!empty($xml_data_account_transaction_sil)) { getAccountTransactionSil($xml_data_account_transaction_sil); }
elseif (!empty($xml_data_stok_BLKODU)) { getStockBLKODU();}


elseif (!empty($xml_data_stock_list)) { getStockList($xml_data_stock_list);}


elseif (!empty($xml_data_stock_inventory)) {
    insertCategoriesFromDatabase();
    updateKategoriIDForAllProducts();
}
elseif (!empty($xml_data_stock)) { stokMiktar($xml_data_stock); }
?>