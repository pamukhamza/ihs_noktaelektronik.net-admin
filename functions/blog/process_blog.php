<?php
include_once '../db.php';
include_once '../functions.php';
$database = new Database();

// SEO link fonksiyonu
function seolink($string) {
    $turkce = ['ş', 'Ş', 'ı', 'İ', 'ç', 'Ç', 'ü', 'Ü', 'ö', 'Ö', 'ğ', 'Ğ'];
    $duzgun = ['s', 'S', 'i', 'I', 'c', 'C', 'u', 'U', 'o', 'O', 'g', 'G'];
    $string = str_replace($turkce, $duzgun, $string);
    $string = strtolower($string); // Tüm harfleri küçük yap
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string); // Alfanümerik olmayan karakterleri kaldır
    $string = preg_replace('/\s+/', '-', $string); // Boşlukları tire ile değiştir
    $string = preg_replace('/-+/', '-', $string); // Çift tireleri tek tireye indir
    return trim($string, '-'); // Baş ve sondaki tireleri kaldır
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $title = $_POST['title'];
    $title_cn = $_POST['titleCn'];
    $text = $_POST['text'];
    $text_cn = $_POST['textCn'];
    $banner_img = !empty($_FILES['foto']['name']) ? $_FILES['foto']['name'] : null;

    // SEO link oluştur (ID olmadan)
    $seo_link = seolink($title);

    if ($action === 'insert') {
        $query = "INSERT INTO blog (`title`, `title_cn`, `text`, `text_cn`, `seo_link`" . (!empty($banner_img) ? ", `foto`" : "") . ") VALUES (:title, :title_cn, :text, :text_cn, :seo_link" . (!empty($banner_img) ? ", :foto" : "") . ")";
        $params = [
            'title' => $title,
            'title_cn' => $title_cn,
            'text' => $text,
            'text_cn' => $text_cn,
            'seo_link' => $seo_link
        ];
        if (!empty($banner_img)) {
            $img = validateAndSaveImage($_FILES['foto'], '../../assets/images/blog/');
            $params['foto'] = $img;
        }
        if ($database->insert($query, $params)) {
            // En son eklenen ID'yi al
            $lastId = $database->lastInsertId();
            // SEO linkin sonuna ID'yi ekle
            $seo_link_with_id = $seo_link . '-' . $lastId;
            $updateQuery = "UPDATE blog SET seo_link = :seo_link WHERE id = :id";
            $updateParams = ['seo_link' => $seo_link_with_id, 'id' => $lastId];
            $database->update($updateQuery, $updateParams);
            echo "Kayıt başarıyla eklendi.";
        } else {
            echo "Ekleme sırasında hata oluştu.";
        }
    } elseif ($action === 'update') {
        $id = $_POST['id'];

        // SEO linkin sonuna ID'yi ekle
        $seo_link_with_id = $seo_link . '-' . $id;

        $query = "UPDATE blog SET `title` = :title, `title_cn` = :title_cn, `text` = :text, `text_cn` = :text_cn, `seo_link` = :seo_link" . (!empty($banner_img) ? ", `foto` = :foto" : "") . " WHERE id = :id";
        $params = [
            'title' => $title,
            'title_cn' => $title_cn,
            'text' => $text,
            'text_cn' => $text_cn,
            'seo_link' => $seo_link_with_id,
            'id' => $id
        ];

        if (!empty($banner_img)) {
            $img = validateAndSaveImage($_FILES['foto'], '../../assets/images/blog/');
            $params['foto'] = $img;
        }

        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }
    }
}
