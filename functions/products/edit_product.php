<?php
include_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    function generateSeoLink($string, $id) {
        // Büyük harfleri küçük harfe çevir
        $string = strtolower($string);

        // Türkçe karakterleri İngilizce karşılıklarına çevir
        $turkish = ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'İ', 'Ö', 'Ş', 'Ü'];
        $english = ['c', 'g', 'i', 'o', 's', 'u', 'c', 'g', 'i', 'o', 's', 'u'];
        $string = str_replace($turkish, $english, $string);

        // Özel karakterleri kaldır
        $string = preg_replace('/[^a-z0-9\s-]/', '', $string);

        // Boşlukları ve alt çizgileri - ile değiştir
        $string = preg_replace('/[\s]+/', '-', $string);

        // Kenar boşluklarını temizle
        $string = trim($string, '-');

        // ID'yi ekle
        return $string . '-' . $id;
    }

    // Veritabanı nesnesini oluştur
    $database = new Database();

    // Formdan gelen verileri al
    $id = $_POST['id'];
    $name = $_POST['name'];
    $name_cn = $_POST['name_cn'];
    $sku = $_POST['sku'];
    $barcode = $_POST['barcode'];
    $category = $_POST['category'];
    $brand = $_POST['brand'];
    $general = $_POST['general'];
    $general_cn = $_POST['general_cn'];
    $technical = $_POST['technical'];
    $technical_cn = $_POST['technical_cn'];
    $seolink = generateSeoLink($name, $id);
    // Güncelleme sorgusu ve parametreleri
    try {
        // Tüm alanları güncellemek için sorguları çalıştır
        $updateQueries = [
            "UPDATE products SET name = :name ,name_cn = :name_cn ,SKU = :sku ,barcode = :barcode ,category = :category ,brand = :brand ,general = :general ,general_cn = :general_cn 
                ,technical = :technical , technical_cn = :technical_cn, seo_link = :seolink WHERE id = :id"
        ];

        // Parametreleri dizi olarak tanımla
        $params = [
            'name' => $name,
            'name_cn' => $name_cn,
            'sku' => $sku,
            'barcode' => $barcode,
            'category' => (int)$category,
            'brand' => $brand,
            'general' => $general,
            'general_cn' => $general_cn,
            'technical' => $technical,
            'technical_cn' => $technical_cn,
            'seolink' => $seolink,
            'id' => $id
        ];

        // Her bir sorguyu döngü ile çalıştır
        foreach ($updateQueries as $query) {
            // Sorguyu çalıştır
            $database->update($query, $params);
        }

        header("Location: ../../pages/add-product.php?id=" . $id . "&s=1") ;
        exit;
    } catch (Exception $e) {
        header("Location: ../../pages/add-product.php?id=" . $id . "&s=2&msg=". htmlspecialchars($e->getMessage())) ;
        exit;
    }
}
?>
