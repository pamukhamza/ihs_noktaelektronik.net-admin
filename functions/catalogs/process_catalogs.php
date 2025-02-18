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
$action = $_POST['action'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catalog_title = $_POST['catalog_title'];
    $catalog_title_en = $_POST['catalog_title_en'];
    $id = $_POST['id'];

    $catalog_photo = !empty($_FILES['catalog_photo']['name']) ? $_FILES['catalog_photo']['name'] : null;
    $catalog_file = !empty($_FILES['catalog_file']['name']) ? $_FILES['catalog_file']['name'] : null;

    if($action == 'update'){
        $query = "UPDATE catalogs SET `title` = :catalog_title, `title_en` = :catalog_title_en" . (empty($catalog_photo) ? "" : ", `img` = :img") .  (empty($catalog_file) ? "" : ", `file` = :file") . " WHERE id = :id";

        $params = [
            'catalog_title' => $catalog_title,
            'catalog_title_en' => $catalog_title_en,
            'id' => $id,
        ];

        if (!empty($catalog_photo)) {
            $img = uploadImageToS3($_FILES['catalog_photo'], 'uploads/images/catalogs/', $s3Client, $config['s3']['bucket']);
            if ($img === false) {
                echo "Image upload failed.";
                exit;
            }
            $params['img'] = $img;
        }
        if (!empty($catalog_file)) {
            $file = uploadImageToS3($_FILES['catalog_file'], 'uploads/catalogs/', $s3Client, $config['s3']['bucket']);
            if ($file === false) {
                echo "File upload failed.";
                exit;
            }
            $params['file'] = $file;
        }

        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }

    }elseif($action == 'insert'){
        $query = "INSERT INTO catalogs (`title`, `title_en`" . (empty($catalog_photo) ? "" : ", `img`") .(empty($catalog_file) ? "" : ", `file`") . ") VALUES (:title, :title_en" . (empty($catalog_photo) ? "" : ", :img") .(empty($catalog_file) ? "" : ", :file") . ")";

        $params = [
            'title' => $catalog_title,
            'title_en' => $catalog_title_en,
        ];

        if (!empty($catalog_photo)) {
            $img = uploadImageToS3($_FILES['catalog_photo'], 'uploads/images/catalogs/', $s3Client, $config['s3']['bucket']);
            if ($img === false) {
                echo "Image upload failed.";
                exit;
            }
            $params['img'] = $img;
        }
        if (!empty($catalog_file)) {
            $file = uploadImageToS3($_FILES['catalog_file'], 'uploads/catalogs/', $s3Client, $config['s3']['bucket']);
            if ($file === false) {
                echo "File upload failed.";
                exit;
            }
            $params['file'] = $file;
        }

        if ($database->insert($query, $params)) {
            echo "Kayıt başarıyla eklendi.";
        } else {
            echo "Ekleme sırasında hata oluştu.";
        }
    }


}


?>
