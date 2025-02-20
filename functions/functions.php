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
    $query = "SELECT id FROM nokta_kategoriler WHERE parent_id = :parent_id";
    $children = $database->fetchAll($query, ['parent_id' => $categoryId]);
    // Eğer alt kategoriler varsa, önce onları sil
    foreach ($children as $child) {
        deleteCategoryAndChildren($child['id'],$database); // Rekürsif olarak alt kategorileri sil
    }
    // Şimdi, ana kategoriyi sil
    $query = "DELETE FROM nokta_kategoriler WHERE id = :id";
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
        $query = "DELETE FROM nokta_urun_markalar WHERE id = :id";
        return $database->delete($query, ['id' => $id]);
    } else {
        echo "ID değeri boş!";
    }
}elseif (isset($_POST['type']) && $_POST['type'] === 'deleteProduct') {
    $id = $_POST['id'];
    require 'db.php';
    $database = new Database();
    if (!empty($id)) {
        $query = "DELETE FROM nokta_urunler WHERE id = :id";
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
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Dosya Türü Doğrulama
    $allowedTypes = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp');
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }

    // Maks. Dosya boyutu
    $max_file_size = 6 * 1024 * 1024; // 6MB in bytes
    if ($file["size"] > $max_file_size) {
        return false;
    }

    // Get the original file extension
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);

    // Generate a unique filename with the original extension
    $unique_filename = uniqid() . '.' . $fileExtension;
    $uploadPath = $upload_path . $unique_filename;

    // Move the uploaded file to the target directory
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return $unique_filename; // Return the unique filename on success
    }

    return false; // Return false on failure
}
function uploadImageToS3($file, $upload_path, $s3Client, $bucket) {
    

    // Maks. Dosya boyutu
    $max_file_size = 6 * 1024 * 1024; // 6MB in bytes
    if ($file["size"] > $max_file_size) {
        return false;
    }

    // Get the original file extension
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);

    // Generate a unique filename with the original extension
    $unique_filename = uniqid() . '.' . $fileExtension;
    $uploadPath = $upload_path . $unique_filename;

    try {
        $result = $s3Client->putObject([
            'Bucket' => $bucket,
            'Key'    => $uploadPath,
            'SourceFile' => $file['tmp_name']
        ]);
        return $unique_filename; // Return the unique filename on success
    } catch (AwsException $e) {
        return false; // Return false on failure
    }
}
function WEB4UniqueOrderNumber() {
    $prefix = 'WEB';
    $datePart = date('YmdHi');
    $randomPart = mt_rand(1000, 9999);
    $orderNumber = $prefix . $datePart . $randomPart;
    return $orderNumber;
}
?>