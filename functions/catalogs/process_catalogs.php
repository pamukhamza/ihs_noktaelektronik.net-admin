<?php
include_once '../db.php';
require '../../vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$config = require '../../aws-config.php';

if (!isset($config['s3']['region']) || !isset($config['s3']['key']) || !isset($config['s3']['secret']) || !isset($config['s3']['bucket'])) {
    die('Missing required S3 configuration values.');
}

$s3Client = new S3Client([
    'version' => 'latest',
    'region'  => $config['s3']['region'],
    'credentials' => [
        'key'    => $config['s3']['key'],
        'secret' => $config['s3']['secret'],
    ],
]);

$database = new Database();
$action = $_POST['action'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catalog_file = !empty($_FILES['catalog_file']['name']) ? $_FILES['catalog_file'] : null;
    $catalog_photo = !empty($_FILES['catalog_photo']['name']) ? $_FILES['catalog_photo'] : null;

    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $catalog_title = isset($_POST['catalog_title']) ? $_POST['catalog_title'] : null;
    $catalog_title_en = isset($_POST['catalog_title_en']) ? $_POST['catalog_title_en'] : null;

    if ($action == 'update') {
        $query = "UPDATE catalogs SET `title` = :catalog_title, `title_en` = :catalog_title_en" .
            (empty($catalog_file) ? "" : ", `file` = :file") .
            (empty($catalog_photo) ? "" : ", `img` = :img") .
            " WHERE id = :id";

        $params = [
            'catalog_title' => $catalog_title,
            'catalog_title_en' => $catalog_title_en,
            'id' => $id,
        ];

        if (!empty($catalog_file)) {
            $uploadDir = 'uploads/catalogs/';
            $unique_filename = uniqid() . '.' . pathinfo($catalog_file['name'], PATHINFO_EXTENSION);
            $uploadPath = $uploadDir . $unique_filename;

            if (move_uploaded_file($catalog_file['tmp_name'], $uploadPath)) {
                $params['file'] = $uploadPath;
            } else {
                echo "File upload failed.";
                exit;
            }
        }

        if (!empty($catalog_photo)) {
            $img = uploadImageToS3($catalog_photo, 'uploads/images/catalogs/', $s3Client, $config['s3']['bucket']);
            if ($img === false) {
                echo "Image upload failed.";
                exit;
            }
            $params['img'] = $img;
        }

        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }

    } elseif ($action == 'insert') {
        $query = "INSERT INTO catalogs (`title`, `title_en`" .
            (empty($catalog_file) ? "" : ", `file`") .
            (empty($catalog_photo) ? "" : ", `img`") .
            ") VALUES (:catalog_title, :catalog_title_en" .
            (empty($catalog_file) ? "" : ", :file") .
            (empty($catalog_photo) ? "" : ", :img") . ")";

        $params = [
            'catalog_title' => $catalog_title,
            'catalog_title_en' => $catalog_title_en,
        ];

        if (!empty($catalog_file)) {
            $uploadDir = 'uploads/catalogs/';
            $unique_filename = uniqid() . '.' . pathinfo($catalog_file['name'], PATHINFO_EXTENSION);
            $uploadPath = $uploadDir . $unique_filename;

            if (move_uploaded_file($catalog_file['tmp_name'], $uploadPath)) {
                $params['file'] = $uploadPath;
            } else {
                echo "File upload failed.";
                exit;
            }
        }

        if (!empty($catalog_photo)) {
            $img = uploadImageToS3($catalog_photo, 'uploads/images/catalogs/', $s3Client, $config['s3']['bucket']);
            if ($img === false) {
                echo "Image upload failed.";
                exit;
            }
            $params['img'] = $img;
        }

        if ($database->insert($query, $params)) {
            echo "Kayıt başarıyla eklendi.";
        } else {
            echo "Ekleme sırasında hata oluştu.";
        }
    }
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
            'SourceFile' => $file['tmp_name'],
            'ACL'    => 'public-read',
        ]);
        return $unique_filename; // Return the unique filename on success
    } catch (AwsException $e) {
        return false; // Return false on failure
    }
}
?>