<?php
include_once '../db.php'; // Veritabanı bağlantısını include et
$database = new Database(); // Database nesnesi oluştur

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action']; // Aksiyon (insert veya update)
    $name = $_POST['name']; // Başlık adı
    $nameEn = $_POST['nameEn']; // Başlık adı (İngilizce)
    $id = $_POST['id']; // ID
    $categoryId = $_POST['category_id']; // Kategori ID'si

    if ($action === 'insert') {
        // filter_title için insert işlemi
        $query = "INSERT INTO filter_title (`title`, `title_en`) VALUES (:name, :nameEn)";
        $params = [
            'name' => $name,
            'nameEn' => $nameEn
        ];

        if ($database->insert($query, $params)) {
            $filterTitleId = $database->lastInsertId();  // Son eklenen filter_title_id

            // category_filter_rel tablosuna yeni ilişki ekle
            if ($categoryId) {
                $queryCategory = "INSERT INTO category_filter_rel (filter_title_id, category_id) VALUES (:filterTitleId, :categoryId)";
                $paramsCategory = [
                    'filterTitleId' => $filterTitleId,
                    'categoryId' => $categoryId
                ];
                $database->insert($queryCategory, $paramsCategory);
            }

            echo "Kayıt başarıyla eklendi.";
        } else {
            echo "Ekleme sırasında hata oluştu.";
        }
    } elseif ($action === 'update') {
        // filter_title için update işlemi
        $query = "UPDATE filter_title SET `title` = :name, `title_en` = :nameEn WHERE id = :id";
        $params = [
            'name' => $name,
            'nameEn' => $nameEn,
            'id' => $id
        ];

        if ($database->update($query, $params)) {
            // category_filter_rel tablosundaki filter_title_id'yi kontrol et
            $queryCategoryRel = "SELECT * FROM category_filter_rel WHERE filter_title_id = :id";
            $paramsCategoryRel = ['id' => $id];
            $existingCategoryRel = $database->fetch($queryCategoryRel, $paramsCategoryRel);

            if ($existingCategoryRel) {
                // Eğer mevcutsa, güncelleme işlemi yap
                $queryUpdateCategory = "UPDATE category_filter_rel SET category_id = :categoryId WHERE filter_title_id = :id";
                $paramsUpdateCategory = [
                    'categoryId' => $categoryId,
                    'id' => $id
                ];
                $database->update($queryUpdateCategory, $paramsUpdateCategory);
            } else {
                // Eğer mevcut değilse, yeni ilişki ekle
                if ($categoryId) {
                    $queryInsertCategory = "INSERT INTO category_filter_rel (filter_title_id, category_id) VALUES (:filterTitleId, :categoryId)";
                    $paramsInsertCategory = [
                        'filterTitleId' => $id,
                        'categoryId' => $categoryId
                    ];
                    $database->insert($queryInsertCategory, $paramsInsertCategory);
                }
            }

            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }
    }
}
?>
