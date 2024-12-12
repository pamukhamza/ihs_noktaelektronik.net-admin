<?php
function deleteID($tableName, $id) {
    require 'db.php';
    $database = new Database();
    $query = "DELETE FROM $tableName WHERE id = :id";
    $result = $database->delete($query, ['id' => $id]);
    if ($result) {
        return "Record with ID $id deleted successfully from $tableName.";
    } else {
        return "Failed to delete record with ID $id from $tableName.";
    }
}
function deleteCategoryAndChildren($categoryId,$database) {
    // Alt kategorileri bul
    $query = "SELECT id FROM categories WHERE parent_id = :parent_id";
    $children = $database->fetchAll($query, ['parent_id' => $categoryId]);
    // Eğer alt kategoriler varsa, önce onları sil
    foreach ($children as $child) {
        deleteCategoryAndChildren($child['id'],$database); // Rekürsif olarak alt kategorileri sil
    }
    // Şimdi, ana kategoriyi sil
    $query = "DELETE FROM categories WHERE id = :id";
    return $database->delete($query, ['id' => $categoryId]);
}
if (isset($_POST['type']) && $_POST['type'] === 'delete') {
    $id = $_POST['id'];          // AJAX'tan gelen ID
    $tableName = $_POST['tablename'];  // AJAX'tan gelen tablo adı
    $type = $_POST['type'];      // AJAX'tan gelen işlem türü
    if (!empty($id)) {
        echo deleteID($tableName, $id);  // Silme fonksiyonu çağrılıyor
    } else {
        echo "ID değeri boş!";
    }
}elseif (isset($_POST['type']) && $_POST['type'] === 'deleteCat') {
    $id = $_POST['id'];
    require 'db.php';
    $database = new Database();
    if (!empty($id)) {
        // Kategoriyi ve alt kategorilerini sil
        if (deleteCategoryAndChildren($id,$database)) {
            echo "Kategori ve alt kategorileri başarıyla silindi.";
        } else {
            echo "Kategori silinemedi.";
        }
    } else {
        echo "ID değeri boş!";
    }
}elseif (isset($_POST['type']) && $_POST['type'] === 'deleteBrand') {
    $id = $_POST['id'];
    require 'db.php';
    $database = new Database();
    if (!empty($id)) {
        $query = "DELETE FROM brands WHERE id = :id";
        return $database->delete($query, ['id' => $id]);
    } else {
        echo "ID değeri boş!";
    }
}elseif (isset($_POST['type']) && $_POST['type'] === 'deleteFilterTitle') {
    $id = $_POST['id'];
    require 'db.php';
    $database = new Database();
    if (!empty($id)) {
        $query = "DELETE FROM filter_title WHERE id = :id";
        return $database->delete($query, ['id' => $id]);
    } else {
        echo "ID değeri boş!";
    }
}elseif (isset($_POST['type']) && $_POST['type'] === 'deleteFilter') {
    $id = $_POST['id'];
    require 'db.php';
    $database = new Database();
    if (!empty($id)) {
        $query = "DELETE FROM filter_value WHERE id = :id";
        return $database->delete($query, ['id' => $id]);
    } else {
        echo "ID değeri boş!";
    }
}
function validateAndSaveImage($file, $upload_path) {
    // Dosya Türü Doğrulama
    $allowedTypes = array('image/jpeg', 'image/png', 'image/gif');
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }

    // Maks. Dosya boyutu
    $max_file_size = 6 * 1024 * 1024; // 6MB in bytes
    if ($file["size"] > $max_file_size) {
        return false;
    }

    // Özel isim oluşturma
    $unique_filename = uniqid() . '.jpg'; // JPG uzantısı
    $uploadPath = $upload_path . $unique_filename;

    // Görüntüyü oluştur
    switch ($file['type']) {
        case 'image/jpeg':
        case 'image/jpg':
            $image = @imagecreatefromjpeg($file['tmp_name']);
            break;
        case 'image/png':
            $image = @imagecreatefrompng($file['tmp_name']);
            break;
        case 'image/gif':
            $image = @imagecreatefromgif($file['tmp_name']);
            break;
        default:
            return false; // Desteklenmeyen dosya türü
    }

    if (!$image) {
        return false; // Görüntü oluşturulamadı
    }

    // JPG olarak kaydet (kaliteyi ayarlayın, 0-100 arasında bir değer)
    $quality = 80; // Örneğin, %80 kalite
    if (imagejpeg($image, $uploadPath, $quality)) {
        imagedestroy($image); // Temizle
        return $unique_filename; // Başarılı ise dosya adını döndür
    }

    imagedestroy($image); // Temizle
    return false; // Kaydetme başarısız oldu
}
?>