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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : null;
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $catalog_title = isset($_POST['catalog_title']) ? $_POST['catalog_title'] : null;
    $catalog_site = isset($_POST['catalog_site']) ? $_POST['catalog_site'] : null;

    if ($action == 'update') {
        $query = "UPDATE catalogs SET `title` = :catalog_title, `site` = :catalog_site" . (empty($catalog_file) ? "" : ", `file` = :file") . " WHERE id = :id";

        $params = [
            'catalog_title' => $catalog_title,
            'catalog_site' => $catalog_site,
            'id' => $id,
        ];

        if (!empty($catalog_file)) {
            try {
                $result = $s3Client->putObject([
                    'Bucket' => $config['s3']['bucket'],
                    'Key'    => 'uploads/catalogs/' . basename($_FILES['catalog_file']['name']),
                    'SourceFile' => $_FILES['catalog_file']['tmp_name'],
                    'ACL'    => 'public-read', // Optional: make the file publicly accessible
                ]);

                $params['file'] = $result['ObjectURL'];
            } catch (AwsException $e) {
                echo "Error uploading file: " . $e->getMessage();
                exit;
            }
        }

        if ($database->update($query, $params)) {
            echo "Kayıt başarıyla güncellendi.";
        } else {
            echo "Güncelleme sırasında hata oluştu.";
        }
    } elseif ($action == 'insert') {
        $query = "INSERT INTO catalogs (`title`, `site`" . (empty($catalog_file) ? "" : ", `file`") . ") VALUES (:catalog_title, :catalog_site" . (empty($catalog_file) ? "" : ", :file") . ")";

        $params = [
            'catalog_title' => $catalog_title,
            'catalog_site' => $catalog_site,
        ];

        if (!empty($catalog_file)) {
            try {
                $result = $s3Client->putObject([
                    'Bucket' => $config['s3']['bucket'],
                    'Key'    => 'uploads/catalogs/' . basename($_FILES['catalog_file']['name']),
                    'SourceFile' => $_FILES['catalog_file']['tmp_name'],
                    'ACL'    => 'public-read', // Optional: make the file publicly accessible
                ]);

                $params['file'] = $result['ObjectURL'];
            } catch (AwsException $e) {
                echo "Error uploading file: " . $e->getMessage();
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