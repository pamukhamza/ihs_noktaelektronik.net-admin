<?php
include_once '../db.php';
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


if (isset($_POST["urun_dosya_ekle"])) {
    $urun_id = $_POST['id'];
    $dosya_turu = $_POST['dosya_turu'];
    $dosya_aciklama = $_POST['dosya_aciklama'];
    $dosya_aciklama_EN = $_POST['dosya_aciklama_EN'];
    $dosya_versiyon = $_POST['dosya_versiyon'];
    $dosya_tarihi = $_POST['dosya_tarihi'];

    // Dosya yüklemesi için gerekli kontrolleri yap
    $dosya = $_FILES['dosya']['name'];

    $ext = pathinfo($dosya, PATHINFO_EXTENSION);

    $query_select = $db->prepare("SELECT dosya_yolu FROM nokta_yuklemeler WHERE id = :id");
    $query_select->execute(array("id" => $dosya_turu));
    $result = $query_select->fetch();

    $dosya_yolu = $result["dosya_yolu"];

    $fileName = time() . '_' . basename($_FILES['dosya']['name']);
    $targetFilePath = $dosya_yolu.'/' . $fileName;
    $query = "INSERT INTO nokta_urunler_yuklemeler (`aciklama`, `aciklamaEn`, `url_path`, `yukleme_id`, `is_active`, `version`,
     `type`, `datetime`, `urun_id`," . (!empty($dosya) ? ", `dosya_adi`" : "") . ") 
     VALUES (:d1, :d2, :d3, :d4, :d5, :d6, :d7, :d8, :d9" . (!empty($dosya) ? ", :d10" : "") . ")";
    $params = [
        "d1" => $dosya_aciklama,
        "d2" => $dosya_aciklama_EN,
        "d3" => $targetFilePath,
        "d4" => $dosya_turu,
        "d5" => 1,
        "d6" => $dosya_versiyon,
        "d7" => $ext,
        "d8" => $dosya_tarihi,
        "d9" => $urun_id
    ];

    if (!empty($dosya)) {
        try {
            $fileName = time() . '_' . basename($_FILES['dosya']['name']);
            $targetFilePath = $dosya_yolu.'/' . $fileName;

            $result = $s3Client->putObject([
                'Bucket' => $config['s3']['bucket'],
                'Key'    => $targetFilePath,
                'SourceFile' => $_FILES['dosya']['tmp_name'],
            ]);

            $params['d10'] = $fileName;
        } catch (AwsException $e) {
            echo "Error uploading file: " . $e->getMessage();
            exit;
        }
    }

    if ($database->insert($query, $params)) {
        echo "Kayıt başarıyla eklendi.";
        header("Location: admin/pages/add-product.php?id=$urun_id");
        exit;
    } else {
        echo "Ekleme sırasında hata oluştu.";
        exit;
    }
}
?>