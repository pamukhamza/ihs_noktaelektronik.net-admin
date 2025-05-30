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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];
    $banner_link1 = $_POST['banner_link1'];
    $banner_link2 = $_POST['banner_link2'];
    $banner_link3 = $_POST['banner_link3'];
    $banner_photo1 = !empty($_FILES['banner_photo1']['name']) ? $_FILES['banner_photo1']['name'] : null;
    $banner_photo2 = !empty($_FILES['banner_photo2']['name']) ? $_FILES['banner_photo2']['name'] : null;
    $banner_photo3 = !empty($_FILES['banner_photo3']['name']) ? $_FILES['banner_photo3']['name'] : null;

    if ($action === 'update') {
        $query = "UPDATE banner_modal SET link1 = :link1, link2 = :link2, link3 = :link3";

        if (!empty($banner_photo1)) {
            $query .= ", foto1 = :banner_photo1";
        }if (!empty($banner_photo2)) {
            $query .= ", foto2 = :banner_photo2";
        }if (!empty($banner_photo3)) {
            $query .= ", foto3 = :banner_photo3";
        }
        
        $query .= " WHERE id = :id";
        $params = ['link1' => $banner_link1, 'link2' => $banner_link2, 'link3' => $banner_link3, 'id' => $id];

        if (!empty($banner_photo1)) {
            $img = uploadImageToS3($_FILES['banner_photo1'], 'uploads/images/banner/', $s3Client, $config['s3']['bucket']);
            if ($img === false) {
                echo "Image upload failed.";
                exit;
            }
            $params['banner_photo1'] = $img;
        }
        if (!empty($banner_photo2)) {
            $img = uploadImageToS3($_FILES['banner_photo2'], 'uploads/images/banner/', $s3Client, $config['s3']['bucket']);
            if ($img === false) {
                echo "Image upload failed.";
                exit;
            }
            $params['banner_photo2'] = $img;
        }
        if (!empty($banner_photo3)) {
            $img = uploadImageToS3($_FILES['banner_photo3'], 'uploads/images/banner/', $s3Client, $config['s3']['bucket']);
            if ($img === false) {
                echo "Image upload failed.";
                exit;
            }
            $params['banner_photo3'] = $img;
        }

        $database->update($query, $params);
        echo "Banner modal başarıyla güncellendi!";
    }
}
?>