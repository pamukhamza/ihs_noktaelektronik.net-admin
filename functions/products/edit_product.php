<?php
include_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    function generateSeoLink($string, $id) {
        $string = strtolower($string);
        $turkish = ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'İ', 'Ö', 'Ş', 'Ü'];
        $english = ['c', 'g', 'i', 'o', 's', 'u', 'c', 'g', 'i', 'o', 's', 'u'];
        $string = str_replace($turkish, $english, $string);
        $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
        $string = preg_replace('/[\s]+/', '-', $string);
        $string = trim($string, '-');
        return $string . '-' . $id;
    }

    $database = new Database();

    $id = $_POST['id'];
    $name = $_POST['name'];
    $BLKODU = $_POST['BLKODU'];
    $name_cn = $_POST['name_en'];
    $urun_kodu = $_POST['urun_kodu'];
    $barcode = $_POST['barkod'];
    $category = $_POST['category'];
    $brand = $_POST['brand'];
    $general = $_POST['ozellikler'];
    $general_cn = $_POST['ozellikler_en'];
    $technical = $_POST['teknik_ozellikler'];
    $technical_cn = $_POST['teknik_ozellikler_en'];
    $seolink = generateSeoLink($name, $id);
    if(isset($_POST['birlikteal'])){
        $birlikteal = $_POST['birlikteal'];
        $birliktealStr = implode(',', $birlikteal);
    }else{
        $birliktealStr = "";
    }
    if(isset($_POST['ikon'])){
        $ikon = $_POST['ikon'];
        $ikonStr = implode(',', $ikon);
    }else{
        $ikonStr = "";
    }
    try {
        // Ürün Güncelleme Sorgusu
        $updateQuery = "UPDATE nokta_urunler SET UrunAdiTR = :name ,UrunAdiEN = :name_en , UrunKodu = :UrunKodu ,
                barkod = :barkod , KategoriID = :category ,MarkaID = :brand ,OzelliklerTR = :OzelliklerTR ,
                OzelliklerEN = :OzelliklerEN ,BilgiTR = :BilgiTR , BilgiEN = :BilgiEN, seo_link = :seolink, birlikte_al = :birlikte_al, ikon = :ikon, BLKODU = :BLKODU 
                WHERE id = :id";

        $params = [
            'name' => $name,
            'name_en' => $name_cn,
            'UrunKodu' => $urun_kodu,
            'barkod' => $barcode,
            'category' => (int)$category,
            'brand' => $brand,
            'OzelliklerTR' => $general,
            'OzelliklerEN' => $general_cn,
            'BilgiTR' => $technical,
            'BilgiEN' => $technical_cn,
            'seolink' => $seolink,
            'birlikte_al' => $birliktealStr,
            'ikon' => $ikonStr,
            'BLKODU' => $BLKODU,
            'id' => $id
        ];

        $database->update($updateQuery, $params);

        // Kategori Hiyerarşisini Takip Ederek category_brand_rel Tablosuna Ekleme Yap
        $currentCategory = $category;
        while ($currentCategory) {
            // Mevcut kategori ve marka ID'sinin olup olmadığını kontrol et
            $checkQuery = "SELECT COUNT(*) FROM category_brand_rel WHERE marka_id = :brand AND kat_id = :category";
            $exists = $database->fetchColumn($checkQuery, ['brand' => $brand, 'category' => $currentCategory]);

            // Eğer kayıt yoksa ekle
            if (!$exists) {
                $insertQuery = "INSERT INTO category_brand_rel (marka_id, kat_id) VALUES (:brand, :category)";
                $database->insert($insertQuery, ['brand' => $brand, 'category' => $currentCategory]);
            }

            // Üst kategoriyi bul
            $parentQuery = "SELECT parent_id FROM nokta_kategoriler WHERE id = :category";
            $parentCategory = $database->fetchColumn($parentQuery, ['category' => $currentCategory]);

            // Parent ID sıfırsa döngüyü kır
            if (!$parentCategory || $parentCategory == 0) {
                break;
            }

            $currentCategory = $parentCategory;
        }
        
        // category_product_rel Güncelleme (Sadece Mevcut Kategori İçin)
        $checkProductCategory = "SELECT COUNT(*) FROM category_product_rel WHERE p_id = :p_id AND cat_id = :cat_id";
        $existsProductCategory = $database->fetchColumn($checkProductCategory, ['p_id' => $id, 'cat_id' => $category]);

        if (!$existsProductCategory) {
            $insertProductCategory = "INSERT INTO category_product_rel (p_id, cat_id) VALUES (:p_id, :cat_id)";
            $database->insert($insertProductCategory, ['p_id' => $id, 'cat_id' => $category]);
        }

        header("Location: ../../pages/genel/add-product.php?id=" . $id . "&s=1");
        exit;
    } catch (Exception $e) {
        header("Location: ../../pages/genel/add-product.php?id=" . $id . "&s=2&msg=" . htmlspecialchars($e->getMessage()));
        exit;
    }
}
?>
