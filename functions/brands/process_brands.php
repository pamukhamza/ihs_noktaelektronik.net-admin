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

function generateSeoLink($string, $id) {
    $string = strtolower($string);
    $turkish = ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'İ', 'Ö', 'Ş', 'Ü'];
    $english = ['c', 'g', 'i', 'o', 's', 'u', 'c', 'g', 'i', 'o', 's', 'u'];
    $string = str_replace($turkish, $english, $string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s]+/', '-', $string);
    return trim($string, '-') . '-' . $id;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $name = $_POST['name'];
    $id = $_POST['id'];
    $cat_img = !empty($_FILES['cat_img']['name']) ? $_FILES['cat_img']['name'] : null;
    $seolink = generateSeoLink($name, $id);

    if ($action === 'insert' || $action === 'update') {
        $imgUrl = null;
        if (!empty($cat_img)) {
            $fileName = time() . '_' . basename($_FILES['cat_img']['name']);
            $targetFilePath = 'uploads/images/brands/' . $fileName;
            
            try {
                $result = $s3Client->putObject([
                    'Bucket' => $config['s3']['bucket'],
                    'Key'    => $targetFilePath,
                    'SourceFile' => $_FILES['cat_img']['tmp_name'],
                ]);
                $imgUrl = $result['ObjectURL'];
            } catch (AwsException $e) {
                die('Error uploading file: ' . $e->getMessage());
            }
        }

        if ($action === 'insert') {
            $query = "INSERT INTO nokta_urun_markalar (`title`,`seo_link" . (!empty($imgUrl) ? ", `hover_img`" : "") . ") VALUES (:name, :seo_link" . (!empty($imgUrl) ? ", :cat_img" : "") . ")";
            $params = [
                'name' => $name,
                'seo_link' => $seolink
            ];

            if (!empty($imgUrl)) {
                $params['cat_img'] = $imgUrl;
            }

            if ($database->insert($query, $params)) {
                echo "Kayıt başarıyla eklendi.";
            } else {
                echo "Ekleme sırasında hata oluştu.";
            }
        } elseif ($action === 'update') {
            $query = "UPDATE nokta_urun_markalar SET `title` = :name, `seo_link` = :seo_link" . (!empty($imgUrl) ? ", `hover_img` = :cat_img" : "") . " WHERE id = :id";
            $params = [
                'name' => $name,
                'seo_link' => $seolink,
                'id' => $id
            ];

            if (!empty($imgUrl)) {
                $params['cat_img'] = $imgUrl;
            }

            if ($database->update($query, $params)) {
                echo "Kayıt başarıyla güncellendi.";
            } else {
                echo "Güncelleme sırasında hata oluştu.";
            }
        }
    }
}
?>
