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

    // Handle file uploads
    $banner_photo1 = "";
    $banner_photo2 = "";
    $banner_photo3 = "";

    // Process photo 1
    if (isset($_FILES['banner_img1']) && $_FILES['banner_img1']['error'] === UPLOAD_ERR_OK) {
        $img = uploadImageToS3($_FILES['banner_img1'], 'uploads/images/banner/', $s3Client, $config['s3']['bucket']);
        if ($img === false) {
            echo "Image 1 upload failed.";
            exit;
        }
        $banner_photo1 = $img;
    }

    // Process photo 2
    if (isset($_FILES['banner_img2']) && $_FILES['banner_img2']['error'] === UPLOAD_ERR_OK) {
        $img = uploadImageToS3($_FILES['banner_img2'], 'uploads/images/banner/', $s3Client, $config['s3']['bucket']);
        if ($img === false) {
            echo "Image 2 upload failed.";
            exit;
        }
        $banner_photo2 = $img;
    }

    // Process photo 3
    if (isset($_FILES['banner_img3']) && $_FILES['banner_img3']['error'] === UPLOAD_ERR_OK) {
        $img = uploadImageToS3($_FILES['banner_img3'], 'uploads/images/banner/', $s3Client, $config['s3']['bucket']);
        if ($img === false) {
            echo "Image 3 upload failed.";
            exit;
        }
        $banner_photo3 = $img;
    }

    if ($action === 'update') {
        // Build the update query
        $query = "UPDATE banner_modal SET link1 = :link1 , link2 = :link2, link3 = :link3";
        $params = ['link1' => $banner_link1, 'limk2' => $banner_link2, 'link3' => $banner_link3];

        // Add photo updates if new photos were uploaded
        if ($banner_photo1 !== "") {
            $query .= ", foto1 = :foto1";
            $params[foto1] = $banner_photo1;
        }
        if ($banner_photo2 !== "") {
            $query .= ", foto2 = :foto2";
            $params[foto2] = $banner_photo2;
        }
        if ($banner_photo3 !== "") {
            $query .= ", foto3 = :foto3";
            $params[foto3] = $banner_photo3;
        }

        $query .= " WHERE id = :id";
        $params[id] = $id;

        $database->query($query, $params);
        echo "Banner modal başarıyla güncellendi!";
    }
}
?> 