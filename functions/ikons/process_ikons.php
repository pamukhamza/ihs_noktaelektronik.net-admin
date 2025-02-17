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
    $ikon_photo = !empty($_FILES['ikon_photo']['name']) ? $_FILES['ikon_photo']['name'] : null;

    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $ikon_title = isset($_POST['ikon_title']) ? $_POST['ikon_title'] : null;

    if ($action == 'update') {
        $query = "UPDATE nokta_urunler_ikonlar SET `title` = :ikon_title " .
            (empty($ikon_photo) ? "" : ", `img` = :img") .
            " WHERE id = :id";

        $params = [
            'ikon_title' => $ikon_title,
            'id' => $id,
        ];

        if (!empty($ikon_photo)) {
            $img = uploadImageToS3($_FILES['ikon_photo'], 'uploads/images/ikons/', $s3Client, $config['s3']['bucket']);
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
        $query = "INSERT INTO nokta_urunler_ikonlar (`title`" .
            (empty($ikon_photo) ? "" : ", `img`") .
            ") VALUES (:ikon_title" .
            (empty($ikon_photo) ? "" : ", :img") . ")";

        $params = [
            'ikon_title' => $ikon_title,
        ];

        if (!empty($ikon_photo)) {
            $img = uploadImageToS3($_FILES['ikon_photo'], 'uploads/images/ikons/', $s3Client, $config['s3']['bucket']);
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
            'SourceFile' => $file['tmp_name']
        ]);
        return $unique_filename; // Return the unique filename on success
    } catch (AwsException $e) {
        return false; // Return false on failure
    }
}
?>