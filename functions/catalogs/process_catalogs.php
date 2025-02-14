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

$database = new Database();
$action = $_POST['action'];

$s3Client = new S3Client([
    'version' => 'latest',
    'region'  => $config['s3']['region'],
    'credentials' => [
        'key'    => $config['s3']['key'],
        'secret' => $config['s3']['secret'],
    ],
]);

$catalog_file = !empty($_FILES['catalog_file']['name']) ? $_FILES['catalog_file']['name'] : null;
$catalog_photo = !empty($_FILES['catalog_photo']['name']) ? $_FILES['catalog_photo']['name'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : null;
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
            try {
                $result = $s3Client->putObject([
                    'Bucket' => $config['s3']['bucket'],
                    'Key'    => 'uploads/catalogs/' . basename($_FILES['catalog_file']['name']),
                    'SourceFile' => $_FILES['catalog_file']['tmp_name']
                ]);
                $params['file'] = $result['ObjectURL'];
            } catch (AwsException $e) {
                echo "Error uploading catalog file: " . $e->getMessage();
                exit;
            }
        }

        if (!empty($catalog_photo)) {
            try {
                $result = $s3Client->putObject([
                    'Bucket' => $config['s3']['bucket'],
                    'Key'    => 'uploads/images/catalogs/' . basename($_FILES['catalog_photo']['name']),
                    'SourceFile' => $_FILES['catalog_photo']['tmp_name'],
                ]);
                $params['img'] = $result['ObjectURL'];
            } catch (AwsException $e) {
                echo "Error uploading catalog photo: " . $e->getMessage();
                exit;
            }
        }

        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }

    } elseif ($action == 'insert') {
        $query = "INSERT INTO catalogs (`title`, `title_en" .
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
            try {
                $result = $s3Client->putObject([
                    'Bucket' => $config['s3']['bucket'],
                    'Key'    => 'uploads/catalogs/' . basename($_FILES['catalog_file']['name']),
                    'SourceFile' => $_FILES['catalog_file']['tmp_name']
                ]);
                $params['file'] = $result['ObjectURL'];
            } catch (AwsException $e) {
                echo "Error uploading catalog file: " . $e->getMessage();
                exit;
            }
        }

        if (!empty($catalog_photo)) {
            try {
                $result = $s3Client->putObject([
                    'Bucket' => $config['s3']['bucket'],
                    'Key'    => 'uploads/images/catalogs/' . basename($_FILES['catalog_photo']['name']),
                    'SourceFile' => $_FILES['catalog_photo']['tmp_name']
                ]);
                $params['img'] = $result['ObjectURL'];
            } catch (AwsException $e) {
                echo "Error uploading catalog photo: " . $e->getMessage();
                exit;
            }
        }

        if ($database->insert($query, $params)) {
            echo "Kayıt başarıyla eklendi.";
        } else {
            echo "Ekleme sırasında hata oluştu.";
        }
    }
}
?>