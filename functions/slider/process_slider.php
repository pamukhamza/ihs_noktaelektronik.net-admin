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
$site = $_POST['slider_site'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $slider_link = $_POST['slider_link'];
    $slider_img = !empty($_FILES['slider_img']['name']) ? $_FILES['slider_img']['name'] : null;

    if($action == 'update'){
        $query = "UPDATE slider SET `site` = :site, `slider_link` = :slider_link" . (empty($slider_img) ? "" : ", `slider_photo` = :slider_photo") . " WHERE id = :id";

        $params = [
            'site' => $site,
            'slider_link' => $slider_link,
            'id' => $id,
        ];

        if (!empty($slider_img)) {
            $img = uploadImageToS3($_FILES['slider_img'], 'uploads/images/slider/', $s3Client, $config['s3']['bucket']);
            if ($img === false) {
                echo "Image upload failed.";
                exit;
            }
            $params['slider_photo'] = $img;
        }

        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }

    }elseif($action == 'insert'){
        $query = "INSERT INTO slider (`slider_link`, `site`" . (empty($slider_img) ? "" : ", `slider_photo`") . ") VALUES (:slider_link, :site" . (empty($slider_img) ? "" : ", :slider_photo") . ")";

        $params = [
            'slider_link' => $slider_link,
            'site' => $site,
        ];

        if (!empty($slider_img)) {
            $img = uploadImageToS3($_FILES['slider_img'], 'uploads/images/slider/', $s3Client, $config['s3']['bucket']);
            if ($img === false) {
                echo "Image upload failed.";
                exit;
            }
            $params['slider_photo'] = $img;
        }

        if ($database->insert($query, $params)) {
            echo "Kayıt başarıyla eklendi.";
        } else {
            echo "Ekleme sırasında hata oluştu.";
        }
    }


}


?>
