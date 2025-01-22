<?php
include_once '../db.php';
include_once '../functions.php';
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
    ]
]);

$database = new Database();
$type = $_POST['type'];
$action = $_POST['action'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $type === 'kvkk') {
    $id = $_POST['id'];
    $slider_title = $_POST['slider_title'];
    $slider_text = $_POST['slider_text'];
    $slider_img = !empty($_FILES['slider_photo']['name']) ? $_FILES['slider_photo']['name'] : null;

    if ($action == 'update') {
        $query = "UPDATE indata_projects SET `p_name` = :p_name, `p_desc` = :p_desc" . (empty($slider_img) ? "" : ", `p_image` = :p_image") . " WHERE id = :id";

        $params = [
            'p_name' => $slider_title,
            'p_desc' => $slider_text,
            'id' => $id,
        ];

        if (!empty($slider_img)) {
            $img = uploadImageToS3($_FILES['slider_photo'], 'uploads/images/indata_projeler/', $s3Client, $config['s3']['bucket']);
            if ($img === false) {
                echo "Image upload failed.";
                exit;
            }
            $params['p_image'] = $img;
        }

        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }
    } elseif ($action == 'insert') {
        $query = "INSERT INTO indata_projects (`p_name`, `p_desc`" . (empty($slider_img) ? "" : ", `p_image`") . ") VALUES (:p_name, :p_desc" . (empty($slider_img) ? "" : ", :p_image") . ")";

        $params = [
            'p_name' => $slider_title,
            'p_desc' => $slider_text
        ];

        if (!empty($slider_img)) {
            $img = uploadImageToS3($_FILES['slider_photo'], 'uploads/images/indata_projeler/', $s3Client, $config['s3']['bucket']);
            if ($img === false) {
                echo "Image upload failed.";
                exit;
            }
            $params['p_image'] = $img;
        }

        if ($database->insert($query, $params)) {
            echo "Kayıt başarıyla eklendi.";
        } else {
            echo "Ekleme sırasında hata oluştu.";
        }
    }
}

function uploadImageToS3($file, $upload_path, $s3Client, $bucket) {
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