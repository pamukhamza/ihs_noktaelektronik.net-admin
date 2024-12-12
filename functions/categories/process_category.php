<?php
include_once '../../../functions/db.php';
include_once '../functions.php';
$database = new Database();

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $name = $_POST['name'];
    $name_cn = $_POST['name_cn'];
    $cat_img = !empty($_FILES['cat_img']['name']) ? $_FILES['cat_img']['name'] : null;

    // Language multiple select can be an array, convert to a string
    $cat_id = is_array($_POST['category']) ? implode(",", $_POST['category']) : $_POST['category'];
    if ($cat_id == 'null') {
        $cat_id = 0;
    }

    $seolink = generateSeoLink($name, $cat_id);

    if ($action === 'insert') {
        // Use quotes for reserved keyword
        $query = "INSERT INTO categories (`name`, `name_cn`, `seo_link`, `parent_id`" . (!empty($cat_img) ? ", `cat_img`" : "") . ") VALUES (:name, :name_cn, :seo_link, :parent_id" . (!empty($cat_img) ? ", :cat_img" : "") . ")";
        $params = [
            'name' => $name,
            'name_cn' => $name_cn,
            'seo_link' => $seolink,
            'parent_id' => $cat_id
        ];

        if (!empty($cat_img)) {
            $img = validateAndSaveImage($_FILES['cat_img'], '../../assets/images/category/');
            $params['cat_img'] = $img;
        }

        if ($database->insert($query, $params)) {
            echo "Kayıt başarıyla eklendi.";
        } else {
            echo "Ekleme sırasında hata oluştu.";
        }
    } elseif ($action === 'update') {
        $id = $_POST['id'];

        $seolink = generateSeoLink($name, $cat_id);

        // Use quotes for reserved keyword
        $query = "UPDATE categories SET `name` = :name, `name_cn` = :name_cn, `seo_link` = :seo_link, `parent_id` = :parent_id" . (!empty($cat_img) ? ", `cat_img` = :cat_img" : "") . " WHERE id = :id";
        $params = [
            'name' => $name,
            'name_cn' => $name_cn,
            'seo_link' => $seolink,
            'parent_id' => $cat_id,
            'id' => $id
        ];

        if (!empty($cat_img)) {
            $img = validateAndSaveImage($_FILES['cat_img'], '../../assets/images/category/');
            $params['cat_img'] = $img;
        }

        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }
    }
}
?>
