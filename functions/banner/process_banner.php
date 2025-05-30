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
    $id = $_POST['id'];
    $banner_link = $_POST['banner_link'];
    $banner_img = !empty($_FILES['banner_img']['name']) ? $_FILES['banner_img']['name'] : null;

    if($action == 'update'){
        $query = "UPDATE b2b_banner SET `banner_link` = :banner_link" . (empty($banner_img) ? "" : ", `banner_foto` = :banner_photo") . " WHERE id = :id";

        $params = [
            'banner_link' => $banner_link,
            'id' => $id,
        ];

        if (!empty($banner_img)) {
            $img = uploadImageToS3($_FILES['banner_img'], 'uploads/images/banner/', $s3Client, $config['s3']['bucket']);
            if ($img === false) {
                echo "Image upload failed.";
                exit;
            }
            $params['banner_photo'] = $img;
        }

        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }
    }
}
?>