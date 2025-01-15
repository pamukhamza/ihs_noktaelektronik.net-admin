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
    ],
]);

$database = new Database();

function generateSeoLink($string, $id) {
    // Convert to lowercase
    $string = strtolower($string);

    // Replace Turkish characters with English equivalents
    $turkish = ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'İ', 'Ö', 'Ş', 'Ü'];
    $english = ['c', 'g', 'i', 'o', 's', 'u', 'c', 'g', 'i', 'o', 's', 'u'];
    $string = str_replace($turkish, $english, $string);

    // Remove special characters
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);

    // Replace spaces and underscores with hyphens
    $string = preg_replace('/[\s]+/', '-', $string);

    // Trim hyphens from the ends
    $string = trim($string, '-');

    // Add ID
    return $string . '-' . $id;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $name = $_POST['name'];
    $name_cn = $_POST['name_cn'];
    $cat_img = !empty($_FILES['cat_img']['name']) ? $_FILES['cat_img']['name'] : null;

    // Language multiple select can be an array, convert to a string
    $cat_id = is_array($_POST['category']) ? implode(",", $_POST['category']) : $_POST['category'];
    if ($cat_id == 'null') {
        $cat_id = 0;
    }

    $seolink = generateSeoLink($name, $cat_id);

    if ($action === 'insert') {
        // Use quotes for reserved keyword
        $query = "INSERT INTO nokta_kategoriler (`KategoriAdiTr`, `KategoriAdiEn`, `seo_link`, `parent_id`" . (!empty($cat_img) ? ", `cat_img`" : "") . ") VALUES (:name, :name_cn, :seo_link, :parent_id" . (!empty($cat_img) ? ", :cat_img" : "") . ")";
        $params = [
            'name' => $name,
            'name_cn' => $name_cn,
            'seo_link' => $seolink,
            'parent_id' => $cat_id
        ];

        if (!empty($cat_img)) {
            try {
                $fileName = time() . '_' . basename($_FILES['cat_img']['name']);
                $targetFilePath = 'uploads/images/categories/' . $fileName;

                $result = $s3Client->putObject([
                    'Bucket' => $config['s3']['bucket'],
                    'Key'    => $targetFilePath,
                    'SourceFile' => $_FILES['cat_img']['tmp_name'],
                    'ACL'    => 'public-read', // Optional: make the file publicly accessible
                ]);

                $params['cat_img'] = $result['ObjectURL'];
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
    } elseif ($action === 'update') {
        $id = $_POST['id'];

        $seolink = generateSeoLink($name, $cat_id);

        // Use quotes for reserved keyword
        $query = "UPDATE nokta_kategoriler SET `KategoriAdiTr` = :name, `KategoriAdiEn` = :name_cn, `seo_link` = :seo_link, `parent_id` = :parent_id" . (!empty($cat_img) ? ", `cat_img` = :cat_img" : "") . " WHERE id = :id";
        $params = [
            'name' => $name,
            'name_cn' => $name_cn,
            'seo_link' => $seolink,
            'parent_id' => $cat_id,
            'id' => $id
        ];

        if (!empty($cat_img)) {
            try {
                $fileName = time() . '_' . basename($_FILES['cat_img']['name']);
                $targetFilePath = 'uploads/images/categories/' . $fileName;

                $result = $s3Client->putObject([
                    'Bucket' => $config['s3']['bucket'],
                    'Key'    => $targetFilePath,
                    'SourceFile' => $_FILES['cat_img']['tmp_name'],
                    'ACL'    => 'public-read', // Optional: make the file publicly accessible
                ]);

                $params['cat_img'] = $result['ObjectURL'];
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
    }
}
?>
